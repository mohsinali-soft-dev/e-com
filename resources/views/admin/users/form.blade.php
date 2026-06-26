@csrf

<div class="form-grid">
    <div>
        <label>Name</label>
        <input name="name" value="{{ old('name', $user->name ?? '') }}" required>
    </div>
    <div>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
    </div>
    <div>
        <label>Role</label>
        <select name="role">
            @foreach(['admin', 'manager', 'cashier'] as $role)
                <option value="{{ $role }}" @selected(old('role', $user->role ?? 'cashier') === $role)>{{ ucfirst($role) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label>Profile Photo</label>
        <input type="file" name="profile_photo" accept="image/*" data-image-preview-input="#profilePhotoPreview">
        <img
            id="profilePhotoPreview"
            class="image-preview"
            src="{{ isset($user) && $user->profile_photo_path ? asset('storage/'.$user->profile_photo_path) : '' }}"
            alt="Profile photo preview"
            @if(!isset($user) || !$user->profile_photo_path) style="display:none;" @endif
        >
    </div>
    <div>
        <label>Password {{ isset($user) ? '(leave blank to keep current)' : '' }}</label>
        <input type="password" name="password">
    </div>
    <div>
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation">
    </div>
</div>

<label>
    <input style="width:auto" type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true))>
    Active
</label>

<button class="btn">Save User</button>
