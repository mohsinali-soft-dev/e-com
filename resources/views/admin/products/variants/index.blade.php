@extends('admin.layout')
@section('title', 'Product Variants')
@section('content')
<div class="page-head">
    <div><div class="eyebrow">Catalog</div><h1>{{ $product->name }} Variants</h1><p>Each size has independent price, stock, SKU and barcode.</p></div>
    <a class="btn btn-light" href="{{ route('admin.products.edit', $product) }}">Back to Product</a>
</div>
<form class="card" method="POST" action="{{ route('admin.products.variants.store', $product) }}">
    @csrf
    <h2>Add Variant</h2>
    <div class="form-grid">
        <input name="name" placeholder="200ml" required>
        <input name="sku" placeholder="SKU" required>
        <input type="number" step=".01" min="0" name="purchase_price" placeholder="Purchase price" required>
        <input type="number" step=".01" min="0" name="selling_price" placeholder="Selling price" required>
        <input type="number" step=".001" min="0" name="stock_quantity" placeholder="Stock" required>
        <input type="number" step=".001" min="0" name="low_stock_alert" placeholder="Low stock alert" required>
    </div>
    <label><input style="width:auto" type="checkbox" name="is_active" value="1" checked> Active</label>
    <button class="btn">Add Variant</button>
</form>

<div style="margin-top:16px">
@forelse($product->variants as $variant)
    <form class="card" style="margin-bottom:12px" method="POST" action="{{ route('admin.products.variants.update', [$product, $variant]) }}">
        @csrf @method('PUT')
        <div class="form-grid">
            <div><label>Variant</label><input name="name" value="{{ $variant->name }}" required></div>
            <div><label>SKU</label><input name="sku" value="{{ $variant->sku }}" required></div>
            <div><label>Barcode</label><input value="{{ $variant->primaryBarcode?->barcode }}" disabled></div>
            <div><label>Purchase</label><input type="number" step=".01" min="0" name="purchase_price" value="{{ $variant->purchase_price }}" required></div>
            <div><label>Sale</label><input type="number" step=".01" min="0" name="selling_price" value="{{ $variant->selling_price }}" required></div>
            <div><label>Stock</label><input type="number" step=".001" min="0" name="stock_quantity" value="{{ $variant->stock_quantity }}" required></div>
            <div><label>Low Stock Alert</label><input type="number" step=".001" min="0" max="{{ $variant->stock_quantity }}" name="low_stock_alert" value="{{ $variant->low_stock_alert }}" required></div>
        </div>
        <label><input style="width:auto" type="checkbox" name="is_active" value="1" @checked($variant->is_active)> Active</label>
        <button class="btn">Update Variant</button>
    </form>
@empty
    <div class="card">No variants yet.</div>
@endforelse
</div>
@endsection
