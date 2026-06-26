@csrf

<div class="form-grid">
    <div>
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" placeholder="Example: Head & Shoulders 200ml">
        @error('name') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div>
        <label>SKU</label>
        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" placeholder="Example: HS-200ML">
        @error('sku') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div>
        <label>Product Image</label>
        <input id="imageInput" type="file" name="image" accept="image/*">
        @error('image') <div class="error">{{ $message }}</div> @enderror
        <img
            id="imagePreview"
            class="image-preview"
            src="{{ isset($product) && $product->image_path ? asset('storage/'.$product->image_path) : '' }}"
            alt="Product image preview"
            @if(!isset($product) || !$product->image_path) style="display:none;" @endif
        >
    </div>
    <div>
        <label>Sale Type</label>
        <select name="sale_type">
            @foreach(['piece' => 'Piece', 'weight' => 'Weight / KG', 'volume' => 'Volume / Liter'] as $value => $label)
                <option value="{{ $value }}" @selected(old('sale_type', $product->sale_type ?? 'piece') == $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('sale_type') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div>
        <label>Category</label>
        <select name="category_id">
            <option value="">No Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label>Brand</label>
        <select name="brand_id">
            <option value="">No Brand</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label>Unit</label>
        <select name="unit_id">
            @foreach($units as $unit)
                <option value="{{ $unit->id }}" @selected(old('unit_id', $product->unit_id ?? '') == $unit->id)>{{ $unit->name }} ({{ $unit->short_name }})</option>
            @endforeach
        </select>
        @error('unit_id') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div>
        <label>Purchase Price</label>
        <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price ?? 0) }}">
    </div>
    <div>
        <label>Selling Price</label>
        <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $product->selling_price ?? 0) }}">
    </div>
    <div>
        <label>Stock Quantity</label>
        <input id="stockQuantity" type="number" min="0" step="0.01" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}">
    </div>
    <div>
        <label>Low Stock Alert</label>
        <input id="lowStockAlert" type="number" min="0" max="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" step="0.01" name="low_stock_alert" value="{{ old('low_stock_alert', $product->low_stock_alert ?? 0) }}">
        @error('low_stock_alert') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>

<label>Description</label>
<textarea name="description" rows="4" placeholder="Product details">{{ old('description', $product->description ?? '') }}</textarea>

<label style="display:flex;align-items:center;gap:10px;margin-top:18px;">
    <input type="checkbox" name="is_active" value="1" style="width:auto" @checked(old('is_active', $product->is_active ?? true))>
    Active product
</label>

<div style="display:flex;gap:10px;margin-top:22px;flex-wrap:wrap;">
    <button class="btn" type="submit">Save Product</button>
    <a class="btn btn-light" href="{{ route('admin.products.index') }}">Cancel</a>
</div>
