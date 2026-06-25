@extends('admin.layout')

@section('title', 'Edit Brand')

@section('content')
<div class="page-head"><div><div class="eyebrow">Catalog</div><h1>Edit Brand</h1><p>Update product brand.</p></div></div>
<div class="card">
    <form action="{{ route('admin.brands.update', $brand) }}" method="POST">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        @include('admin.brands.form')
    </form>
</div>
@endsection
