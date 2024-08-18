<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ];
        DB::table('users')->insert($param);
    }
}
