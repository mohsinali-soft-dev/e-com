@extends('admin.layout')

@section('title', 'Settings')

@section('content')
<div class="page-head">
    <div><div class="eyebrow">Configuration</div><h1>Store Settings</h1></div>
</div>

<form class="card" method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')
    <div class="form-grid">
        <div><label>Store Name</label><input name="store_name" value="{{ old('store_name', $setting->store_name) }}" required></div>
        <div><label>Currency</label><input name="currency" value="{{ old('currency', $setting->currency) }}" required></div>
        <div><label>Tax Rate (%)</label><input type="number" min="0" max="100" step=".01" name="tax_rate" value="{{ old('tax_rate', $setting->tax_rate) }}" required></div>
        <div>
            <label>Receipt Width</label>
            <select name="receipt_width">
                <option value="58" @selected($setting->receipt_width == 58)>58mm</option>
                <option value="80" @selected($setting->receipt_width == 80)>80mm</option>
            </select>
        </div>
        <div>
            <label>Site Logo</label>
            <input type="file" name="logo" accept="image/*">
            @if($setting->logo_path)<img class="image-preview" src="{{ asset('storage/'.$setting->logo_path) }}" alt="Current site logo">@endif
        </div>
        <div>
            <label>Favicon</label>
            <input type="file" name="favicon" accept="image/*">
            @if($setting->favicon_path)<img class="image-preview" src="{{ asset('storage/'.$setting->favicon_path) }}" alt="Current favicon">@endif
        </div>
    </div>
    <label><input style="width:auto" type="checkbox" name="show_logo_on_receipt" value="1" @checked($setting->show_logo_on_receipt)> Show logo on receipt</label>
    <button class="btn">Save Settings</button>
</form>
@endsection
