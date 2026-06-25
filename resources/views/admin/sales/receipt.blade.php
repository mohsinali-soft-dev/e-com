@php($setting = \App\Models\Setting::current())
<!doctype html>
<html><head><meta charset="utf-8"><title>Receipt</title>
<style>@page{size:{{ $setting->receipt_width }}mm auto;margin:3mm}body{width:{{ $setting->receipt_width - 8 }}mm;margin:auto;font:12px monospace}.c{text-align:center}.line{border-top:1px dashed #000;margin:8px 0}.row{display:flex;justify-content:space-between;gap:8px}.bold{font-weight:bold}@media print{button{display:none}}</style>
</head><body><button onclick="print()">Print</button>
<div class="c">@if($setting->show_logo_on_receipt && $setting->logo_path)<img src="{{ asset('storage/'.$setting->logo_path) }}" style="max-width:40mm;max-height:20mm">@endif<h2>{{ $setting->store_name }}</h2><div>{{ $sale->invoice_no }}</div><div>{{ $sale->created_at->format('d M Y h:i A') }}</div></div>
<div class="line"></div>
@foreach($sale->items as $item)<div class="bold">{{ $item->product_name }}</div><div class="row"><span>{{ $item->quantity }} × {{ number_format($item->unit_price, 2) }}</span><span>{{ number_format($item->line_total, 2) }}</span></div>@endforeach
<div class="line"></div><div class="row"><span>Subtotal</span><span>{{ number_format($sale->subtotal, 2) }}</span></div><div class="row"><span>Discount</span><span>{{ number_format($sale->discount_total, 2) }}</span></div><div class="row bold"><span>TOTAL</span><span>{{ $setting->currency }} {{ number_format($sale->grand_total, 2) }}</span></div><div class="row"><span>Paid</span><span>{{ number_format($sale->paid_amount, 2) }}</span></div><div class="row"><span>Change</span><span>{{ number_format($sale->change_amount, 2) }}</span></div>
<div class="line"></div><p class="c">Thank you for shopping with us.</p>
<script>window.onload = () => window.print()</script></body></html>
