@extends('admin.layout')

@section('title', $sale->invoice_no)

@section('content')
<div class="page-head">
    <div><div class="eyebrow">Sale details</div><h1>{{ $sale->invoice_no }}</h1><p>{{ $sale->created_at->format('d M Y, h:i A') }}</p></div>
    <div class="stack"><a class="btn btn-light" href="{{ route('admin.sales.index') }}">Back</a><a class="btn btn-light" target="_blank" href="{{ route('admin.sales.invoice',$sale) }}">Invoice</a><a class="btn btn-light" target="_blank" href="{{ route('admin.sales.receipt',$sale) }}">Thermal Receipt</a><a class="btn" href="{{ route('admin.sales.return',$sale) }}">Return Items</a></div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Product</th><th>SKU</th><th>Barcode</th><th>Unit Price</th><th>Quantity</th><th>Line Total</th></tr></thead>
            <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td><strong>{{ $item->product_name }}</strong></td>
                    <td>{{ $item->sku }}</td>
                    <td>{{ $item->barcode ?: '-' }}</td>
                    <td>{{ $adminSetting->currency }} {{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->quantity, 3) }}</td>
                    <td><strong>{{ $adminSetting->currency }} {{ number_format($item->line_total, 2) }}</strong></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="form-grid" style="margin-top:16px;">
    <div></div>
    <div class="card">
        <div class="summary-row"><span>Subtotal</span><strong>{{ $adminSetting->currency }} {{ number_format($sale->subtotal, 2) }}</strong></div>
        <div class="summary-row"><span>Discount</span><strong>{{ $adminSetting->currency }} {{ number_format($sale->discount_total, 2) }}</strong></div>
        <div class="summary-row total"><span>Total</span><span>{{ $adminSetting->currency }} {{ number_format($sale->grand_total, 2) }}</span></div>
        <div class="summary-row"><span>Paid Amount</span><strong>{{ $adminSetting->currency }} {{ number_format($sale->paid_amount, 2) }}</strong></div>
        <div class="summary-row"><span>Change Amount</span><strong class="text-success">{{ $adminSetting->currency }} {{ number_format($sale->change_amount, 2) }}</strong></div>
        <div class="summary-row"><span>Refunded</span><strong class="text-danger">{{ $adminSetting->currency }} {{ number_format($sale->refunded_amount, 2) }}</strong></div>
        <div class="summary-row"><span>Payment Method</span><span class="badge">{{ ucfirst($sale->payment_method) }}</span></div>
    </div>
</div>
@endsection
