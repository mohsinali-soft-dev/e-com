@extends('admin.layout')

@section('title', 'Low Stock')

@section('content')
<div class="page-head">
    <div><div class="eyebrow">Inventory</div><h1>Low Stock</h1><p>Products at or below their configured alert level.</p></div>
    <a class="btn" href="{{ route('admin.inventory.adjustments') }}">Adjust Stock</a>
</div>

<div class="table-wrap">
    <table>
        <thead><tr><th>Product</th><th>Barcode</th><th>Category</th><th>Current Stock</th><th>Alert At</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($products as $product)
            <tr>
                <td><strong>{{ $product->name }}</strong><br><small>{{ $product->sku }}</small></td>
                <td>{{ $product->primaryBarcode?->barcode ?? '-' }}</td>
                <td>{{ $product->category?->name ?? '-' }}</td>
                <td><strong class="text-danger">{{ number_format($product->stock_quantity, 2) }} {{ $product->unit?->short_name }}</strong></td>
                <td>{{ number_format($product->low_stock_alert, 2) }}</td>
                <td><span class="badge">{{ $product->stock_quantity <= 0 ? 'Out of stock' : 'Low stock' }}</span></td>
                <td><a class="btn btn-light" href="{{ route('admin.products.edit', $product) }}">Product</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty-state">Excellent—no products are currently low on stock.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:16px;">{{ $products->links() }}</div>
@endsection
