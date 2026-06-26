@extends('admin.layout')

@section('title', 'POS')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Checkout</div>
        <h1>Point of Sale</h1>
        <p>Scan a generated product barcode and complete the customer's sale.</p>
    </div>
    <a class="btn btn-light" href="{{ route('admin.sales.index') }}">View Sales</a>
</div>

<div class="pos-layout" data-pos data-scan-url="{{ route('admin.pos.scan') }}" data-checkout-url="{{ route('admin.pos.checkout') }}" data-csrf="{{ csrf_token() }}" data-currency="{{ $adminSetting->currency }}">
    <div>
        <div class="card">
            <label for="barcodeInput">Barcode Scanner</label>
            <div class="search-row">
                <input id="barcodeInput" type="text" placeholder="Scan barcode and press Enter" autofocus autocomplete="off">
                <button class="btn" type="button" data-scan-current>Add Product</button>
                <button class="btn btn-light" type="button" data-clear-cart>Clear</button>
            </div>
            <p id="posMessage" class="muted" aria-live="polite" style="margin-bottom:0;"></p>
        </div>

        <div class="card" style="margin-top:16px;">
            <div class="page-head">
                <div><div class="eyebrow">Current cart</div><h2 style="margin:4px 0;">Invoice Items</h2></div>
                <span class="badge"><span id="cartCount">0</span>&nbsp;products</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Product</th><th>Barcode</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead>
                    <tbody id="cartBody"><tr><td colspan="6" class="empty-state">No items scanned yet.</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>

    <aside class="card pos-summary">
        <div class="eyebrow">Payment</div>
        <h2 style="margin:4px 0 14px;">Bill Summary</h2>
        <div class="summary-row"><span>Subtotal</span><strong>{{ $adminSetting->currency }} <span id="subtotal">0.00</span></strong></div>

        <label for="discount">Discount</label>
        <input id="discount" type="number" step="0.01" min="0" value="0">

        <div class="summary-row total"><span>Total</span><span>{{ $adminSetting->currency }} <span id="grandTotal">0.00</span></span></div>

        <label for="paidAmount">Paid / Received Amount</label>
        <input id="paidAmount" type="number" step="0.01" min="0" value="0">

        <div class="summary-row">
            <span>Change</span>
            <strong class="text-success">{{ $adminSetting->currency }} <span id="changeAmount">0.00</span></strong>
        </div>

        <label for="paymentMethod">Payment Method</label>
        <select id="paymentMethod">
            <option value="cash">Cash</option>
            <option value="card">Card</option>
            <option value="bank">Bank Transfer</option>
        </select>

        <label for="customerId">Customer (optional)</label>
        <select id="customerId">
            <option value="">Walk-in customer</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->phone ? '- '.$customer->phone : '' }}</option>
            @endforeach
        </select>

        <button id="checkoutButton" class="btn" type="button" style="margin-top:18px;width:100%;">Complete Sale</button>
    </aside>
</div>

@push('scripts')
    @vite('resources/js/pos.js')
@endpush
@endsection
