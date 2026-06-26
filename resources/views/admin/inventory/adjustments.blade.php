@extends('admin.layout')

@section('title', 'Stock Adjustments')

@section('content')
<div class="page-head">
    <div><div class="eyebrow">Inventory</div><h1>Stock Adjustments</h1><p>Correct stock levels with a complete movement history.</p></div>
    <a class="btn btn-light" href="{{ route('admin.inventory.low-stock') }}">Low Stock</a>
</div>

<div class="card" style="margin-bottom:16px;">
    <form action="{{ route('admin.inventory.adjustments.store') }}" method="POST">
        @csrf
        <div class="form-grid">
            <div>
                <label>Product</label>
                <select name="product_id" required>
                    <option value="">Select product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }} - stock {{ number_format($product->stock_quantity, 2) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Adjustment Type</label>
                <select name="type" required>
                    <option value="increase" @selected(old('type') === 'increase')>Increase Stock</option>
                    <option value="decrease" @selected(old('type') === 'decrease')>Decrease Stock</option>
                </select>
            </div>
            <div>
                <label>Quantity</label>
                <input type="number" name="quantity" min="0.01" step="0.01" value="{{ old('quantity') }}" required>
            </div>
            <div>
                <label>Reason</label>
                <input type="text" name="reason" value="{{ old('reason') }}" placeholder="Purchase, damage, correction..." required>
            </div>
        </div>
        <label>Notes</label>
        <textarea name="notes" rows="2" placeholder="Optional adjustment details">{{ old('notes') }}</textarea>
        <button class="btn" type="submit" style="margin-top:16px;">Save Adjustment</button>
    </form>
</div>

<div class="table-wrap">
    <table>
        <thead><tr><th>Date</th><th>Product</th><th>Type</th><th>Quantity</th><th>Before</th><th>After</th><th>Reason</th></tr></thead>
        <tbody>
        @forelse($adjustments as $adjustment)
            <tr>
                <td>{{ $adjustment->created_at->format('d M Y, h:i A') }}</td>
                <td><strong>{{ $adjustment->product?->name ?? 'Deleted product' }}</strong>@if($adjustment->variant)<br><small>{{ $adjustment->variant->name }}</small>@endif</td>
                <td><span class="badge">{{ ucfirst($adjustment->type) }}</span></td>
                <td class="{{ $adjustment->type === 'increase' ? 'text-success' : 'text-danger' }}">{{ $adjustment->type === 'increase' ? '+' : '-' }}{{ number_format($adjustment->quantity, 2) }}</td>
                <td>{{ number_format($adjustment->stock_before, 2) }}</td>
                <td><strong>{{ number_format($adjustment->stock_after, 2) }}</strong></td>
                <td>{{ $adjustment->reason }}@if($adjustment->notes)<br><small class="muted">{{ $adjustment->notes }}</small>@endif</td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty-state">No stock adjustments recorded yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:16px;">{{ $adjustments->links() }}</div>
@endsection
