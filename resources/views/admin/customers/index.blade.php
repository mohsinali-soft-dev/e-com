@extends('admin.layout')
@section('title', 'Customers')
@section('content')
<div class="page-head"><div><div class="eyebrow">CRM</div><h1>Customers</h1><p>Customer profiles and purchase history.</p></div><a class="btn" href="{{ route('admin.customers.create') }}">Add Customer</a></div>
<form class="card search-row" data-live-search method="GET"><input name="search" value="{{ request('search') }}" placeholder="Search name, phone or email"><button class="btn">Search</button><span data-live-search-status></span></form>
<div data-live-results style="margin-top:16px"><div class="table-wrap"><table><thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Orders</th><th>POS Sales</th><th></th></tr></thead><tbody>
@forelse($customers as $customer)
<tr><td><strong>{{ $customer->name }}</strong></td><td>{{ $customer->phone }}</td><td>{{ $customer->email }}</td><td>{{ $customer->orders_count }}</td><td>{{ $customer->sales_count }}</td><td><a class="btn btn-light" href="{{ route('admin.customers.show', $customer) }}">History</a> <a class="btn btn-light" href="{{ route('admin.customers.edit', $customer) }}">Edit</a> <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" style="display:inline" onsubmit="return confirm('Delete customer?')">@csrf @method('DELETE')<button class="btn btn-light">Delete</button></form></td></tr>
@empty<tr><td colspan="6">No customers found.</td></tr>@endforelse
</tbody></table></div>{{ $customers->links() }}</div>
@endsection
