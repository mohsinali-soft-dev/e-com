<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\ProductVariant;
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
        return view('admin.pos.index', [
            'customers' => Customer::orderBy('name')->get(['id', 'name', 'phone']),
        ]);
    }

    public function scan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'barcode' => ['required', 'string', 'max:100'],
        ]);

        $barcode = ProductBarcode::with(['product.unit', 'product.category', 'product.brand', 'variant'])
            ->where('barcode', $data['barcode'])
            ->first();

        if (! $barcode || ! $barcode->product || ! $barcode->product->is_active) {
            return response()->json(['success' => false, 'message' => 'Product not found for this barcode.'], 404);
        }

        $product = $barcode->product;
        $variant = $barcode->variant;
        $stockable = $variant ?: $product;

        if ($variant && ! $variant->is_active) {
            return response()->json(['success' => false, 'message' => 'This variant is inactive.'], 422);
        }

        if ((float) $stockable->stock_quantity <= 0) {
            return response()->json(['success' => false, 'message' => $product->name.' is out of stock.'], 422);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'variant_id' => $variant?->id,
                'name' => $product->name.($variant ? ' - '.$variant->name : ''),
                'sku' => $variant?->sku ?? $product->sku,
                'barcode' => $barcode->barcode,
                'unit' => $product->unit?->short_name,
                'sale_type' => $product->sale_type,
                'price' => (float) ($variant?->selling_price ?? $product->selling_price),
                'stock' => (float) $stockable->stock_quantity,
            ],
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $data = $request->validate([
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,bank'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:products,id'],
            'items.*.variant_id' => ['nullable', 'exists:product_variants,id'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.barcode' => ['nullable', 'string', 'max:100'],
        ]);

        return DB::transaction(function () use ($data) {
            $subtotal = 0;
            $items = [];

            $groupedItems = collect($data['items'])->groupBy(fn ($item) => $item['id'].':'.($item['variant_id'] ?? 0))
                ->map(function ($group) {
                    $item = $group->first();
                    $item['qty'] = $group->sum('qty');

                    return $item;
                });

            foreach ($groupedItems as $cartItem) {
                $product = Product::lockForUpdate()->findOrFail($cartItem['id']);
                if (! $product->is_active) {
                    return response()->json(['success' => false, 'message' => $product->name.' is inactive.'], 422);
                }
                $variant = ! empty($cartItem['variant_id'])
                    ? ProductVariant::lockForUpdate()->where('product_id', $product->id)->where('is_active', true)->findOrFail($cartItem['variant_id'])
                    : null;
                if ($product->has_variants && ! $variant) {
                    return response()->json(['success' => false, 'message' => $product->name.' requires a variant.'], 422);
                }
                $stockable = $variant ?: $product;
                $quantity = (float) $cartItem['qty'];
                if ($product->sale_type === 'piece' && floor($quantity) !== $quantity) {
                    return response()->json(['success' => false, 'message' => $product->name.' requires a whole quantity.'], 422);
                }
                if (! empty($cartItem['barcode']) && ! ProductBarcode::where('barcode', $cartItem['barcode'])
                    ->where('product_id', $product->id)
                    ->where('product_variant_id', $variant?->id)
                    ->exists()) {
                    return response()->json(['success' => false, 'message' => 'Barcode does not match '.$product->name.'.'], 422);
                }

                if ((float) $stockable->stock_quantity < $quantity) {
                    return response()->json(['success' => false, 'message' => $product->name.' has not enough stock.'], 422);
                }

                $unitPrice = (float) ($variant?->selling_price ?? $product->selling_price);
                $lineTotal = round($unitPrice * $quantity, 2);
                $subtotal += $lineTotal;

                $items[] = compact('product', 'variant', 'stockable', 'quantity', 'unitPrice', 'lineTotal', 'cartItem');
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
                'customer_id' => $data['customer_id'] ?? null,
                'user_id' => auth()->id(),
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
                $variant = $item['variant'];
                $stockable = $item['stockable'];
                $quantity = $item['quantity'];
                $lineTotal = $item['lineTotal'];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $product->name.($variant ? ' - '.$variant->name : ''),
                    'sku' => $variant?->sku ?? $product->sku,
                    'barcode' => $item['cartItem']['barcode'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $item['unitPrice'],
                    'purchase_price' => $variant?->purchase_price ?? $product->purchase_price,
                    'line_total' => $lineTotal,
                ]);

                $stockBefore = (float) $stockable->stock_quantity;
                $stockAfter = $stockBefore - $quantity;
                $stockable->update(['stock_quantity' => $stockAfter]);
                StockAdjustment::create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
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
