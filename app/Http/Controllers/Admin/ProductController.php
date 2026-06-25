<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->toString();

        $products = Product::with(['category', 'brand', 'unit', 'primaryBarcode'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhereHas('barcodes', fn ($barcodes) => $barcodes->where('barcode', 'like', "%{$search}%"))
                        ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('brand', fn ($brand) => $brand->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['has_variants'] = false;

        $product = Product::create($data);

        ProductBarcode::create([
            'product_id' => $product->id,
            'barcode' => $this->generateBarcode($product->id),
            'type' => 'store',
            'is_primary' => true,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully with auto barcode.');
    }

    public function edit(Product $product)
    {
        $product->load('barcodes');

        return view('admin.products.edit', array_merge($this->formData(), compact('product')));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validatedData($request, $product->id);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        if ($request->hasFile('image') && $product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        $data['is_active'] = $request->boolean('is_active');

        $product->update($data);

        if (! $product->barcodes()->where('is_primary', true)->exists()) {
            ProductBarcode::create([
                'product_id' => $product->id,
                'barcode' => $this->generateBarcode($product->id),
                'type' => 'store',
                'is_primary' => true,
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
            'units' => Unit::where('is_active', true)->orderBy('name')->get(),
        ];
    }

    private function validatedData(Request $request, ?int $productId = null): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($productId)],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'sale_type' => ['required', 'in:piece,weight,volume'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'numeric', 'min:0'],
            'low_stock_alert' => ['nullable', 'numeric', 'min:0', 'lte:stock_quantity'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function generateBarcode(int $productId): string
    {
        do {
            $barcode = 'ECM'.str_pad((string) $productId, 6, '0', STR_PAD_LEFT).random_int(10, 99);
        } while (ProductBarcode::where('barcode', $barcode)->exists());

        return $barcode;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $suffix = 2;

        while (Product::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
