@extends('admin.layout')

@section('title', 'Edit Product')

@section('content')
<div class="page-head"><div><div class="eyebrow">Catalog</div><h1>Edit Product</h1><p>Update item details, image, barcode and stock.</p></div><a class="btn" href="{{ route('admin.products.variants.index',$product) }}">Manage Variants</a></div>
<div class="card">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.products.form')
    </form>
</div>
@endsection
