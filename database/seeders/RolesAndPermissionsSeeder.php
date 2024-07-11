<?php

namespace Database\Seeders;

use App\Constants\Permissions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);

        // Create users and assign roles
        $adminUser = User::firstOrCreate([
            'email' => 'admin@admin.com'
        ], [
            'name' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $editorUser = User::firstOrCreate([
            'email' => 'editor@admin.com'
        ], [
            'name' => 'editor',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $adminUser->assignRole($adminRole,$editorRole,$viewerRole);
        $editorUser->assignRole($editorRole,$viewerRole);

        // Define permissions
        $permissions = [
            Permissions::CREATE_PRODUCT,
            Permissions::UPDATE_PRODUCT,
            Permissions::DELETE_PRODUCT,
            Permissions::VIEW_PRODUCT,
            Permissions::VIEW_ALL_PRODUCTS,

            Permissions::CREATE_ORDER,
            Permissions::UPDATE_ORDER,
            Permissions::DELETE_ORDER,
            Permissions::VIEW_ORDER,
            Permissions::VIEW_ALL_ORDERS,
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Define permissions by role
        $permissionsByRole = [
            'admin' => [Permissions::CREATE_PRODUCT, Permissions::UPDATE_PRODUCT, Permissions::DELETE_PRODUCT, Permissions::VIEW_PRODUCT, Permissions::VIEW_ALL_PRODUCTS,
                        Permissions::CREATE_ORDER, Permissions::UPDATE_ORDER, Permissions::DELETE_ORDER, Permissions::VIEW_ORDER, Permissions::VIEW_ALL_ORDERS,
        ],
            'editor' => [Permissions::CREATE_PRODUCT, Permissions::UPDATE_PRODUCT, Permissions::VIEW_PRODUCT, Permissions::VIEW_ALL_PRODUCTS
                        ,Permissions::CREATE_ORDER, Permissions::UPDATE_ORDER, Permissions::VIEW_ORDER, Permissions::VIEW_ALL_ORDERS
        ],
            'viewer' => [Permissions::VIEW_PRODUCT, Permissions::VIEW_ALL_PRODUCTS,
                            Permissions::VIEW_ORDER, Permissions::VIEW_ALL_ORDERS
                        ],
        ];

        // Assign permissions to roles
        foreach ($permissionsByRole as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();
            $role->syncPermissions($permissions);
        }
    }
}
