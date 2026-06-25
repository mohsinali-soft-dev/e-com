<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - E-Com POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f6f8; color: #111827; }
        .app { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #111827; color: white; padding: 24px 16px; }
        .sidebar a { display: block; color: #d1d5db; text-decoration: none; padding: 10px 12px; border-radius: 8px; margin-bottom: 6px; }
        .sidebar a:hover { background: #1f2937; color: white; }
        .main { flex: 1; padding: 28px; }
        .card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
        .stat { font-size: 28px; font-weight: bold; margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #e5e7eb; }
        .btn { display: inline-block; background: #111827; color: white; padding: 9px 14px; border-radius: 8px; text-decoration: none; border: 0; cursor: pointer; }
        .btn-light { background: #e5e7eb; color: #111827; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; box-sizing: border-box; }
        label { display: block; margin: 12px 0 6px; font-weight: 600; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .error { color: #dc2626; font-size: 14px; }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <h2>E-Com POS</h2>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.products.index') }}">Products</a>
        <a href="{{ route('admin.categories.index') }}">Categories</a>
        <a href="{{ route('admin.brands.index') }}">Brands</a>
        <a href="{{ route('admin.units.index') }}">Units</a>
    </aside>
    <main class="main">
        @if(session('success'))
            <div class="card" style="margin-bottom:16px;">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
