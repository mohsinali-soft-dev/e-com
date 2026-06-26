@extends('admin.layout')

@section('title', 'Users')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Security</div>
        <h1>Users & Roles</h1>
    </div>
    <a class="btn" href="{{ route('admin.users.create') }}">Add User</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Photo</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>
                    @if($user->profile_photo_path)
                        <img class="profile-avatar user-index-avatar" src="{{ asset('storage/'.$user->profile_photo_path) }}" alt="{{ $user->name }}">
                    @else
                        <span class="profile-avatar user-index-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td><span class="badge">{{ ucfirst($user->role) }}</span></td>
                <td>{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
                <td>
                    <a class="btn btn-light" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                    @if(! auth()->user()->is($user))
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline" onsubmit="return confirm('Delete user?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-light">Delete</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

{{ $users->links() }}
@endsection
