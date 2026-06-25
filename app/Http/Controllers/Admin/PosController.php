<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\Sale;
use App\Models\StockAdjustment;
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

        if ((float) $product->stock_quantity <= 0) {
            return response()->json(['success' => false, 'message' => $product->name.' is out of stock.'], 422);
        }

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
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,bank'],
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
                    return response()->json(['success' => false, 'message' => $product->name.' has not enough stock.'], 422);
                }

                $lineTotal = (float) $product->selling_price * $quantity;
                $subtotal += $lineTotal;

                $items[] = compact('product', 'quantity', 'lineTotal', 'cartItem');
            }

            $discount = round(min((float) ($data['discount'] ?? 0), $subtotal), 2);
            $grandTotal = round($subtotal - $discount, 2);
            $paid = round((float) $data['paid_amount'], 2);

            if ($paid < $grandTotal) {
                return response()->json(['success' => false, 'message' => 'Paid amount is less than total.'], 422);
            }

            do {
                $invoiceNo = 'INV-'.now()->format('YmdHis').random_int(100, 999);
            } while (Sale::where('invoice_no', $invoiceNo)->exists());

            $sale = Sale::create([
                'invoice_no' => $invoiceNo,
                'subtotal' => $subtotal,
                'discount_total' => $discount,
                'tax_total' => 0,
                'grand_total' => $grandTotal,
                'paid_amount' => $paid,
                'change_amount' => $paid - $grandTotal,
                'payment_method' => $data['payment_method'],
                'status' => 'completed',
            ]);

            foreach ($items as $item) {
                $product = $item['product'];
                $quantity = $item['quantity'];
                $lineTotal = $item['lineTotal'];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $item['cartItem']['barcode'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $product->selling_price,
                    'line_total' => $lineTotal,
                ]);

                $stockBefore = (float) $product->stock_quantity;
                $stockAfter = $stockBefore - $quantity;
                $product->update(['stock_quantity' => $stockAfter]);
                StockAdjustment::create([
                    'product_id' => $product->id,
                    'type' => 'decrease',
                    'quantity' => $quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reason' => 'Sale '.$invoiceNo,
                    'notes' => 'Automatically deducted during POS checkout.',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully.',
                'invoice_no' => $invoiceNo,
                'grand_total' => $grandTotal,
                'change_amount' => $paid - $grandTotal,
                'sale_url' => route('admin.sales.show', $sale),
            ]);
        });
    }
}
