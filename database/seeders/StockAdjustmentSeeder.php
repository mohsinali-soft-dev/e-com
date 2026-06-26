<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Database\Seeder;

class StockAdjustmentSeeder extends Seeder
{
    public function run(): void
    {
        Product::with('variants')->orderBy('id')->get()->each(function (Product $product) {
            if (! $product->has_variants && (float) $product->stock_quantity > 0) {
                StockAdjustment::updateOrCreate([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'reason' => 'Opening stock',
                ], [
                    'type' => 'increase',
                    'quantity' => $product->stock_quantity,
                    'stock_before' => 0,
                    'stock_after' => $product->stock_quantity,
                    'notes' => 'Seeded opening stock.',
                ]);
            }

            foreach ($product->variants as $variant) {
                if ((float) $variant->stock_quantity <= 0) {
                    continue;
                }

                StockAdjustment::updateOrCreate([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'reason' => 'Opening stock',
                ], [
                    'type' => 'increase',
                    'quantity' => $variant->stock_quantity,
                    'stock_before' => 0,
                    'stock_after' => $variant->stock_quantity,
                    'notes' => 'Seeded opening stock.',
                ]);
            }
        });
    }
}
