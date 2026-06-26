<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\ProductVariant;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        $product->load(['variants.primaryBarcode']);

        return view('admin.products.variants.index', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $data = $this->validated($request, $product);
        DB::transaction(function () use ($product, $data) {
            $variant = $product->variants()->create($data);
            ProductBarcode::create([
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'barcode' => $this->barcode($product->id, $variant->id),
                'type' => 'store',
                'is_primary' => true,
            ]);
            if ((float) $variant->stock_quantity > 0) {
                StockAdjustment::create([
                    'product_id' => $product->id, 'product_variant_id' => $variant->id,
                    'type' => 'increase', 'quantity' => $variant->stock_quantity,
                    'stock_before' => 0, 'stock_after' => $variant->stock_quantity, 'reason' => 'Variant opening stock',
                ]);
            }
            $product->update(['has_variants' => true]);
        });

        return back()->with('success', 'Variant created with barcode.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);
        $data = $this->validated($request, $product, $variant);
        DB::transaction(function () use ($variant, $product, $data) {
            $before = (float) $variant->stock_quantity;
            $variant->update($data);
            $after = (float) $variant->stock_quantity;
            if ($before !== $after) {
                StockAdjustment::create([
                    'product_id' => $product->id, 'product_variant_id' => $variant->id,
                    'type' => $after > $before ? 'increase' : 'decrease', 'quantity' => abs($after - $before),
                    'stock_before' => $before, 'stock_after' => $after, 'reason' => 'Variant form stock correction',
                ]);
            }
        });

        return back()->with('success', 'Variant updated.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);
        if ($variant->stock_quantity > 0 || $variant->saleItems()->exists() || $variant->orderItems()->exists() || $variant->stockAdjustments()->exists()) {
            return back()->withErrors(['variant' => 'A variant with stock, sales, or order history cannot be deleted.']);
        }
        $variant->delete();
        if (! $product->variants()->exists()) {
            $product->update(['has_variants' => false]);
        }

        return back()->with('success', 'Variant deleted.');
    }

    private function validated(Request $request, Product $product, ?ProductVariant $variant = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('product_variants')->ignore($variant),
                Rule::unique('products', 'sku'),
            ],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0', 'gte:purchase_price'],
            'stock_quantity' => ['required', 'numeric', 'min:0'],
            'low_stock_alert' => ['required', 'numeric', 'min:0', 'lte:stock_quantity'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function barcode(int $productId, int $variantId): string
    {
        do {
            $barcode = 'ECV'.str_pad((string) $productId, 5, '0', STR_PAD_LEFT).str_pad((string) $variantId, 4, '0', STR_PAD_LEFT).random_int(10, 99);
        } while (ProductBarcode::where('barcode', $barcode)->exists());

        return $barcode;
    }
}
