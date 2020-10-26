<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call(PermissionsTableSeeder::class);
        $this->call(CollectionsTableSeeder::class);
        $this->call(TagsTableSeeder::class);
    }
}
