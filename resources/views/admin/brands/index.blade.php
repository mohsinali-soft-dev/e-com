@extends('admin.layout')

@section('title', 'Brands')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Catalog</div>
        <h1>Brands</h1>
        <p>Manage manufacturers and product companies.</p>
    </div>
    <a class="btn" href="{{ route('admin.brands.create') }}">Add Brand</a>
</div>

<div class="table-wrap">
    <table>
        <thead><tr><th>Name</th><th>Slug</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($brands as $brand)
            <tr>
                <td><strong>{{ $brand->name }}</strong></td>
                <td>{{ $brand->slug }}</td>
                <td><span class="badge">{{ $brand->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td>{{ $brand->created_at->format('d M Y') }}</td>
                <td>
                    <a class="btn btn-light" href="{{ route('admin.brands.edit', $brand) }}">Edit</a>
                    <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this brand?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-light" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No brands found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:16px;">{{ $brands->links() }}</div>
@endsection
