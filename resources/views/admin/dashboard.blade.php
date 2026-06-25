@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="page-head">
        <div><div class="eyebrow">Reports</div><h1>Store Dashboard</h1><p>Today’s performance and inventory health at a glance.</p></div>
        <a class="btn" href="{{ route('admin.pos.index') }}">Open POS</a>
    </div>

    <div class="grid">
        <div class="card"><div class="stat-label">Sales Today</div><div class="stat">{{ $salesToday }}</div></div>
        <div class="card"><div class="stat-label">Revenue Today</div><div class="stat">Rs. {{ number_format($revenueToday, 2) }}</div></div>
        <div class="card"><div class="stat-label">Total Revenue</div><div class="stat">Rs. {{ number_format($totalRevenue, 2) }}</div></div>
        <div class="card"><div class="stat-label">Low Stock Items</div><div class="stat {{ $lowStockProducts ? 'text-danger' : '' }}">{{ $lowStockProducts }}</div></div>
    </div>

    <div class="form-grid" style="margin-top:16px;align-items:start;">
        <div class="card">
            <div class="page-head"><div><div class="eyebrow">Performance</div><h2>Top Selling Products</h2></div></div>
            @forelse($topProducts as $item)
                <div class="summary-row">
                    <div><strong>{{ $item->product_name }}</strong><br><small class="muted">Rs. {{ number_format($item->revenue, 2) }} revenue</small></div>
                    <span class="badge">{{ number_format($item->quantity_sold, 3) }} sold</span>
                </div>
            @empty
                <div class="empty-state">No sales recorded yet.</div>
            @endforelse
        </div>
        <div class="card">
            <div class="eyebrow">Catalog</div><h2>Store Records</h2>
            <div class="summary-row"><span>Products</span><strong>{{ $totalProducts }}</strong></div>
            <div class="summary-row"><span>Categories</span><strong>{{ $totalCategories }}</strong></div>
            <div class="summary-row"><span>Brands</span><strong>{{ $totalBrands }}</strong></div>
            <div class="summary-row"><span>Generated Barcodes</span><strong>{{ $totalBarcodes }}</strong></div>
        </div>
    </div>
@endsection
