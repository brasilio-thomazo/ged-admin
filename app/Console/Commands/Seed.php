<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Models\Privilege;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;

class Seed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default users';

    private function createGroups(): array
    {
        $rw = ['group' => 'rw', 'user' => 'rw', 'client' => 'rw', 'app' => 'rw'];
        $r_ = ['group' => 'r', 'user' => 'r', 'client' => 'r', 'app' => 'r'];
        $rw['authorities'] = Privilege::makeAuthorities($rw);
        $r_['authorities'] = Privilege::makeAuthorities($r_);

        $groups = [];

        $group = Group::where('name', 'Administradores')->first();
        if ($group) {
            $groups['admin'] = $group->id;
        } else {
            print("Create groups [Administradores]\n");
            try {
                $group = new Group(['name' => 'Administradores']);
                $group->privilege()->save(new Privilege($rw));
                $groups['admin'] = $group->id;
                print("Group [Administradores] created");
            } catch (Exception $ex) {
                printf("Error [%s]\n", $ex->getMessage());
            }
        }

        $group = Group::where('name', 'Usuários')->first();
        if ($group) {
            $groups['user'] = $group->id;
        } else {
            print("Create groups [Usuários]\n");
            try {
                $group = Group::create(['name' => 'Usuários']);
                $group->privilege()->save(new Privilege($r_));
                $groups['user'] = $group->id;
                print("Group [Usuários] created");
            } catch (Exception $ex) {
                printf("Error [%s]\n", $ex->getMessage());
            }
        }
        return $groups;
    }

    private function createUsers($arr): array
    {
        $users = [];
        foreach ($arr as $item) {
            $user = User::where('username', $item['data']['username'])->first();
            if ($user) {
                printf("User [%s] exists\n", $user->username);
                $users[$user->username] = $user->id;
                continue;
            }
            printf("Creating user [%s] ... ", $item['data']['username']);
            try {
                $user = User::create($item['data']);
                $user->groups()->attach($item['groups']);
                $users[$user->username] = $user->id;
            } catch (Exception $ex) {
                printf("error [%s]\n", $ex->getMessage());
            }
        }
        return $users;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $groups = $this->createGroups();
        if (!count($groups)) {
            return;
        }

        $users = [
            [
                'data' => [
                    'name' => 'System',
                    'document' => '',
                    'role' => '',
                    'phone' => '',
                    'email' => 'root@localhost',
                    'username' => 'system',
                    'password' => env('SYSTEM_PASSWORD', 'system')
                ],
                'groups' => [$groups['admin']],
            ],
            [
                'data' => [
                    'name' => 'Administrador',
                    'document' => '',
                    'role' => '',
                    'phone' => '',
                    'email' => 'postmaster@localhost',
                    'username' => 'admin',
                    'password' => env('ADMIN_PASSWORD', 'admin')
                ],
                'groups' => [$groups['admin']],
            ]
        ];

        $this->createUsers($users);
    }
}
