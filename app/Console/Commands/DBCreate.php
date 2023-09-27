<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DBCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:db-create {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create databse';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $connection = env('DB_CONNECTION', 'pgsql');
        $root = $connection . '_root';
        $section = sprintf("database.connections.%s", $connection);
        $database = config(sprintf("%s.database", $section));
        $username = config(sprintf("%s.username", $section));
        $password = config(sprintf("%s.password", $section));

        $name = $this->argument('name') ?: $database;
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
                DB::connection($root)->statement($q);
                print("[OK]\n");
            } catch (Exception $ex) {
                printf("[ERROR]\n[%s]\n", $ex->getMessage());
            }
        }

        config([$database => $name]);
    }
}
