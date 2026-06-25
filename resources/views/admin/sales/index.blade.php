@extends('admin.layout')

@section('title', 'Sales')

@section('content')
<div class="page-head">
    <div><div class="eyebrow">Transactions</div><h1>Sales</h1><p>Review completed invoices and payment totals.</p></div>
    <a class="btn" href="{{ route('admin.pos.index') }}">New Sale</a>
</div>

<form class="card search-row" method="GET" action="{{ route('admin.sales.index') }}" data-live-search style="margin-bottom:16px;">
    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search invoice, product, SKU, barcode or payment method" autocomplete="off">
    <button class="btn" type="submit">Search</button>
    <div class="live-search-status" data-live-search-status aria-live="polite"></div>
</form>

<div data-live-results>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Invoice</th><th>Date</th><th>Items</th><th>Subtotal</th><th>Discount</th><th>Total</th><th>Paid</th><th>Method</th><th></th></tr></thead>
            <tbody>
            @forelse($sales as $sale)
                <tr>
                    <td><strong>{{ $sale->invoice_no }}</strong></td>
                    <td>{{ $sale->created_at->format('d M Y, h:i A') }}</td>
                    <td>{{ $sale->items_count }}</td>
                    <td>Rs. {{ number_format($sale->subtotal, 2) }}</td>
                    <td>Rs. {{ number_format($sale->discount_total, 2) }}</td>
                    <td><strong>Rs. {{ number_format($sale->grand_total, 2) }}</strong></td>
                    <td>Rs. {{ number_format($sale->paid_amount, 2) }}</td>
                    <td><span class="badge">{{ ucfirst($sale->payment_method) }}</span></td>
                    <td><a class="btn btn-light" href="{{ route('admin.sales.show', $sale) }}">Details</a></td>
                </tr>
            @empty
                <tr><td colspan="9" class="empty-state">No sales found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $sales->links() }}</div>
</div>
@endsection
