<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductBarcode;
use Illuminate\Database\Seeder;

class BarcodeSeeder extends Seeder
{
    public function run(): void
    {
        $sequence = 100000000001;

        Product::with('variants')->orderBy('id')->get()->each(function (Product $product) use (&$sequence) {
            ProductBarcode::updateOrCreate([
                'product_id' => $product->id,
                'product_variant_id' => null,
                'is_primary' => true,
            ], [
                'barcode' => (string) $sequence++,
                'type' => 'store',
            ]);

            foreach ($product->variants as $variant) {
                ProductBarcode::updateOrCreate([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'is_primary' => true,
                ], [
                    'barcode' => (string) $sequence++,
                    'type' => 'store',
                ]);
            }
        });
    }
}
