<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBarcode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            return response()->json(['success' => false, 'message' => 'Product not found for this barcode.'], 404);
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

    public function checkout(Request $request): JsonResponse
    {
        $data = $request->validate([
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'numeric', 'min:0.001'],
            'items.*.barcode' => ['nullable', 'string', 'max:100'],
        ]);

        return DB::transaction(function () use ($data) {
            $subtotal = 0;
            $items = [];

            foreach ($data['items'] as $cartItem) {
                $product = Product::lockForUpdate()->findOrFail($cartItem['id']);
                $quantity = (float) $cartItem['qty'];

                if ((float) $product->stock_quantity < $quantity) {
                    return response()->json(['success' => false, 'message' => $product->name . ' has not enough stock.'], 422);
                }

                $lineTotal = (float) $product->selling_price * $quantity;
                $subtotal += $lineTotal;

                $items[] = compact('product', 'quantity', 'lineTotal', 'cartItem');
            }

            $paid = (float) $data['paid_amount'];

            if ($paid < $subtotal) {
                return response()->json(['success' => false, 'message' => 'Paid amount is less than total.'], 422);
            }

            $invoiceNo = 'INV-' . now()->format('YmdHis') . random_int(100, 999);

            $saleId = DB::table('sales')->insertGetId([
                'invoice_no' => $invoiceNo,
                'subtotal' => $subtotal,
                'discount_total' => 0,
                'tax_total' => 0,
                'grand_total' => $subtotal,
                'paid_amount' => $paid,
                'change_amount' => $paid - $subtotal,
                'payment_method' => $data['payment_method'],
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($items as $item) {
                $product = $item['product'];
                $quantity = $item['quantity'];
                $lineTotal = $item['lineTotal'];

                DB::table('sale_items')->insert([
                    'sale_id' => $saleId,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $item['cartItem']['barcode'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $product->selling_price,
                    'line_total' => $lineTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $product->decrement('stock_quantity', $quantity);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully.',
                'invoice_no' => $invoiceNo,
                'grand_total' => $subtotal,
                'change_amount' => $paid - $subtotal,
            ]);
        });
    }
}
