<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourceTownSeeder extends Seeder
{
    /**
     * Seed the application's source towns.
     */
    public function run(): void
    {
        $now = now();

        $towns = [
            [
                'town_name' => 'တောင်ကြီး',
                'type' => 'source',
                'city_code' => 'TGI',
                'sort_order' => 1,
            ],
            [
                'town_name' => 'လားရှိုး',
                'type' => 'source',
                'city_code' => 'LSO',
                'sort_order' => 2,
            ],
            [
                'town_name' => 'တာချီလိတ်',
                'type' => 'source',
                'city_code' => 'TCL',
                'sort_order' => 3,
            ],
        ];

        foreach ($towns as $town) {
            DB::table('towns')->updateOrInsert(
                [
                    'town_name' => $town['town_name'],
                    'type' => $town['type'],
                ],
                [
                    'city_code' => $town['city_code'],
                    'sort_order' => $town['sort_order'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
