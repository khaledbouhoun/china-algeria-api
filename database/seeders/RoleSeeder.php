<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Simple role definitions.
     * Frontend uses role_id to control UI.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'code' => 'CLIENT', 'name' => 'Client'],
            ['id' => 2, 'code' => 'EMPLOYEE', 'name' => 'Employee'],
            ['id' => 3, 'code' => 'RESPONSABLE_ZONE_A', 'name' => 'Zone A Manager'],
            ['id' => 4, 'code' => 'RESPONSABLE_ZONE_B', 'name' => 'Zone B Manager'],
            ['id' => 5, 'code' => 'DELIVERY', 'name' => 'Delivery Person'],
            ['id' => 6, 'code' => 'TRAVELER', 'name' => 'Traveler'],
            ['id' => 7, 'code' => 'ADMIN', 'name' => 'Administrator'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['id' => $role['id']],
                $role
            );
        }
    }
}
