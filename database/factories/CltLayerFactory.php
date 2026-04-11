<?php

namespace Database\Factories;

use App\Models\CltLayup;
use Illuminate\Database\Eloquent\Factories\Factory;

class CltLayerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'layup_id'    => CltLayup::factory(),
            'layer_order' => $this->faker->unique()->numberBetween(1, 20),
            'thickness'   => $this->faker->randomFloat(2, 5, 50),
            'width'       => $this->faker->randomFloat(2, 50, 200),
            'angle'       => $this->faker->randomElement([0, 45, 90, -45]),
        ];
    }
}
