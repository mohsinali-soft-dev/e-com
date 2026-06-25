@extends('admin.layout')

@section('title', 'Add Brand')

@section('content')
<div class="page-head"><div><div class="eyebrow">Catalog</div><h1>Add Brand</h1><p>Create a product brand.</p></div></div>
<div class="card"><form action="{{ route('admin.brands.store') }}" method="POST">@include('admin.brands.form')</form></div>
@endsection
