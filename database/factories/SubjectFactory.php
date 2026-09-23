<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Mathematics',
                'English Language',
                'Basic Science',
                'Social Studies',
                'Civic Education',
                'Computer Studies',
                'Physical and Health Education',
                'Creative Arts',
            ]),
            'code' => strtoupper($this->faker->unique()->lexify('???')),
        ];
    }
}
