<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{

    public function run()
    {

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

       Role::create(['name' => 'admin']);
       Role::create(['name' => 'writer']);
       Role::create(['name' => 'viewer']);
    }
}

