<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function lowStock(): View
    {
        $products = Product::with(['category', 'unit', 'primaryBarcode'])
            ->whereColumn('stock_quantity', '<=', 'low_stock_alert')
            ->orderBy('stock_quantity')
            ->paginate(20);

        return view('admin.inventory.low-stock', compact('products'));
    }

    public function adjustments(): View
    {
        return view('admin.inventory.adjustments', [
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
            'adjustments' => StockAdjustment::with(['product', 'variant'])->latest()->paginate(20),
        ]);
    }

    public function storeAdjustment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:increase,decrease'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);
            $before = (float) $product->stock_quantity;
            $quantity = (float) $data['quantity'];
            $after = $data['type'] === 'increase' ? $before + $quantity : $before - $quantity;

            if ($after < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'The adjustment cannot reduce stock below zero.',
                ]);
            }

            $product->update(['stock_quantity' => $after]);
            StockAdjustment::create([
                ...$data,
                'stock_before' => $before,
                'stock_after' => $after,
            ]);
        });

        return back()->with('success', 'Stock adjustment saved successfully.');
    }
}
