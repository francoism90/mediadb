<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Administrator',
            'slug' => 'administrator',
            'email' => 'email@example.com',
            'password' => Hash::make('secret'),
        ]);
    }
}
