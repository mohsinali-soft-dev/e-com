<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('customer')->withCount('items')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q) => $q->where('order_no', 'like', '%'.$request->search.'%')->orWhere('customer_name', 'like', '%'.$request->search.'%')->orWhere('customer_phone', 'like', '%'.$request->search.'%')))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,processing,delivered,cancelled']]);
        if (in_array($order->status, ['delivered', 'cancelled'], true) && $order->status !== $data['status']) {
            return back()->withErrors(['status' => 'Delivered or cancelled orders are final.']);
        }
        DB::transaction(function () use ($order, $data) {
            if ($data['status'] === 'cancelled' && $order->stock_deducted) {
                foreach ($order->items as $item) {
                    $stockable = $item->variant ?: $item->product;
                    $before = (float) $stockable->stock_quantity;
                    $stockable->increment('stock_quantity', $item->quantity);
                    StockAdjustment::create([
                        'product_id' => $item->product_id, 'product_variant_id' => $item->product_variant_id, 'type' => 'increase', 'quantity' => $item->quantity,
                        'stock_before' => $before, 'stock_after' => $before + (float) $item->quantity,
                        'reason' => 'Cancelled order '.$order->order_no,
                    ]);
                }
                $order->stock_deducted = false;
                $order->cancelled_at = now();
            }
            if ($data['status'] === 'delivered') {
                $order->delivered_at = now();
            }
            $order->status = $data['status'];
            $order->save();
        });

        return back()->with('success', 'Order status updated.');
    }
}
