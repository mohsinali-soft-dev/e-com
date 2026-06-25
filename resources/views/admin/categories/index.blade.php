@extends('admin.layout')

@section('title', 'Categories')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Catalog</div>
        <h1>Categories</h1>
        <p>Manage product groups and sub-groups.</p>
    </div>
    <a class="btn" href="{{ route('admin.categories.create') }}">Add Category</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
        <tr><th>Name</th><th>Parent</th><th>Status</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody>
        @forelse($categories as $category)
            <tr>
                <td><strong>{{ $category->name }}</strong><br><small>{{ $category->slug }}</small></td>
                <td>{{ $category->parent?->name ?? 'Main Category' }}</td>
                <td><span class="badge">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td>{{ $category->created_at->format('d M Y') }}</td>
                <td>
                    <a class="btn btn-light" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this category?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-light" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No categories found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">{{ $categories->links() }}</div>
@endsection
