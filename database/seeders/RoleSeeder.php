<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeding the exact 10 roles specified in the requirements.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 1,  'code' => 'ADMIN',         'name' => 'Admin'],
            ['id' => 2,  'code' => 'CLIENT',        'name' => 'Client'],
            ['id' => 3,  'code' => 'CASHIER',       'name' => 'Cashier'],
            ['id' => 4,  'code' => 'AGENT_A',       'name' => 'Agent A'],
            ['id' => 5,  'code' => 'AGENT_C',       'name' => 'Agent C'],
            ['id' => 6,  'code' => 'RESP_A',        'name' => 'Responsable A'],
            ['id' => 7,  'code' => 'RESP_C',        'name' => 'Responsable C'],
            ['id' => 8,  'code' => 'TRAVELER',      'name' => 'Traveler'],
            ['id' => 9,  'code' => 'DELIVERY',      'name' => 'Delivery'],
            ['id' => 10, 'code' => 'VERIFIER',      'name' => 'Verifier'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['id' => $role['id']],
                $role
            );
        }
    }
}
