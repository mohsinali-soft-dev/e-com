<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\StockAdjustment;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        try {
            DB::transaction(function () use ($data) {
                $product = Product::create($data);
                ProductBarcode::create([
                    'product_id' => $product->id,
                    'barcode' => $this->generateBarcode($product->id),
                    'type' => 'store',
                    'is_primary' => true,
                ]);
                if ((float) $product->stock_quantity > 0) {
                    StockAdjustment::create([
                        'product_id' => $product->id, 'type' => 'increase', 'quantity' => $product->stock_quantity,
                        'stock_before' => 0, 'stock_after' => $product->stock_quantity, 'reason' => 'Opening stock',
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            if (! empty($data['image_path'])) {
                Storage::disk('public')->delete($data['image_path']);
            }
            throw $exception;
        }

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

        DB::transaction(function () use ($product, $data) {
            $before = (float) $product->stock_quantity;
            $product->update($data);
            $after = (float) $product->stock_quantity;
            if ($before !== $after) {
                StockAdjustment::create([
                    'product_id' => $product->id, 'type' => $after > $before ? 'increase' : 'decrease',
                    'quantity' => abs($after - $before), 'stock_before' => $before, 'stock_after' => $after,
                    'reason' => 'Product form stock correction',
                ]);
            }
        });

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
        if ($product->saleItems()->exists() || $product->stockAdjustments()->exists() || OrderItem::where('product_id', $product->id)->exists()) {
            return back()->withErrors(['product' => 'Products with stock, sales, or order history cannot be deleted. Deactivate the product instead.']);
        }

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
