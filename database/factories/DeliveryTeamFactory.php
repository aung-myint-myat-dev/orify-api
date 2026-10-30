<?php

namespace Database\Factories;

use App\Models\DeliveryTeam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryTeam>
 */
class DeliveryTeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_name' => fake()->name(),
            'team_code' => 'DT-'.fake()->unique()->bothify('####'),
            'description' => fake()->paragraphs(2, true),
        ];
    }
}
