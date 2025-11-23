<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DonationTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Cash', 'Goods', 'Medical Supplies', 'Clothing']),
            'description' => $this->faker->sentence(6),
        ];
    }
}