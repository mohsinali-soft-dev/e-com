<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'piece', 'short_name' => 'pc', 'type' => 'piece', 'decimal_places' => 0],
            ['name' => 'kg', 'short_name' => 'kg', 'type' => 'weight', 'decimal_places' => 3],
            ['name' => 'gram', 'short_name' => 'g', 'type' => 'weight', 'decimal_places' => 3],
            ['name' => 'liter', 'short_name' => 'L', 'type' => 'volume', 'decimal_places' => 3],
            ['name' => 'ml', 'short_name' => 'ml', 'type' => 'volume', 'decimal_places' => 0],
            ['name' => 'dozen', 'short_name' => 'doz', 'type' => 'piece', 'decimal_places' => 0],
            ['name' => 'pack', 'short_name' => 'pack', 'type' => 'piece', 'decimal_places' => 0],
            ['name' => 'carton', 'short_name' => 'ctn', 'type' => 'piece', 'decimal_places' => 0],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(['name' => $unit['name']], $unit + ['is_active' => true]);
        }
    }
}
