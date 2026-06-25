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
        .nav a:hover { background: rgba(255,255,255,.16); color: white; }
        .main { padding: 18px; max-width: 1280px; margin: 0 auto; }
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
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.products.index') }}">Products</a>
            <a href="{{ route('admin.categories.index') }}">Categories</a>
            <a href="{{ route('admin.brands.index') }}">Brands</a>
            <a href="{{ route('admin.units.index') }}">Units</a>
            <a href="#">Inventory</a>
            <a href="#">POS</a>
            <a href="#">Orders</a>
        </nav>
    </aside>
    <main class="main">
        @if(session('success'))
            <div class="card alert">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
