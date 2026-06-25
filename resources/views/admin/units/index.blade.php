@extends('admin.layout')

@section('title', 'Units')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Catalog</div>
        <h1>Units</h1>
        <p>Manage piece, kg, gram, liter and other selling units.</p>
    </div>
    <a class="btn" href="{{ route('admin.units.create') }}">Add Unit</a>
</div>

<div class="table-wrap">
    <table>
        <thead><tr><th>Name</th><th>Short</th><th>Type</th><th>Decimals</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($units as $unit)
            <tr>
                <td><strong>{{ $unit->name }}</strong></td>
                <td>{{ $unit->short_name }}</td>
                <td>{{ ucfirst($unit->type) }}</td>
                <td>{{ $unit->decimal_places }}</td>
                <td><span class="badge">{{ $unit->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td>
                    <a class="btn btn-light" href="{{ route('admin.units.edit', $unit) }}">Edit</a>
                    <form action="{{ route('admin.units.destroy', $unit) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this unit?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-light" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">No units found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:16px;">{{ $units->links() }}</div>
@endsection
