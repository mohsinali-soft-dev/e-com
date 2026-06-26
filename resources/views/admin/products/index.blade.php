@extends('admin.layout')

@section('title', 'Products')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Catalog</div>
        <h1>Products</h1>
        <p>Manage items, variants, prices, stock, units and barcode labels.</p>
    </div>
    <a class="btn" href="{{ route('admin.products.create') }}">Add Product</a>
</div>

<form class="card search-row" method="GET" action="{{ route('admin.products.index') }}" data-live-search style="margin-bottom:16px;">
    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search name, SKU, barcode, category, brand or variant" autocomplete="off">
    <button class="btn" type="submit">Search</button>
    <div class="live-search-status" data-live-search-status aria-live="polite"></div>
</form>

<div data-live-results>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Barcode</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Variants</th>
                    <th>Status</th>
                    <th>Barcode Label</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <td>
                        @if($product->image_path)
                            <img class="product-image" src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}">
                        @else
                            <div class="product-image" style="display:grid;place-items:center;color:var(--muted);">N/A</div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $product->name }}</strong><br>
                        <small>{{ $product->sku }}</small>
                    </td>
                    <td><strong>{{ $product->primaryBarcode?->barcode ?? '-' }}</strong></td>
                    <td>{{ $product->category?->name ?? '-' }}</td>
                    <td>{{ $product->unit?->short_name ?? '-' }}</td>
                    <td>{{ $adminSetting->currency }} {{ number_format($product->selling_price, 2) }}</td>
                    <td>
                        @if($product->has_variants)
                            {{ number_format($product->variants->sum('stock_quantity'), 3) }} total
                        @else
                            {{ $product->stock_quantity }}
                        @endif
                    </td>
                    <td>
                        @if($product->variants_count > 0)
                            <details class="variant-dropdown">
                                <summary>{{ $product->variants_count }} variant(s)</summary>
                                <div class="variant-panel">
                                    <table class="variant-table">
                                        <thead>
                                            <tr>
                                                <th>Variant</th>
                                                <th>SKU</th>
                                                <th>Barcode</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th>Status</th>
                                                <th>Label</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($product->variants as $variant)
                                            <tr>
                                                <td><strong>{{ $variant->name }}</strong></td>
                                                <td>{{ $variant->sku }}</td>
                                                <td>{{ $variant->primaryBarcode?->barcode ?? '-' }}</td>
                                                <td>{{ $adminSetting->currency }} {{ number_format($variant->selling_price, 2) }}</td>
                                                <td>{{ $variant->stock_quantity }}</td>
                                                <td><span class="badge">{{ $variant->is_active ? 'Active' : 'Inactive' }}</span></td>
                                                <td>
                                                    @if($variant->primaryBarcode)
                                                        <form action="{{ route('admin.barcode-labels.print') }}" method="POST" target="_blank" class="stack">
                                                            @csrf
                                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                            <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
                                                            <input type="number" name="quantity" min="1" max="200" value="1" aria-label="Variant label quantity" style="width:72px;">
                                                            <button class="btn btn-light" type="submit">Print</button>
                                                        </form>
                                                    @else
                                                        <span class="muted">No barcode</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        @else
                            <span class="muted">No variants</span>
                        @endif
                    </td>
                    <td><span class="badge">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        @if($product->primaryBarcode)
                            <form action="{{ route('admin.barcode-labels.print') }}" method="POST" target="_blank" class="stack" style="flex-wrap:nowrap;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="number" name="quantity" min="1" max="200" value="1" aria-label="Label quantity" style="width:72px;">
                                <button class="btn btn-light" type="submit">Print</button>
                            </form>
                        @else
                            <span class="muted">No barcode</span>
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-light" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                        <a class="btn btn-light" href="{{ route('admin.products.variants.index', $product) }}">Variants</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-light" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="11" class="empty-state">No products found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $products->links() }}</div>
</div>
@endsection
