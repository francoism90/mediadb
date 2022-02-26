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
        $this->callOnce([
            PermissionsTableSeeder::class,
            TagsTableSeeder::class,
        ]);
    }
}
