<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Condition;

class ConditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Condition::class;

    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['良好', '目立った傷や汚れなし', '屋や傷や汚れあり', '状態が悪い']),
        ];
    }
}
