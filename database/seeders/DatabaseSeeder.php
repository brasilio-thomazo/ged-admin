<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Group;
use App\Models\Privilege;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $rw = ['group' => 'rw', 'user' => 'rw', 'client' => 'rw', 'app' => 'rw'];
        $r_ = ['group' => 'r', 'user' => 'r', 'client' => 'r', 'app' => 'r'];

        $rw['authorities'] = Privilege::makeAuthorities($rw);
        $r_['authorities'] = Privilege::makeAuthorities($r_);




        $admins = Group::factory()->create(['name' => 'Administradores']);
        $admins->privilege()->save(new Privilege($rw));


        $users = Group::factory()->create(['name' => 'Usuários']);
        $users->privilege()->save(new Privilege($r_));

        $system = User::factory()->create([
            'name' => 'System',
            'document' => '',
            'role' => '',
            'phone' => '',
            'email' => 'root@localhost',
            'username' => 'system',
            'password' => 'system'
        ]);

        $system->groups()->attach([$admins->id]);

        $admin = User::factory()->create([
            'name' => 'Administrador',
            'document' => '',
            'role' => '',
            'phone' => '',
            'email' => 'postmaster@localhost',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $admin->groups()->attach([$admins->id]);
    }
}
