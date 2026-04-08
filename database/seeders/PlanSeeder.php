<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::insert([
            [
                'name'               => 'Free',
                'max_apps'           => 1,
                'max_ops_per_month'  => 1000,
                'price'              => 0,
                'is_active'          => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'name'               => 'Pro',
                'max_apps'           => 5,
                'max_ops_per_month'  => 50000,
                'price'              => 29.99,
                'is_active'          => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'name'               => 'Enterprise',
                'max_apps'           => 999,
                'max_ops_per_month'  => 999999,
                'price'              => 99.99,
                'is_active'          => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ]);
    }
}