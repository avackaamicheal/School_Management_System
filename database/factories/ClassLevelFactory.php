<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ClassLevel>
 */
class ClassLevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['JSS1', 'JSS2', 'JSS3', 'SS1', 'SS2', 'SS3']),

        ];
    }
}
