<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert(
            [
                [
                    'name' => 'Admin GTN',
                    'email' => 'admin@example.com',
                    'password' => Hash::make('12345678'),
                    'isAdmin' => 1,
                    'NIP' => '',
                    'created_at' => Carbon::now()
                ],
                [
                    'name' => 'Sales GTN',
                    'email' => 'sales@example.com',
                    'password' => Hash::make('12345678'),
                    'isAdmin' => 0,
                    'NIP' => 'Sales-000001',
                    'created_at' => Carbon::now()
                ],
            ]
        );
    }
}
