<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductBarcode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        return view('admin.pos.index');
    }

    public function scan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'barcode' => ['required', 'string', 'max:100'],
        ]);

        $barcode = ProductBarcode::with(['product.unit', 'product.category', 'product.brand'])
            ->where('barcode', $data['barcode'])
            ->first();

        if (! $barcode || ! $barcode->product || ! $barcode->product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found for this barcode.',
            ], 404);
        }

        $product = $barcode->product;

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $barcode->barcode,
                'unit' => $product->unit?->short_name,
                'sale_type' => $product->sale_type,
                'price' => (float) $product->selling_price,
                'stock' => (float) $product->stock_quantity,
            ],
        ]);
    }
}
