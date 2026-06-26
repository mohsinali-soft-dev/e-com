<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Nestle', 'Pepsi', 'Head & Shoulders', 'Lifebuoy', 'Surf Excel', 'Local'] as $name) {
            Brand::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'is_active' => true]);
        }
    }
}
