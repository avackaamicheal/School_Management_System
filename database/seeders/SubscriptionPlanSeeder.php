<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $plans = [
            [
                'name'         => 'Starter',
                'max_students' => 100,
                'price'        => 25000,
                'duration_days'=> 90,
                'is_active'    => true,
            ],
            [
                'name'         => 'Growth',
                'max_students' => 300,
                'price'        => 50000,
                'duration_days'=> 90,
                'is_active'    => true,
            ],
            [
                'name'         => 'Academy',
                'max_students' => 600,
                'price'        => 90000,
                'duration_days'=> 90,
                'is_active'    => true,
            ],
            [
                'name'         => 'Enterprise',
                'max_students' => 999999,
                'price'        => 150000,
                'duration_days'=> 90,
                'is_active'    => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
