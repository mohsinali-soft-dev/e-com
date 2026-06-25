@extends('admin.layout')

@section('title', 'Products')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Catalog</div>
        <h1>Products</h1>
        <p>Manage items, prices, stock, units and barcode values.</p>
    </div>
    <a class="btn" href="{{ route('admin.products.create') }}">Add Product</a>
</div>

<form class="card search-row" method="GET" action="{{ route('admin.products.index') }}" data-live-search style="margin-bottom:16px;">
    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search name, SKU, barcode, category or brand" autocomplete="off">
    <button class="btn" type="submit">Search</button>
    <div class="live-search-status" data-live-search-status aria-live="polite"></div>
</form>

<div data-live-results>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Image</th><th>Product</th><th>Barcode</th><th>Category</th><th>Unit</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
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
                    <td><strong>{{ $product->name }}</strong><br><small>{{ $product->sku }}</small></td>
                    <td><strong>{{ $product->primaryBarcode?->barcode ?? '-' }}</strong></td>
                    <td>{{ $product->category?->name ?? '-' }}</td>
                    <td>{{ $product->unit?->short_name ?? '-' }}</td>
                    <td>Rs. {{ number_format($product->selling_price, 2) }}</td>
                    <td>{{ $product->stock_quantity }}</td>
                    <td><span class="badge">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <a class="btn btn-light" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-light" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="empty-state">No products found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $products->links() }}</div>
</div>
@endsection
