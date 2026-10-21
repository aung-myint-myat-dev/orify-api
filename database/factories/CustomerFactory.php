<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'customer_code' => 'CUS-' . strtoupper(fake()->unique()->bothify('#####')),
            'phone_number_1' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
