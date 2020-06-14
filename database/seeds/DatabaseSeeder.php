<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call(PermissionsSeeder::class);
        $this->call(TagsTableSeeder::class);
    }
}
