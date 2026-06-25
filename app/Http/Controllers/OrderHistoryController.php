<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderHistoryController extends Controller
{
    public function index(Request $request)
    {
        $orders = collect();
        if ($request->filled('phone')) {
            $orders = Order::where('customer_phone', $request->phone)->latest()->get();
        }

        return view('store.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        abort_unless(hash_equals((string) $order->customer_phone, (string) $request->query('phone')), 403);
        $order->load('items');

        return view('store.orders.show', compact('order'));
    }
}
