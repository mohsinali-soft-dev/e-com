@extends('admin.layout')

@section('title', 'Add Category')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Catalog</div>
        <h1>Add Category</h1>
        <p>Create product grouping for the store.</p>
    </div>
</div>

<div class="card">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @include('admin.categories.form')
    </form>
</div>
@endsection
