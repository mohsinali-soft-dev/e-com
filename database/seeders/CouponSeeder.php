<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::updateOrCreate(['code' => 'FIXED100'], [
            'type' => 'fixed',
            'value' => 100,
            'minimum_order' => 500,
            'maximum_discount' => null,
            'expires_at' => now()->addYear(),
            'is_active' => true,
        ]);

        Coupon::updateOrCreate(['code' => 'SAVE10'], [
            'type' => 'percentage',
            'value' => 10,
            'minimum_order' => 1000,
            'maximum_discount' => 500,
            'expires_at' => now()->addYear(),
            'is_active' => true,
        ]);
    }
}
