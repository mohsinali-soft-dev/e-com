@extends('admin.layout')

@section('title', 'Add Unit')

@section('content')
<div class="page-head"><div><div class="eyebrow">Catalog</div><h1>Add Unit</h1><p>Create selling unit for products.</p></div></div>
<div class="card"><form action="{{ route('admin.units.store') }}" method="POST">@include('admin.units.form')</form></div>
@endsection
