@csrf

<div class="form-grid">
    <div>
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" placeholder="Example: Shampoo">
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div>
        <label>Parent Category</label>
        <select name="parent_id">
            <option value="">Main Category</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id ?? '') == $parent->id)>{{ $parent->name }}</option>
            @endforeach
        </select>
        @error('parent_id') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>

<label style="display:flex;align-items:center;gap:10px;margin-top:18px;">
    <input type="checkbox" name="is_active" value="1" style="width:auto" @checked(old('is_active', $category->is_active ?? true))>
    Active category
</label>

<div style="display:flex;gap:10px;margin-top:22px;flex-wrap:wrap;">
    <button class="btn" type="submit">Save Category</button>
    <a class="btn btn-light" href="{{ route('admin.categories.index') }}">Cancel</a>
</div>
