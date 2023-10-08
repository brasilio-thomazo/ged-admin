<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppRequest;
use App\Http\Requests\UpdateAppRequest;
use App\Models\App;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Yaml\Yaml;

class AppController extends Controller
{
    private string $home;
    private string $secret;
    private string $config_map;

    private array $deployments = [
        'nginx' => '',
        'grpc' => '',
        'fpm' => '',
    ];

    private array $services = [
        'nginx' => '',
        'grpc' => '',
        'fpm' => '',
    ];

    private array $servicePorts = [
        'nginx' => [
            'port' => [80],
            'target' => [80],
            'node' => null
        ],
        'grpc' => [
            'port' => [50051],
            'target' => [50051],
            'node' => []
        ],
        'fpm' => [
            'port' => [9000],
            'target' => [9000],
            'node' => null
        ],
    ];


    private int $portMin = 30000;
    private int $portMax = 32767;
    private int $grpcPort = 50051;

    public function __construct()
    {

        $this->home = preg_replace("/^\/(.+)\/public$/", "$1", $_SERVER['DOCUMENT_ROOT']);
        $base = sprintf("/%s/resources/kubernetes/client", $this->home);
        $this->secret = $base . '/secret.yaml';
        $this->config_map = $base . '/config-map.yaml';
        $this->deployments['nginx'] = $base . '/deployment-nginx.yaml';
        $this->deployments['grpc'] = $base . '/deployment-grpc.yaml';
        $this->deployments['fpm'] = $base . '/deployment-fpm.yaml';

        $this->services['nginx'] = $base . '/service-nginx.yaml';
        $this->services['grpc'] = $base . '/service-grpc.yaml';
        $this->services['fpm'] = $base . '/service-fpm.yaml';
        $this->grpcPort = config('grpc.port', 50051);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $builder = App::with('client');
        return response($builder->get());
    }

    private function generatePort(): int
    {
        $ports = App::get(['grpc_port'])->toArray();
        do {
            $port = rand($this->portMin, $this->portMax);
        } while (in_array($port, $ports));
        return $port;
    }

    private function setContainer(array &$containers, $config, $secret = null, $name = null, $image = null)
    {
        foreach ($containers as $k => $container) {
            if (isset($name) && isset($image)) {
                if ($container['image'] == $image) {
                    $containers[$k]['name'] = $name;
                }
            }
            $envs = $container['envFrom'];
            foreach ($envs as $k2 => $env) {
                if (isset($env['configMapRef']) && $env['configMapRef']['name'] == 'client-config') {
                    $containers[$k]['envFrom'][$k2]['configMapRef']['name'] = $config;
                    continue;
                }
                if (isset($secret) && isset($env['secretRef']) && $env['secretRef']['name'] == 'client-secret') {
                    $containers[$k]['envFrom'][$k2]['secretRef']['name'] = $secret;
                }
            }
        }
    }

    private function setContainerPort(array &$containers, array $images, array $ports)
    {
        $j = 0;
        foreach ($containers as $i => $container) {
            if ($container['image'] != $images[$i]) continue;
            if (!isset($container['ports'])) continue;
            $container_ports = $container['ports'];
            for ($k = 0; $k < count($container_ports); $k++) {
                $containers[$i]['ports'][$k]['containerPort'] = $ports[$i][$j];
            }
            $j++;
        }
    }

    private function setPorts(array &$yaml, array $ports, array $targetPorts, array $nodePorts = null)
    {
        $j = 0;
        for ($i = 0; $i < count($yaml); $i++) {
            if (isset($yaml[$i]['port'])) {
                $yaml[$i]['port'] = $ports[$i];
            }
            if (isset($yaml[$i]['containerPort'])) {
                $yaml[$i]['containerPort'] = $ports[$i];
            }
            if (isset($yaml[$i]['targetPort'])) {
                $yaml[$i]['targetPort'] = $targetPorts[$i];
            }
            if (isset($yaml[$i]['nodePort']) && $nodePorts) {
                $yaml[$i]['nodePort'] = $nodePorts[$i];
            }
        }
    }

    private function makeDeplyments(array $names, array &$data)
    {
        $data[] = $this->makeFpmDeplyment($names);
        $data[] = $this->makeNginxDeplyment($names);
        $data[] = $this->makeGrpcDeplyment($names);
    }

    private function makeDeplyment(array &$yaml, string $key, array $names, $image)
    {
        $yaml['metadata']['name'] = $names[$key];
        $yaml['spec']['selector']['matchLabels']['app'] = $names[$key];
        $yaml['spec']['template']['metadata']['labels']['app'] = $names[$key];

        if (isset($yaml['spec']['template']['spec']['initContainers'])) {
            $this->setContainer($yaml['spec']['template']['spec']['initContainers'], $names['config'], $names['secret']);
        }
        if (isset($yaml['spec']['template']['spec']['containers'])) {
            $this->setContainer($yaml['spec']['template']['spec']['containers'], $names['config'], $names['secret'], $names[$key], $image);
        }
        return $yaml;
    }

    private function makeFpmDeplyment(array $names): string
    {
        $yaml = Yaml::parse(file_get_contents($this->deployments['fpm']));
        $this->makeDeplyment($yaml, 'fpm', $names, 'devoptimus/ged-client-fpm');
        foreach ($yaml['spec']['volumes'] as $k => $volume) {
            if ($volume['name'] == 'storage') {
                $yaml['spec']['volumes'][$k]['persistentVolumeClaim']['claimName'] = config('k8s.pvc.images');
            }
        }
        return Yaml::dump($yaml, 10, 2);
    }

    private function makeNginxDeplyment(array $names): string
    {
        $yaml = Yaml::parse(file_get_contents($this->deployments['nginx']));
        $this->makeDeplyment($yaml, 'nginx', $names, 'devoptimus/ged-client-nginx');
        return Yaml::dump($yaml, 10, 2);
    }

    private function makeGrpcDeplyment(array $names): string
    {
        $yaml = Yaml::parse(file_get_contents($this->deployments['grpc']));
        $this->makeDeplyment($yaml, 'grpc', $names, 'devoptimus/ged-client-grpc-server');
        foreach ($yaml['spec']['volumes'] as $k => $volume) {
            if ($volume['name'] == 'storage') {
                $yaml['spec']['volumes'][$k]['persistentVolumeClaim']['claimName'] = config('k8s.pvc.images');
            }
        }
        return Yaml::dump($yaml, 10, 2);
    }

    private function makeConfigMap(Request $request, App $app, array $names, array &$data)
    {
        $yaml = Yaml::parse(file_get_contents($this->config_map));
        $custom = $request->get("custom", false);

        $section = "database.connections." . config("database.default");
        $writer_host = config($section . ".write.host");
        $writer_port = config($section . ".write.port");
        $reader_host = config($section . ".read.host");
        $reader_port = config($section . ".read.port");

        $redis_host = config("database.redis.default.host");
        $redis_port = config("database.redis.default.port");

        $yaml['metadata']['name'] = $names['config'];

        $yaml['data']['DB_CONNECTION'] = $custom ? $request->db_type : config('database.default', 'pgsql');
        $yaml['data']['DB_DATABASE'] = $request->db_name;
        $yaml['data']['DB_WRITER_HOST'] = $custom ? $request->db_writer_host : $writer_host;
        $yaml['data']['DB_WRITER_PORT'] = $custom ? $request->db_writer_port : $writer_port;
        $yaml['data']['DB_READER_HOST'] = $custom ? $request->db_reader_host : $reader_host;
        $yaml['data']['DB_READER_PORT'] = $custom ? $request->db_reader_port : $reader_port;
        $yaml['data']['SESSION_DRIVER'] = $custom ? $request->session_driver : config("session.driver", 'file');
        $yaml['data']['CACHE_DRIVER'] = $custom ? $request->session_driver : config("cache.default", "file");
        $yaml['data']['FPM_HOST'] = $names['fpm'] . ':9000';
        $yaml['data']['GRPC_HOST'] = $names['grpc'];
        $yaml['data']['GRPC_PORT'] = strval($this->grpcPort);

        $yaml['data']['REDIS_HOST'] = $custom ? $request->redis_host : $redis_host;
        $yaml['data']['REDIS_PORT'] = $custom ? $request->redis_port : $redis_port;
        $yaml['data']['UPLOAD_IMAGE'] = $this->home . "/" . $app->path;
        $data[] = Yaml::dump($yaml, 10, 2, Yaml::DUMP_NUMERIC_KEY_AS_STRING);
    }

    private function makeSecret(Request $request, array $names, array &$data)
    {
        $yaml = Yaml::parse(file_get_contents($this->secret));
        $custom = $request->get('custom', false);
        $key = sprintf('base64:%s', base64_encode(openssl_random_pseudo_bytes(32)));
        $section = "database.connections." . config("database.default");
        $super_username = config($section . ".super.username");
        $super_password = config($section . ".super.password");
        $username = config($section . ".username");
        $password = config($section . ".password");

        $redis_username = config("database.redis.default.username", "null");
        $redis_password = config("database.redis.default.password", "null");

        $yaml['metadata']['name'] = $names['secret'];

        $yaml['data']['APP_KEY'] = base64_encode($key);

        $yaml['data']['SYSTEM_PASSWORD'] = base64_encode(config('install.system.password'));
        $yaml['data']['ADMIN_PASSWORD'] = base64_encode(config('install.admin.password'));

        $yaml['data']['DB_SUPER_USERNAME'] = base64_encode($custom ? $request->super_user : $super_username);
        $yaml['data']['DB_SUPER_PASSWORD'] = base64_encode($custom ? $request->super_pass : $super_password);
        $yaml['data']['DB_USERNAME'] = base64_encode($custom ? $request->db_user : $username);
        $yaml['data']['DB_PASSWORD'] = base64_encode($custom ? $request->db_pass_super : $password);

        $yaml['data']['REDIS_USERNAME'] = base64_encode($custom ? $request->redis_user : $redis_username);
        $yaml['data']['REDIS_PASSWORD'] = base64_encode($custom ? $request->redis_pass : $redis_password);

        $data[] = Yaml::dump($yaml, 10, 2);
    }

    private function makeServices(array $names, array &$data)
    {
        foreach ($this->services as $k => $service) {
            $yaml = Yaml::parse(file_get_contents($service));
            $this->makeService(
                $yaml,
                $names[$k],
                $this->servicePorts[$k]['port'],
                $this->servicePorts[$k]['target'],
                $this->servicePorts[$k]['node']
            );
            $data[] = Yaml::dump($yaml, 10, 2);
        }
    }

    private function makeService(array &$yaml, $name, array $ports, array $targetPorts, array $nodePorts = null)
    {
        $yaml['metadata']['name'] = $name;
        $yaml['spec']['selector']['app'] = $name;
        if (isset($nodePorts)) {
            $this->setPorts($yaml['spec']['ports'], $ports, $targetPorts, $nodePorts);
        } else {
            $this->setPorts($yaml, $ports, $targetPorts);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppRequest $request)
    {
        $useCustom = $request->get('use_custom', false);
        $useDomain = $request->get('use_domain', false);
        $domain = env('CLIENT_DOMAIN', 'localhost');
        $nodePort = $this->generatePort();

        $this->servicePorts['grpc']['node'] = [$nodePort];

        $save = [
            'client_id' => $request->get('client_id'),
            'application' => 0,
            'path' => $request->get('path'),
            'subdomain' => $request->get('path') . '.' . $domain,
            'grpc_port' =>  $nodePort,
            'use_domain' => $useDomain,
            'use_custom' => $useCustom,
            'db_name' => $request->get('db_name'),
        ];

        if ($useDomain) {
            $save['domain'] = $request->get('domain');
        }

        if ($useCustom) {
            $save['redis_host'] = $request->get('redis_host');
            $save['redis_port'] = $request->get('redis_port');
            $save['memcached_host'] = $request->get('memcached_host');
            $save['db_type'] = $request->get('db_type');
            $save['db_host'] = $request->get('db_host');
            $save['db_port'] = $request->get('db_port');
            $save['cache_driver'] = $request->get('cache_driver');
            $save['session_driver'] = $request->get('session_driver');
        }


        $app = App::create($save);
        $app->client;
        $name = $request->get('path', $app->id);
        $names = [
            'secret' => "ged-client-secret-" . $name,
            'config' => "ged-client-config-" . $name,
            'nginx' => "ged-client-nginx-" . $name,
            'grpc' => "ged-client-grpc-" . $name,
            'fpm' => "ged-client-fpm-" . $name,
        ];

        $data = array();
        $crlf = "\n---\n\n";
        $this->makeServices($names, $data);
        $this->makeSecret($request, $names, $data);
        $this->makeConfigMap($request, $app, $names, $data);
        $this->makeDeplyments($names, $data);
        $content = implode($crlf, $data);
        $filename = "k8s-" . $app->id . ".yaml";
        Storage::put($filename, $content);
        return response($app, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(App $app)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppRequest $request, App $app)
    {

        // dd(config("database.connections"));
        $useCustom = $request->get('use_custom', false);
        $useDomain = $request->get('use_domain', false);
        $nodePort = $app->grpc_port;

        $this->servicePorts['grpc']['node'] = [$nodePort];

        $save = [
            'client_id' => $request->get('client_id'),
            'grpc_port' =>  $nodePort,
            'use_domain' => $useDomain,
            'use_custom' => $useCustom,
            'db_name' => $request->get('db_name'),
        ];

        if ($useDomain) {
            $save['domain'] = $request->get('domain');
        }

        if ($useCustom) {
            $save['redis_host'] = $request->get('redis_host');
            $save['redis_port'] = $request->get('redis_port');
            $save['memcached_host'] = $request->get('memcached_host');
            $save['db_type'] = $request->get('db_type');
            $save['db_host'] = $request->get('db_host');
            $save['db_port'] = $request->get('db_port');
            $save['cache_driver'] = $request->get('cache_driver');
            $save['session_driver'] = $request->get('session_driver');
        }


        $app->update($save);
        $app->client;
        $names = [
            'secret' => "client-secret-" . $app->path,
            'config' => "client-config-" . $app->path,
            'nginx' => "client-nginx-" . $app->path,
            'grpc' => "client-grpc-" . $app->path,
            'fpm' => "client-fpm-" . $app->path,
        ];

        $data = array();
        $crlf = "\n---\n\n";
        $this->makeServices($names, $data);
        $this->makeSecret($request, $names, $data);
        $this->makeConfigMap($request, $app, $names, $data);
        $this->makeDeplyments($names, $data);
        $content = implode($crlf, $data);
        $filename = "k8s-" . $app->id . ".yaml";
        Storage::put($filename, $content);
        return response($app, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(App $app)
    {
        //
    }

    public function start(App $app)
    {
        $response = Http::post("http://localhost:8080/reload");
        return response($response->json());
    }

    public function started(App $app)
    {
        $tz = new DateTimeZone("America/Sao_Paulo");
        $app->update(['started_at' => now($tz)]);
        return response($app);
    }

    public function installed(App $app)
    {
        $tz = new DateTimeZone("America/Sao_Paulo");
        $app->update(['installed_at' => now($tz)]);
        return response($app);
    }
}
