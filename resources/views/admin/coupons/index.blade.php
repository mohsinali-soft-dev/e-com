@extends('admin.layout')
@section('title', 'Coupons')
@section('content')
<div class="page-head"><div><div class="eyebrow">Promotions</div><h1>Coupons</h1></div><a class="btn" href="{{ route('admin.coupons.create') }}">Add Coupon</a></div>
<div class="table-wrap"><table><thead><tr><th>Code</th><th>Type</th><th>Value</th><th>Minimum</th><th>Expiry</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($coupons as $coupon)
<tr><td><strong>{{ $coupon->code }}</strong></td><td>{{ ucfirst($coupon->type) }}</td><td>{{ $coupon->type === 'percentage' ? $coupon->value.'%' : number_format($coupon->value, 2) }}</td><td>{{ number_format($coupon->minimum_order, 2) }}</td><td>{{ $coupon->expires_at?->format('d M Y') ?? 'Never' }}</td><td><span class="badge">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</span></td><td><a class="btn btn-light" href="{{ route('admin.coupons.edit', $coupon) }}">Edit</a> <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" style="display:inline" onsubmit="return confirm('Delete coupon?')">@csrf @method('DELETE')<button class="btn btn-light">Delete</button></form></td></tr>
@endforeach
</tbody></table></div>{{ $coupons->links() }}
@endsection
