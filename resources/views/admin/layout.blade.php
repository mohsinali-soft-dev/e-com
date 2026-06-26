<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php($adminSetting = \App\Models\Setting::current())
    <title>@yield('title', 'Admin') - {{ $adminSetting->store_name }}</title>
    @if($adminSetting->favicon_path)<link rel="icon" href="{{ asset('storage/'.$adminSetting->favicon_path) }}">@endif
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js', 'resources/js/admin.js'])
</head>
<body>
<div class="app">
    <aside class="sidebar" id="admin-sidebar" aria-label="Admin navigation">
        <div class="sidebar-head">
            <div class="brand">
                <div class="brand-logo">@if($adminSetting->logo_path)<img src="{{ asset('storage/'.$adminSetting->logo_path) }}" alt="{{ $adminSetting->store_name }}">@else EP @endif</div>
                <div><div class="brand-title">{{ $adminSetting->store_name }}</div><div class="brand-subtitle">Retail control center</div></div>
            </div>
            <button class="sidebar-close" type="button" aria-label="Close navigation" data-sidebar-close>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="nav">
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
            @if(auth()->user()->hasRole('admin','manager'))
                <a class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Products</a>
                <a class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">Categories</a>
                <a class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">Brands</a>
                <a class="{{ request()->routeIs('admin.units.*') ? 'active' : '' }}" href="{{ route('admin.units.index') }}">Units</a>
                <a class="{{ request()->routeIs('admin.inventory.low-stock') ? 'active' : '' }}" href="{{ route('admin.inventory.low-stock') }}">Low Stock</a>
                <a class="{{ request()->routeIs('admin.inventory.adjustments*') ? 'active' : '' }}" href="{{ route('admin.inventory.adjustments') }}">Stock Adjustments</a>
            @endif
            <a class="{{ request()->routeIs('admin.pos.*') ? 'active' : '' }}" href="{{ route('admin.pos.index') }}">POS</a>
            <a class="{{ request()->routeIs('admin.sales.*') ? 'active' : '' }}" href="{{ route('admin.sales.index') }}">Sales</a>
            <a class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">Online Orders</a>
            @if(auth()->user()->hasRole('admin','manager'))
                <a class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">Customers</a>
                <a class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">Coupons</a>
            @endif
            <a class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">Reports</a>
            @if(auth()->user()->hasRole('admin'))
                <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Users</a>
                <a class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">Settings</a>
            @endif
        </nav>
    </aside>
    <div class="sidebar-backdrop" data-sidebar-backdrop aria-hidden="true"></div>
    <section class="workspace">
        <header class="topbar">
            <div class="topbar-start">
                <button class="menu-toggle" type="button" aria-label="Open navigation" aria-controls="admin-sidebar" aria-expanded="false" data-sidebar-toggle>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a class="topbar-brand" href="{{ route('admin.dashboard') }}">
                    @if($adminSetting->logo_path)<img class="topbar-logo" src="{{ asset('storage/'.$adminSetting->logo_path) }}" alt="{{ $adminSetting->store_name }}">@endif
                    <span>{{ $adminSetting->store_name }}</span>
                </a>
            </div>
            <div class="topbar-actions">
                <a class="btn btn-light" href="{{ route('home') }}">View Website</a>
                <details class="profile-menu">
                    <summary>
                        @if(auth()->user()->profile_photo_path)
                            <img class="profile-avatar" src="{{ asset('storage/'.auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" style="object-fit:cover;">
                        @else
                            <span class="profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        @endif
                        <span class="topbar-user-copy"><strong>{{ auth()->user()->name }}</strong></span>
                    </summary>
                    <div class="profile-dropdown">
                        <a href="{{ route('profile.edit') }}">View Profile</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Logout</button></form>
                    </div>
                </details>
            </div>
        </header>
        <main class="main">
            @if(session('success'))<div class="card alert">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="card alert alert-danger"><strong>Please fix the following:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
    </section>
</div>
@stack('scripts')
</body>
</html>
