<?php

namespace Database\Seeders;

use App\Models\DeliveryTeam;
use Illuminate\Database\Seeder;

class DeliveryTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryTeam::factory()->count(5)->create();
    }
}
