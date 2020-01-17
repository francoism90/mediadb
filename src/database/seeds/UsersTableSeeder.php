<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'email@example.com',
            'password' => bcrypt('secret'),
        ]);
    }
}
