<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class BarcodeLabelController extends Controller
{
    public function print(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:200'],
        ]);
        $product = Product::with(['primaryBarcode', 'variants.primaryBarcode'])->findOrFail($data['product_id']);
        $variantId = $data['product_variant_id'] ?? null;
        $variant = $variantId ? $product->variants->firstWhere('id', (int) $variantId) : null;
        if ($variantId && ! $variant) {
            abort(422, 'Variant does not belong to product.');
        }
        $barcode = $variant?->primaryBarcode?->barcode ?? $product->primaryBarcode?->barcode;
        abort_unless($barcode, 422, 'No barcode is available.');

        return view('admin.barcode-labels.print', compact('product', 'variant', 'barcode') + ['quantity' => $data['quantity']]);
    }
}
