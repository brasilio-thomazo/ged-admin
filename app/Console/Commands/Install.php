<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'install app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $buffer = "";
        $this->call("config:cache", [], $buffer);
        print $buffer;

        $this->createDb();

        $this->call("migrate", ['--force' => ''], $buffer);
        print $buffer;


        $rw = ['group' => 'rw', 'user' => 'rw', 'client' => 'rw', 'app' => 'rw'];
        $r = ['group' => 'r', 'user' => 'r', 'client' => 'r', 'app' => 'r'];
        $in_groups = [
            ['name' => 'administrador', 'is_admin' => true, 'privilege' => $rw, 'authorities' => Group::makeAuthorities($rw)],
            ['name' => 'usuário administrador', 'privilege' => $rw, 'authorities' => Group::makeAuthorities($rw)],
            ['name' => 'usuário', 'privilege' => $r, 'authorities' => Group::makeAuthorities($r)],
        ];
        $groups = $this->createGroups($in_groups);

        $in_users = [
            [
                'data' => [
                    'name' => 'System',
                    'document' => '',
                    'role' => '',
                    'phone' => '',
                    'email' => 'root@localhost',
                    'username' => 'system',
                    'password' => config('install.system.password')
                ],
                'group' => [$groups['administrador']],
            ],
            [
                'data' => [
                    'name' => 'Administrador',
                    'document' => '',
                    'role' => '',
                    'phone' => '',
                    'email' => 'postmaster@localhost',
                    'username' => 'admin',
                    'password' => config('install.admin.password')
                ],
                'group' => [$groups['administrador']],
            ]
        ];

        $this->createUsers($in_users);
    }


    private function createDb()
    {

        $connection = env('DB_CONNECTION', 'pgsql');
        $section = "database.connections." . $connection;
        $database = config($section . ".database");
        $username = config($section . ".username");
        $password = config($section . ".password");

        $name = $database;
        config([$database => null]);
        $querys = [];

        if ($connection == 'pgsql') {
            $querys['create_user'] = "CREATE USER {$username} WITH PASSWORD '$password'";
            $querys['create_db'] = "CREATE DATABASE $name OWNER $username";
            $querys['grant'] = "GRANT ALL PRIVILEGES ON DATABASE $name TO $username";
        } else {
            $querys['create_user'] = "CREATE USER '$username'@'%' IDENTIFIED BY '$password'";
            $querys['create_db'] = "CREATE DATABASE $name";
            $querys['grant'] = "GRANT PRIVILEGE ON $name TO '$name'@'%s'";
        }

        foreach ($querys as $q) {
            $show = preg_replace("/('$password')$/", "'******'", $q);
            printf("RUN [%s] ... ", $show);
            try {
                DB::connection($connection . ".super")->statement($q);
                print("[OK]\n");
            } catch (Exception $ex) {
                printf("[ERROR]\n[%s]\n", $ex->getMessage());
            }
        }

        config([$database => $name]);
    }


    private function createGroups(array $in)
    {
        $out = [];
        foreach ($in as $item) {
            printf("Creating group [%s] ... ", $item['name']);
            $data = Group::where('name', $item['name'])->first();
            if ($data) {
                printf("[exists]\n", $item['name']);
                $out[$item['name']] = $data->id;
                continue;
            }
            try {
                $data = Group::create($item);
                $out[$item['name']] = $data->id;
                print("[created]\n");
            } catch (Exception $ex) {
                printf("[error]\n---\n%s\n---\n", $ex->getMessage());
            }
        }
        return $out;
    }

    private function createUsers(array $in)
    {
        $out = [];
        foreach ($in as $item) {
            $subitem = $item['data'];
            printf("Creating user [%s] with password [%s] ... ", $subitem['username'], $subitem['password']);
            $data = User::where('username', $subitem['username'])->first();
            if ($data) {
                printf("[exists]\n");
                $out[$subitem['username']] = $data->id;
                continue;
            }
            try {
                $data = User::create($subitem);
                $data->groups()->attach($item['group']);
                $out[$subitem['username']] = $data->id;
                print("[created]\n");
            } catch (Exception $ex) {
                printf("[error]\n---\n%s\n---\n", $ex->getMessage());
            }
        }
        return $out;
    }
}
