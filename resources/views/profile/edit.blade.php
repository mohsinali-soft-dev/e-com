@extends('admin.layout')

@section('title', 'My Profile')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Account</div>
        <h1>My Profile</h1>
        <p>Update your account name, email, or password.</p>
    </div>
</div>

<form class="card" method="POST" enctype="multipart/form-data" action="{{ route('profile.update') }}">
    @csrf
    @method('PUT')
    <div class="form-grid">
        <div>
            <label>Profile Picture</label>
            <input type="file" name="profile_photo" accept="image/*">
            @if($user->profile_photo_path)
                <img class="image-preview" src="{{ asset('storage/'.$user->profile_photo_path) }}" alt="{{ $user->name }}">
            @endif
        </div>
        <div>
            <label>Name</label>
            <input name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div>
            <label>New Password</label>
            <input type="password" name="password" placeholder="Leave blank to keep current password">
        </div>
        <div>
            <label>Confirm New Password</label>
            <input type="password" name="password_confirmation">
        </div>
    </div>
    <button class="btn" type="submit" style="margin-top:18px;">Save Profile</button>
</form>
@endsection
