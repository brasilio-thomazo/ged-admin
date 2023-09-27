<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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
        $calls = [
            ['call' => 'migrate', 'params' => ['--force' => null]],
            ['call' => 'app:db-create', 'params' => []],
            ['call' => 'app:seed', 'params' => []],
            ['call' => 'config:cache', 'params' => []]
        ];

        foreach ($calls as $c) {
            try {
                printf("%s\n", $c['call']);
                $this->call($c['call'], $c['params'], $buffer);
                print($buffer);
            } catch (Exception $ex) {
                printf("error [%s]\n", $ex->getMessage());
            }
        }
    }
}
