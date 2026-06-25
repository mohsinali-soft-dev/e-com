@php($setting = \App\Models\Setting::current())
<!doctype html>
<html><head><meta charset="utf-8"><title>{{ $sale->invoice_no }}</title>
<style>body{font:14px Arial;color:#111;margin:30px}.head{display:flex;justify-content:space-between;border-bottom:2px solid #111;padding-bottom:15px}table{width:100%;border-collapse:collapse;margin-top:25px}th,td{padding:10px;border-bottom:1px solid #ddd;text-align:left}.totals{width:360px;margin:20px 0 0 auto}.row{display:flex;justify-content:space-between;padding:6px}.total{font-size:20px;font-weight:bold}@media print{button{display:none}}</style>
</head><body><button onclick="print()">Print</button>
<div class="head"><div>@if($setting->logo_path)<img src="{{ asset('storage/'.$setting->logo_path) }}" style="max-height:60px">@endif<h1>{{ $setting->store_name }}</h1></div><div><strong>{{ $sale->invoice_no }}</strong><br>{{ $sale->created_at->format('d M Y h:i A') }}<br>{{ $sale->customer?->name }}</div></div>
<table><thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead><tbody>@foreach($sale->items as $item)<tr><td>{{ $item->product_name }}</td><td>{{ $item->quantity }}</td><td>{{ $setting->currency }} {{ number_format($item->unit_price, 2) }}</td><td>{{ $setting->currency }} {{ number_format($item->line_total, 2) }}</td></tr>@endforeach</tbody></table>
<div class="totals"><div class="row"><span>Subtotal</span><strong>{{ number_format($sale->subtotal, 2) }}</strong></div><div class="row"><span>Discount</span><strong>{{ number_format($sale->discount_total, 2) }}</strong></div><div class="row total"><span>Total</span><strong>{{ $setting->currency }} {{ number_format($sale->grand_total, 2) }}</strong></div><div class="row"><span>Paid</span><strong>{{ number_format($sale->paid_amount, 2) }}</strong></div><div class="row"><span>Change</span><strong>{{ number_format($sale->change_amount, 2) }}</strong></div></div>
<p style="text-align:center;margin-top:40px">Thank you for shopping with us.</p>
</body></html>
