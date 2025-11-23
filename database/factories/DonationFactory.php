<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DonationType;

class DonationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'donor_name' => $this->faker->name(),
            'donation_type_id' => DonationType::inRandomOrder()->first()?->id ?? DonationType::factory(),
            'amount' => $this->faker->randomElement([null, $this->faker->numberBetween(100, 5000)]),
            'items' => $this->faker->randomElement([null, 'Canned goods', 'Clothes', 'Blankets']),
            'donation_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['received','pending','distributed']),
        ];
    }
}