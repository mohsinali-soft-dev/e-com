<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('store.cart.index', ['cart' => collect(session('cart', []))]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['product_id' => ['required', 'exists:products,id'], 'product_variant_id' => ['nullable', 'exists:product_variants,id'], 'quantity' => ['required', 'numeric', 'gt:0']]);
        $product = Product::where('is_active', true)->findOrFail($data['product_id']);
        $variant = $data['product_variant_id'] ? ProductVariant::where('product_id', $product->id)->where('is_active', true)->findOrFail($data['product_variant_id']) : null;
        if ($product->has_variants && ! $variant) {
            return back()->withErrors(['product_variant_id' => 'Please select a product variant.']);
        }
        $stockable = $variant ?: $product;
        $quantity = (float) $data['quantity'];
        $key = $product->id.':'.($variant?->id ?? 0);
        $cart = session('cart', []);
        $newQuantity = ($cart[$key]['quantity'] ?? 0) + $quantity;
        if ($newQuantity > (float) $stockable->stock_quantity) {
            return back()->withErrors(['quantity' => 'Requested quantity exceeds available stock.']);
        }
        if ($product->sale_type === 'piece' && floor($newQuantity) !== $newQuantity) {
            return back()->withErrors(['quantity' => 'Piece products require whole quantities.']);
        }
        $cart[$key] = [
            'key' => $key, 'product_id' => $product->id, 'variant_id' => $variant?->id,
            'name' => $product->name, 'variant_name' => $variant?->name, 'sku' => $variant?->sku ?? $product->sku,
            'price' => (float) ($variant?->selling_price ?? $product->selling_price), 'quantity' => $newQuantity,
            'image' => $product->image_path,
        ];
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', 'Product added to cart.');
    }

    public function update(Request $request, string $key)
    {
        $quantity = (float) $request->validate(['quantity' => ['required', 'numeric', 'gt:0']])['quantity'];
        $cart = session('cart', []);
        abort_unless(isset($cart[$key]), 404);
        $item = $cart[$key];
        $product = Product::findOrFail($item['product_id']);
        $stockable = $item['variant_id'] ? ProductVariant::where('product_id', $product->id)->findOrFail($item['variant_id']) : $product;
        if ($quantity > (float) $stockable->stock_quantity) {
            return back()->withErrors(['quantity' => 'Requested quantity exceeds available stock.']);
        }
        if ($product->sale_type === 'piece' && floor($quantity) !== $quantity) {
            return back()->withErrors(['quantity' => 'Piece products require whole quantities.']);
        }
        $cart[$key]['quantity'] = $quantity;
        session(['cart' => $cart]);

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(string $key)
    {
        $cart = session('cart', []);
        unset($cart[$key]);
        session(['cart' => $cart]);

        return back()->with('success', 'Item removed.');
    }
}
