<?php

namespace Database\Seeders;

use App\Models\DeliveryTeam;
use App\Models\DeliveryTeamMember;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use RuntimeException;

class DeliveryTeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $teams = DeliveryTeam::all();

        // Random drivers
        $drivers = Employee::whereHas('position', function ($query) {
            $query->where('name', 'Driver');
        })
            ->inRandomOrder()
            ->pluck('id');

        // Random delivery staff
        $deliveryStaff = Employee::whereHas('position', function ($query) {
            $query->where('name', 'Delivery Staff');
        })
            ->inRandomOrder()
            ->pluck('id');

        if (
            $drivers->count() < $teams->count() ||
            $deliveryStaff->count() < $teams->count()
        ) {
            throw new RuntimeException(
                'Not enough drivers or delivery staff for all teams.'
            );
        }

        foreach ($teams as $index => $team) {
            // Driver
            DeliveryTeamMember::create([
                'delivery_team_id' => $team->id,
                'employee_id' => $drivers[$index],
                'joined_at' => now(),
            ]);

            // Delivery Staff
            DeliveryTeamMember::create([
                'delivery_team_id' => $team->id,
                'employee_id' => $deliveryStaff[$index],
                'joined_at' => now(),
            ]);
        }
    }
}
