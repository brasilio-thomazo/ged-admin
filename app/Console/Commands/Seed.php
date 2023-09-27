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

    private function privileges(): array
    {
        $privileges = [];
        $privileges['rw'] = ['group' => 'rw', 'user' => 'rw', 'client' => 'rw', 'app' => 'rw'];
        $privileges['r'] = ['group' => 'r', 'user' => 'r', 'client' => 'r', 'app' => 'r'];
        $privileges['rw']['authorities'] = Privilege::makeAuthorities($privileges['rw']);
        $privileges['r']['authorities'] = Privilege::makeAuthorities($privileges['r']);
        return $privileges;
    }

    private function createGroups(array $arr): array
    {

        $groups = [];
        foreach ($arr as $g) {
            $group = Group::where('name', $g['group']['name'])->first();
            if ($group) {
                printf("Group [%s] exists\n", $g['group']['name']);
                $groups[$g['group']['name']] = $group->id;
                continue;
            }
            printf("Creating group [%s]\n", $g['group']['name']);
            try {
                $group = Group::create($g['group']);
                $group->privilege()->save(new Privilege($g['privileges']));
                $groups[$g['group']['name']] = $group->id;
                print("created\n");
            } catch (Exception $ex) {
                printf("Error [%s]\n", $ex->getMessage());
            }
        }
        return $groups;
    }

    private function createUsers(array $arr): array
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
                print("created\n");
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
        $privileges = $this->privileges();
        $data_groups = [
            [
                'group' => ['name' => 'Administradores'],
                'privileges' => $privileges['rw'],
            ],
            [
                'group' => ['name' => 'Usuários'],
                'privileges' => $privileges['r'],
            ]
        ];

        $groups = $this->createGroups($data_groups);

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
                'groups' => [$groups['Administradores']],
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
                'groups' => [$groups['Administradores']],
            ]
        ];

        $this->createUsers($users);
    }
}
