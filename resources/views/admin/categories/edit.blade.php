@extends('admin.layout')

@section('title', 'Edit Category')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Catalog</div>
        <h1>Edit Category</h1>
        <p>Update product grouping details.</p>
    </div>
</div>

<div class="card">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        @include('admin.categories.form')
    </form>
</div>
@endsection
