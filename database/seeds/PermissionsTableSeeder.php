<?php

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
        Permission::create(['name' => 'create channels']);
        Permission::create(['name' => 'delete channels']);
        Permission::create(['name' => 'publish channels']);
        Permission::create(['name' => 'unpublish channels']);

        Permission::create(['name' => 'edit media']);
        Permission::create(['name' => 'delete media']);
        Permission::create(['name' => 'publish media']);
        Permission::create(['name' => 'unpublish media']);

        Permission::create(['name' => 'edit playlists']);
        Permission::create(['name' => 'delete playlists']);
        Permission::create(['name' => 'publish playlists']);
        Permission::create(['name' => 'unpublish playlists']);

        // Create admin role and assign created permissions
        $roleAdmin = Role::create(['name' => 'super-admin']);
        $roleAdmin->givePermissionTo(Permission::all());

        // Create moderator role and assign created permissions
        $roleModerator = Role::create(['name' => 'moderator']);

        $roleModerator->givePermissionTo('publish media');
        $roleModerator->givePermissionTo('unpublish media');

        $roleModerator->givePermissionTo('publish playlists');
        $roleModerator->givePermissionTo('unpublish playlists');

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
