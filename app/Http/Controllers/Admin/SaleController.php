<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $sales = Sale::query()
            ->withCount('items')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('invoice_no', 'like', "%{$search}%")
                        ->orWhere('payment_method', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('items', function ($items) use ($search) {
                            $items->where('product_name', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%")
                                ->orWhere('barcode', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.sales.index', compact('sales'));
    }

    public function show(Sale $sale): View
    {
        $sale->load(['items', 'customer', 'returns.items']);

        return view('admin.sales.show', compact('sale'));
    }

    public function invoice(Sale $sale): View
    {
        $sale->load(['items', 'customer']);

        return view('admin.sales.invoice', compact('sale'));
    }

    public function receipt(Sale $sale): View
    {
        $sale->load(['items', 'customer']);

        return view('admin.sales.receipt', compact('sale'));
    }

    public function returnForm(Sale $sale): View
    {
        $sale->load('items');

        return view('admin.sales.return', compact('sale'));
    }

    public function storeReturn(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'numeric', 'min:0'],
        ]);
        $data['notes'] ??= null;

        DB::transaction(function () use ($sale, $data) {
            $sale->load('items');
            $refund = 0;
            $returnLines = [];
            foreach ($sale->items as $item) {
                $quantity = (float) ($data['items'][$item->id] ?? 0);
                if ($quantity <= 0) {
                    continue;
                }
                $remaining = (float) $item->quantity - (float) $item->returned_quantity;
                if ($quantity > $remaining) {
                    throw ValidationException::withMessages(['items.'.$item->id => 'Return quantity exceeds the remaining sold quantity.']);
                }
                $lineRefund = round((float) $item->unit_price * $quantity, 2);
                $refund += $lineRefund;
                $returnLines[] = compact('item', 'quantity', 'lineRefund');
            }
            if (! $returnLines) {
                throw ValidationException::withMessages(['items' => 'Select at least one item to return.']);
            }
            do {
                $returnNo = 'RET-'.now()->format('YmdHis').random_int(100, 999);
            } while (SaleReturn::where('return_no', $returnNo)->exists());
            $return = SaleReturn::create(['sale_id' => $sale->id, 'user_id' => auth()->id(), 'return_no' => $returnNo, 'refund_amount' => $refund, 'reason' => $data['reason'], 'notes' => $data['notes']]);
            foreach ($returnLines as $line) {
                $item = $line['item'];
                $return->items()->create(['sale_item_id' => $item->id, 'quantity' => $line['quantity'], 'unit_refund' => $item->unit_price, 'line_refund' => $line['lineRefund']]);
                $item->increment('returned_quantity', $line['quantity']);
                $stockable = $item->variant ?: $item->product;
                $before = (float) $stockable->stock_quantity;
                $stockable->increment('stock_quantity', $line['quantity']);
                StockAdjustment::create(['product_id' => $item->product_id, 'product_variant_id' => $item->product_variant_id, 'type' => 'increase', 'quantity' => $line['quantity'], 'stock_before' => $before, 'stock_after' => $before + $line['quantity'], 'reason' => 'Return '.$returnNo]);
            }
            $sale->increment('refunded_amount', $refund);
            if ((float) $sale->fresh()->refunded_amount >= (float) $sale->grand_total) {
                $sale->update(['status' => 'refunded']);
            }
        });

        return redirect()->route('admin.sales.show', $sale)->with('success', 'Sale return saved and stock restored.');
    }
}
