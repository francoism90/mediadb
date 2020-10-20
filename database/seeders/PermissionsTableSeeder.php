<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::create(['name' => 'create collections']);
        Permission::create(['name' => 'edit collections']);
        Permission::create(['name' => 'delete collections']);
        Permission::create(['name' => 'publish collections']);
        Permission::create(['name' => 'unpublish collections']);

        Permission::create(['name' => 'create tags']);
        Permission::create(['name' => 'edit tags']);
        Permission::create(['name' => 'delete tags']);
        Permission::create(['name' => 'publish tags']);
        Permission::create(['name' => 'unpublish tags']);

        Permission::create(['name' => 'create videos']);
        Permission::create(['name' => 'edit videos']);
        Permission::create(['name' => 'delete videos']);
        Permission::create(['name' => 'publish videos']);
        Permission::create(['name' => 'unpublish videos']);

        // Create admin role and assign created permissions
        $roleAdmin = Role::create(['name' => 'super-admin']);
        $roleAdmin->givePermissionTo(Permission::all());

        // Create moderator role and assign created permissions
        $roleModerator = Role::create(['name' => 'moderator']);

        $roleModerator->givePermissionTo('edit collections');
        $roleModerator->givePermissionTo('unpublish collections');

        $roleModerator->givePermissionTo('edit videos');
        $roleModerator->givePermissionTo('unpublish videos');

        // Create the admin user
        $user = User::create([
            'name' => 'Administrator',
            'slug' => 'administrator',
            'email' => 'email@example.com',
            'password' => Hash::make('secret'),
        ]);

        $user->assignRole($roleAdmin);
    }
}
