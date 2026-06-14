<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Simple zone definitions for data filtering.
     */
    public function run(): void
    {
        $zones = [
            ['code' => 'ZONE_A', 'name' => 'Zone A'],
            ['code' => 'ZONE_B', 'name' => 'Zone B'],
            ['code' => 'ZONE_C', 'name' => 'Zone C'],
            ['code' => 'ALGIERS', 'name' => 'Algiers'],
            ['code' => 'ORAN', 'name' => 'Oran'],
        ];

        foreach ($zones as $zone) {
            Zone::updateOrCreate(
                ['code' => $zone['code']],
                $zone
            );
        }
    }
}
