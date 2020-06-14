<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
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
        Permission::create(['name' => 'edit media']);
        Permission::create(['name' => 'delete media']);
        Permission::create(['name' => 'publish media']);
        Permission::create(['name' => 'unpublish media']);

        // Create roles and assign created permissions
        $roleAdmin = Role::create(['name' => 'super-admin']);
        $roleAdmin->givePermissionTo(Permission::all());

        $roleModerator = Role::create(['name' => 'moderator']);
        $roleModerator->givePermissionTo('publish media');
        $roleModerator->givePermissionTo('unpublish media');

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
