<?php

namespace Database\Seeders;

use App\Models\EmployeePosition;
use Illuminate\Database\Seeder;

class EmployeePositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            'Driver',
            'Delivery Staff',
        ];

        foreach ($positions as $position) {
            EmployeePosition::firstOrCreate(
                ['name' => $position],
                [
                    'description' => fake()->paragraph(),
                    'is_active' => true,
                ]
            );
        }
    }
}
