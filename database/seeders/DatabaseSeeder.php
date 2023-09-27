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

        $admin = new Group(['name' => 'Administradores']);
        $admin->privilege()->save(new Privilege($rw));

        $users = new Group(['name' => 'Usuários']);
        $users->privilege()->save(new Privilege($r_));

        User::create([
            'name' => 'System',
            'document' => '',
            'role' => '',
            'phone' => '',
            'email' => 'root@localhost',
            'username' => 'system',
            'password' => 'system'
        ])->groups()->attach([$admin->id]);

        User::create([
            'name' => 'Administrador',
            'document' => '',
            'role' => '',
            'phone' => '',
            'email' => 'postmaster@localhost',
            'username' => 'admin',
            'password' => 'admin'
        ])->groups()->attach([$admin->id]);
    }
}
