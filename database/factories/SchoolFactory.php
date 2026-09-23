<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Demo School Academy',
            'slug' => 'demo-school-academy',
            'email' => 'admin@demoschool.test',
            'phone_number' => '08000000000',
            'address' => '1 Demo Street, Lagos',
            'principal_name' => 'Mrs. Demo Principal',
            'is_active' => true,
            'approval_status' => 'approved',
            'approved_at' => now(),
            'is_subscribed' => true,
            'subscription_expires_at' => now()->addYears(5),
        ];
    }
}
