@extends('admin.layout')

@section('title', 'Add Product')

@section('content')
<div class="page-head"><div><div class="eyebrow">Catalog</div><h1>Add Product</h1><p>Create item with price, unit, stock and auto barcode.</p></div></div>
<div class="card"><form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">@include('admin.products.form')</form></div>
@endsection
