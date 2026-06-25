@csrf

<div class="form-grid">
    <div>
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name', $unit->name ?? '') }}" placeholder="Example: Kilogram">
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div>
        <label>Short Name</label>
        <input type="text" name="short_name" value="{{ old('short_name', $unit->short_name ?? '') }}" placeholder="Example: kg">
        @error('short_name') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div>
        <label>Type</label>
        <select name="type">
            @foreach(['piece' => 'Piece', 'weight' => 'Weight', 'volume' => 'Volume', 'length' => 'Length'] as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $unit->type ?? 'piece') == $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('type') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div>
        <label>Decimal Places</label>
        <select name="decimal_places">
            @foreach([0,1,2,3] as $decimal)
                <option value="{{ $decimal }}" @selected(old('decimal_places', $unit->decimal_places ?? 0) == $decimal)>{{ $decimal }}</option>
            @endforeach
        </select>
        @error('decimal_places') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>

<label style="display:flex;align-items:center;gap:10px;margin-top:18px;">
    <input type="checkbox" name="is_active" value="1" style="width:auto" @checked(old('is_active', $unit->is_active ?? true))>
    Active unit
</label>

<div style="display:flex;gap:10px;margin-top:22px;flex-wrap:wrap;">
    <button class="btn" type="submit">Save Unit</button>
    <a class="btn btn-light" href="{{ route('admin.units.index') }}">Cancel</a>
</div>
