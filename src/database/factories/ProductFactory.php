<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->word,
            'brand_name' => $this->faker->company,
            'description' => $this->faker->sentence,
            'price' => $this->faker->numberBetween(500, 5000),
            'image' => 'products/default.png',
            'condition_id' => \App\Models\Condition::factory(),
        ];
    }
}
