<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaketPenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('selling_packets')->insert(
            [
                [
                    'packet_name' => 'Home 1',
                    'packet_price' => 100000,
                ],
                [
                    'packet_name' => 'Home 2',
                    'packet_price' => 150000,
                ],
                [
                    'packet_name' => 'Home 3',
                    'packet_price' => 200000,
                ],
            ]
        );
    }
}
