<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppRequest;
use App\Http\Requests\UpdateAppRequest;
use App\Models\App;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AppController extends Controller
{
    private array $vars = [
        'ID', 'APP', 'URL',
        'DB_DRIVER', 'DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS',
        'DOMAIN', 'HTTP_PORT',
        'REDIS_HOST', 'REDIS_PORT', 'REDIS_PASS',
        'MEMCACHED_HOST',
        'CACHE_DRIVER', 'SESSION_DRIVER',
    ];

    private function getRegex(): array
    {
        return array_map(fn ($v) => sprintf('/VAR_%s/', $v), $this->vars);
    }

    private function getReplace(App $app, Request $request): array
    {
        return [
            $app->id, $app->application, sprintf('http://%s:%d', $app->domain, $app->http_port),
            $app->db_type, $app->db_host, $app->db_port, $app->db_name,
            $request->db_user, $request->db_pass,
            $app->domain, $app->http_port,
            $app->redis_host, $app->redis_port, $request->redis_pass,
            $app->memcached_host,
            $app->cache_driver, $app->session_driver
        ];
    }

    private function writeConfigs(App $app, Request $request)
    {
        $fpm_data = file_get_contents('http/fpm.ini');
        $vhost_data = file_get_contents('http/nginx.conf');
        $fpm = sprintf("fpm/%s.conf", $app->id);
        $vhost = sprintf("vhost/%s.conf", $app->id);
        Storage::put($fpm, preg_replace($this->getRegex(), $this->getReplace($app, $request), $fpm_data));
        Storage::put($vhost, preg_replace($this->getRegex(), $this->getReplace($app, $request), $vhost_data));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $builder = App::with('client');
        return response($builder->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppRequest $request)
    {
        $save = App::create($request->except(['db_pass', 'db_user', 'redis_pass']));
        $app = App::with('client')->findOrFail($save->id);
        $this->writeConfigs($app, $request);

        return response($app, 201);
        //return response([], 400);
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
        $update = array_filter($request->all(), function ($value, $key) use ($app) {
            return $value != $app->$key;
        }, ARRAY_FILTER_USE_BOTH);
        if (count($update)) {
            $app->update($update);
        }
        return response($update);
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
