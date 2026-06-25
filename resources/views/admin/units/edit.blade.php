@extends('admin.layout')

@section('title', 'Edit Unit')

@section('content')
<div class="page-head"><div><div class="eyebrow">Catalog</div><h1>Edit Unit</h1><p>Update selling unit.</p></div></div>
<div class="card">
    <form action="{{ route('admin.units.update', $unit) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.units.form')
    </form>
</div>
@endsection
