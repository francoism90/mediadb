<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call(PermissionsTableSeeder::class);
        $this->call(TagsTableSeeder::class);
        $this->call(ChannelsTableSeeder::class);
        $this->call(PlaylistsTableSeeder::class);
    }
}
