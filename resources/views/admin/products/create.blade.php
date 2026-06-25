@extends('admin.layout')

@section('title', 'Add Product')

@section('content')
<div class="page-head"><div><div class="eyebrow">Catalog</div><h1>Add Product</h1><p>Create item with price, unit, stock and barcode.</p></div></div>
<div class="card"><form action="{{ route('admin.products.store') }}" method="POST">@include('admin.products.form')</form></div>
@endsection
