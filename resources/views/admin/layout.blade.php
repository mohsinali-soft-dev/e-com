<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - E-Com POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-soft: #f1f5f9;
            --text: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --primary: #0f766e;
            --primary-dark: #115e59;
            --primary-soft: #ccfbf1;
            --accent: #f97316;
            --danger: #dc2626;
            --shadow: 0 18px 45px rgba(15, 23, 42, .08);
            --radius: 18px;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--text); font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif; }
        a { color: inherit; }
        .app { min-height: 100vh; }
        .sidebar { background: linear-gradient(160deg, #0f172a 0%, #115e59 100%); color: white; padding: 18px; position: sticky; top: 0; z-index: 20; }
        .brand { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
        .brand-logo { width: 42px; height: 42px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,.14); font-weight: 800; }
        .brand-title { font-weight: 800; letter-spacing: -.03em; }
        .brand-subtitle { color: #99f6e4; font-size: 12px; margin-top: 2px; }
        .nav { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
        .nav a { text-decoration: none; color: #d1fae5; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.08); padding: 11px 12px; border-radius: 14px; font-size: 14px; }
        .nav a:hover, .nav a.active { background: rgba(255,255,255,.18); color: white; border-color: rgba(255,255,255,.2); }
        .main { padding: 18px; width: 100%; max-width: 1500px; }
        .page-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 18px; }
        .eyebrow { color: var(--primary); font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; }
        h1 { margin: 4px 0 4px; font-size: clamp(26px, 7vw, 38px); letter-spacing: -.04em; }
        p { color: var(--muted); }
        .card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 18px; box-shadow: var(--shadow); }
        .grid { display: grid; grid-template-columns: 1fr; gap: 12px; }
        .stat { font-size: 32px; font-weight: 900; letter-spacing: -.05em; margin-top: 8px; }
        .stat-label { color: var(--muted); font-size: 14px; }
        .table-wrap { overflow-x: auto; border-radius: var(--radius); border: 1px solid var(--line); background: white; }
        table { width: 100%; border-collapse: collapse; min-width: 720px; }
        th, td { text-align: left; padding: 14px; border-bottom: 1px solid var(--line); white-space: nowrap; }
        th { color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: .06em; background: var(--surface-soft); }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: var(--primary); color: white; padding: 11px 15px; min-height: 42px; border-radius: 14px; text-decoration: none; border: 0; cursor: pointer; font-weight: 700; }
        .btn:hover { background: var(--primary-dark); }
        .btn-light { background: var(--surface-soft); color: var(--text); }
        .btn-light:hover { background: #e2e8f0; }
        .badge { display: inline-flex; padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 800; background: var(--primary-soft); color: var(--primary-dark); }
        input, select, textarea { width: 100%; padding: 12px 13px; border: 1px solid var(--line); border-radius: 14px; background: white; color: var(--text); outline: none; }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-soft); }
        label { display: block; margin: 14px 0 7px; font-weight: 800; font-size: 14px; }
        .form-grid { display: grid; grid-template-columns: 1fr; gap: 12px; }
        .error { color: var(--danger); font-size: 13px; margin-top: 6px; }
        .alert { margin-bottom: 16px; background: #ecfdf5; border-color: #99f6e4; color: #115e59; }
        .alert-danger { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
        .muted { color: var(--muted); }
        .stack { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .metric { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .metric-icon { width: 46px; height: 46px; display: grid; place-items: center; border-radius: 14px; background: var(--primary-soft); color: var(--primary-dark); font-weight: 900; }
        .product-image { width: 54px; height: 54px; object-fit: cover; border-radius: 12px; border: 1px solid var(--line); background: var(--surface-soft); }
        .image-preview { display: block; width: 150px; height: 150px; margin-top: 12px; object-fit: cover; border-radius: 16px; border: 1px dashed var(--line); background: var(--surface-soft); }
        .summary-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 9px 0; border-bottom: 1px solid var(--line); }
        .summary-row.total { border-bottom: 0; font-size: 20px; font-weight: 900; padding-top: 14px; }
        .pos-layout { display: grid; grid-template-columns: 1fr; gap: 16px; align-items: start; }
        .pos-summary { position: static; }
        .text-success { color: #047857; }
        .text-danger { color: var(--danger); }
        .empty-state { padding: 34px; text-align: center; color: var(--muted); }
        .search-row { display: flex; gap: 10px; flex-wrap: wrap; }
        .search-row input { flex: 1 1 240px; }
        .live-search-status { min-height: 20px; margin: 8px 2px 0; font-size: 13px; color: var(--muted); }
        [data-live-results].is-loading { opacity: .55; pointer-events: none; transition: opacity .15s ease; }
        @media (min-width: 760px) {
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .main { padding: 28px; }
        }
        @media (min-width: 1100px) {
            .app { display: grid; grid-template-columns: 280px 1fr; }
            .sidebar { min-height: 100vh; padding: 28px 18px; }
            .nav { grid-template-columns: 1fr; }
            .grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .pos-layout { grid-template-columns: minmax(0, 1.7fr) minmax(320px, .7fr); }
            .pos-summary { position: sticky; top: 28px; }
        }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="brand-logo">EP</div>
                <div>
                    <div class="brand-title">E-Com POS</div>
                    <div class="brand-subtitle">Retail control center</div>
                </div>
            </div>
        </div>
        <nav class="nav">
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Products</a>
            <a class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">Categories</a>
            <a class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">Brands</a>
            <a class="{{ request()->routeIs('admin.units.*') ? 'active' : '' }}" href="{{ route('admin.units.index') }}">Units</a>
            <a class="{{ request()->routeIs('admin.inventory.low-stock') ? 'active' : '' }}" href="{{ route('admin.inventory.low-stock') }}">Low Stock</a>
            <a class="{{ request()->routeIs('admin.inventory.adjustments*') ? 'active' : '' }}" href="{{ route('admin.inventory.adjustments') }}">Stock Adjustments</a>
            <a class="{{ request()->routeIs('admin.pos.*') ? 'active' : '' }}" href="{{ route('admin.pos.index') }}">POS</a>
            <a class="{{ request()->routeIs('admin.sales.*') ? 'active' : '' }}" href="{{ route('admin.sales.index') }}">Sales</a>
        </nav>
    </aside>
    <main class="main">
        @if(session('success'))
            <div class="card alert">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="card alert alert-danger">
                <strong>Please fix the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</div>
<script>
    (() => {
        const forms = document.querySelectorAll('[data-live-search]');

        forms.forEach(form => {
            const input = form.querySelector('input[name="search"]');
            const results = document.querySelector('[data-live-results]');
            const status = form.querySelector('[data-live-search-status]');
            if (!input || !results) return;

            let timer;
            let controller;

            const loadResults = async url => {
                controller?.abort();
                controller = new AbortController();
                results.classList.add('is-loading');
                if (status) status.textContent = 'Searching...';

                try {
                    const response = await fetch(url, {
                        headers: {'X-Requested-With': 'XMLHttpRequest'},
                        signal: controller.signal
                    });
                    if (!response.ok) throw new Error('Search request failed.');

                    const html = await response.text();
                    const documentResult = new DOMParser().parseFromString(html, 'text/html');
                    const nextResults = documentResult.querySelector('[data-live-results]');
                    if (!nextResults) throw new Error('Search results were not found.');

                    results.innerHTML = nextResults.innerHTML;
                    history.replaceState({}, '', url);
                    if (status) status.textContent = input.value.trim() ? 'Results updated.' : '';
                } catch (error) {
                    if (error.name !== 'AbortError' && status) {
                        status.textContent = 'Could not update results. Please try again.';
                    }
                } finally {
                    results.classList.remove('is-loading');
                }
            };

            const search = () => {
                const url = new URL(form.action || window.location.href);
                const value = input.value.trim();
                if (value) url.searchParams.set('search', value);
                else url.searchParams.delete('search');
                url.searchParams.delete('page');
                loadResults(url);
            };

            input.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(search, 300);
            });

            form.addEventListener('submit', event => {
                event.preventDefault();
                clearTimeout(timer);
                search();
            });

            results.addEventListener('click', event => {
                const link = event.target.closest('a');
                if (!link || !link.closest('nav[role="navigation"]')) return;
                event.preventDefault();
                loadResults(new URL(link.href));
            });
        });
    })();
</script>
</body>
</html>
