@csrf

<div class="form-grid">
    <div>
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name', $brand->name ?? '') }}" placeholder="Example: Nestle">
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>

<label style="display:flex;align-items:center;gap:10px;margin-top:18px;">
    <input type="checkbox" name="is_active" value="1" style="width:auto" @checked(old('is_active', $brand->is_active ?? true))>
    Active brand
</label>

<div style="display:flex;gap:10px;margin-top:22px;flex-wrap:wrap;">
    <button class="btn" type="submit">Save Brand</button>
    <a class="btn btn-light" href="{{ route('admin.brands.index') }}">Cancel</a>
</div>
