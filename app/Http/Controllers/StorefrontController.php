<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function home()
    {
        return view('store.home', [
            'products' => Product::with(['category', 'variants'])->where('is_active', true)->where(fn ($q) => $q->where('stock_quantity', '>', 0)->orWhereHas('variants', fn ($v) => $v->where('is_active', true)->where('stock_quantity', '>', 0)))->latest()->limit(8)->get(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'setting' => Setting::current(),
        ]);
    }

    public function products(Request $request)
    {
        $products = Product::with(['category', 'variants'])
            ->where('is_active', true)
            ->when($request->filled('category'), fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $request->category)))
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$request->search.'%')->orWhere('description', 'like', '%'.$request->search.'%')->orWhere('sku', 'like', '%'.$request->search.'%')))
            ->latest()->paginate(12)->withQueryString();

        return view('store.products.index', ['products' => $products, 'categories' => Category::where('is_active', true)->orderBy('name')->get()]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load(['category', 'brand', 'unit', 'variants' => fn ($q) => $q->where('is_active', true)]);

        return view('store.products.show', compact('product'));
    }
}
