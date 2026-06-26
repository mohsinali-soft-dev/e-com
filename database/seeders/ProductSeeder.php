<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');
        $brands = Brand::pluck('id', 'name');
        $units = Unit::pluck('id', 'name');

        $products = [
            [
                'name' => 'Head & Shoulders Shampoo Bottle',
                'sku' => 'HS-BOTTLE',
                'category_id' => $categories['Personal Care'],
                'brand_id' => $brands['Head & Shoulders'],
                'unit_id' => $units['piece'],
                'sale_type' => 'piece',
                'purchase_price' => 450,
                'selling_price' => 550,
                'stock_quantity' => 36,
                'low_stock_alert' => 6,
            ],
            [
                'name' => 'Loose Rice 1kg',
                'sku' => 'RICE-LOOSE-1KG',
                'category_id' => $categories['Grocery'],
                'brand_id' => $brands['Local'],
                'unit_id' => $units['kg'],
                'sale_type' => 'weight',
                'purchase_price' => 260,
                'selling_price' => 320,
                'stock_quantity' => 75,
                'low_stock_alert' => 15,
            ],
            [
                'name' => 'Cooking Oil',
                'sku' => 'OIL-1L',
                'category_id' => $categories['Grocery'],
                'brand_id' => $brands['Local'],
                'unit_id' => $units['liter'],
                'sale_type' => 'volume',
                'purchase_price' => 520,
                'selling_price' => 610,
                'stock_quantity' => 48,
                'low_stock_alert' => 8,
            ],
            [
                'name' => 'Biscuits Pack',
                'sku' => 'BISCUITS-PACK',
                'category_id' => $categories['Bakery'],
                'brand_id' => $brands['Local'],
                'unit_id' => $units['pack'],
                'sale_type' => 'piece',
                'purchase_price' => 80,
                'selling_price' => 100,
                'stock_quantity' => 120,
                'low_stock_alert' => 20,
            ],
            [
                'name' => 'Eggs Dozen',
                'sku' => 'EGGS-DOZEN',
                'category_id' => $categories['Grocery'],
                'brand_id' => $brands['Local'],
                'unit_id' => $units['dozen'],
                'sale_type' => 'piece',
                'purchase_price' => 300,
                'selling_price' => 360,
                'stock_quantity' => 30,
                'low_stock_alert' => 5,
            ],
            [
                'name' => 'Coke',
                'sku' => 'COKE',
                'category_id' => $categories['Beverages'],
                'brand_id' => $brands['Pepsi'],
                'unit_id' => $units['piece'],
                'sale_type' => 'piece',
                'purchase_price' => 0,
                'selling_price' => 0,
                'stock_quantity' => 0,
                'low_stock_alert' => 0,
                'has_variants' => true,
                'variants' => [
                    ['name' => '250ml', 'sku' => 'COKE-250ML', 'purchase_price' => 65, 'selling_price' => 80, 'stock_quantity' => 96, 'low_stock_alert' => 12],
                    ['name' => '500ml', 'sku' => 'COKE-500ML', 'purchase_price' => 105, 'selling_price' => 130, 'stock_quantity' => 72, 'low_stock_alert' => 12],
                    ['name' => '1L', 'sku' => 'COKE-1L', 'purchase_price' => 170, 'selling_price' => 210, 'stock_quantity' => 48, 'low_stock_alert' => 8],
                ],
            ],
            [
                'name' => 'Head & Shoulders Shampoo',
                'sku' => 'HS-SHAMPOO',
                'category_id' => $categories['Personal Care'],
                'brand_id' => $brands['Head & Shoulders'],
                'unit_id' => $units['piece'],
                'sale_type' => 'piece',
                'purchase_price' => 0,
                'selling_price' => 0,
                'stock_quantity' => 0,
                'low_stock_alert' => 0,
                'has_variants' => true,
                'variants' => [
                    ['name' => '200ml', 'sku' => 'HS-200ML', 'purchase_price' => 350, 'selling_price' => 430, 'stock_quantity' => 40, 'low_stock_alert' => 6],
                    ['name' => '400ml', 'sku' => 'HS-400ML', 'purchase_price' => 670, 'selling_price' => 790, 'stock_quantity' => 32, 'low_stock_alert' => 5],
                    ['name' => '800ml', 'sku' => 'HS-800ML', 'purchase_price' => 1200, 'selling_price' => 1450, 'stock_quantity' => 18, 'low_stock_alert' => 4],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $variants = $productData['variants'] ?? [];
            unset($productData['variants']);
            $product = Product::updateOrCreate(['sku' => $productData['sku']], $productData + [
                'slug' => Str::slug($productData['name']),
                'description' => 'Demo product for testing POS and storefront flows.',
                'has_variants' => $productData['has_variants'] ?? false,
                'is_active' => true,
            ]);

            foreach ($variants as $variantData) {
                $product->variants()->updateOrCreate(['sku' => $variantData['sku']], $variantData + ['is_active' => true]);
            }
        }
    }
}
