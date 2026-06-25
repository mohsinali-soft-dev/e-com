@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <h1>Admin Dashboard</h1>
    <p>Store overview and product foundation stats.</p>

    <div class="grid">
        <div class="card"><div>Total Products</div><div class="stat"><?php echo $totalProducts; ?></div></div>
        <div class="card"><div>Categories</div><div class="stat"><?php echo $totalCategories; ?></div></div>
        <div class="card"><div>Brands</div><div class="stat"><?php echo $totalBrands; ?></div></div>
        <div class="card"><div>Barcodes</div><div class="stat"><?php echo $totalBarcodes; ?></div></div>
    </div>
@endsection
