<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Grocery', 'Beverages', 'Personal Care', 'Bakery', 'Household'] as $name) {
            Category::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'is_active' => true]);
        }
    }
}
