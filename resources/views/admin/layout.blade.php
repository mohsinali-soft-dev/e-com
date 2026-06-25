<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php($adminSetting = \App\Models\Setting::current())
    <title>@yield('title', 'Admin') - {{ $adminSetting->store_name }}</title>
    @if($adminSetting->favicon_path)<link rel="icon" href="{{ asset('storage/'.$adminSetting->favicon_path) }}">@endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{--bg:#f8fafc;--surface:#fff;--surface-soft:#f1f5f9;--text:#0f172a;--muted:#64748b;--line:#e2e8f0;--primary:#0f766e;--primary-dark:#115e59;--primary-soft:#ccfbf1;--danger:#dc2626;--shadow:0 18px 45px rgba(15,23,42,.08);--radius:18px}
        *{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--text);font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif}body.sidebar-open{overflow:hidden}a{color:inherit}.app{min-height:100vh}.workspace{min-width:0}.sidebar{background:linear-gradient(160deg,#0f172a 0%,#115e59 100%);color:#fff;padding:22px 18px;position:fixed;inset:0 auto 0 0;width:min(86vw,300px);z-index:50;overflow-y:auto;transform:translateX(-105%);transition:transform .28s cubic-bezier(.4,0,.2,1);box-shadow:24px 0 60px rgba(15,23,42,.28)}.sidebar.is-open{transform:translateX(0)}.sidebar-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}.brand{display:flex;align-items:center;gap:12px;margin-bottom:22px}.brand-logo{width:42px;height:42px;border-radius:14px;display:grid;place-items:center;background:#ffffff24;font-weight:800}.brand-logo img{width:100%;height:100%;object-fit:contain;border-radius:14px}.brand-title{font-weight:800}.brand-subtitle{color:#99f6e4;font-size:12px;margin-top:2px}.sidebar-close{width:38px;height:38px;flex:0 0 auto;display:grid;place-items:center;border:1px solid #ffffff24;border-radius:12px;background:#ffffff14;color:#fff;cursor:pointer}.sidebar-close:hover{background:#ffffff24}.sidebar-close svg,.menu-toggle svg{width:22px;height:22px}.nav{display:grid;grid-template-columns:1fr;gap:8px}.nav a{text-decoration:none;color:#d1fae5;background:#ffffff14;border:1px solid #ffffff14;padding:11px 12px;border-radius:14px;font-size:14px}.nav a:hover,.nav a.active{background:#ffffff2e;color:#fff;border-color:#ffffff33}.sidebar-backdrop{position:fixed;inset:0;z-index:45;background:rgba(15,23,42,.56);backdrop-filter:blur(3px);opacity:0;visibility:hidden;transition:opacity .25s ease,visibility .25s ease}.sidebar-backdrop.is-visible{opacity:1;visibility:visible}
        .topbar{min-height:70px;background:#fff;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:14px;padding:12px 18px;position:sticky;top:0;z-index:15}.topbar-start{display:flex;align-items:center;gap:10px;min-width:0}.menu-toggle{width:42px;height:42px;flex:0 0 auto;display:grid;place-items:center;border:1px solid var(--line);border-radius:13px;background:var(--surface-soft);color:var(--text);cursor:pointer;transition:background .2s ease,transform .2s ease}.menu-toggle:hover{background:#e2e8f0}.menu-toggle:active{transform:scale(.96)}.topbar-brand{display:flex;align-items:center;gap:10px;min-width:0;text-decoration:none;font-weight:900}.topbar-brand span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.topbar-logo{width:40px;height:40px;object-fit:contain;border-radius:11px;background:var(--surface-soft)}.topbar-actions{display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-wrap:wrap}.profile-menu{position:relative}.profile-menu summary{list-style:none;cursor:pointer;display:flex;align-items:center;gap:10px;padding:8px 11px;border-radius:13px;background:var(--surface-soft)}.profile-menu summary::-webkit-details-marker{display:none}.profile-avatar{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;background:var(--primary);color:#fff;font-weight:900}.profile-dropdown{position:absolute;right:0;top:calc(100% + 8px);width:210px;padding:10px;background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:var(--shadow)}.profile-dropdown a,.profile-dropdown button{width:100%;display:block;text-align:left;padding:10px 12px;border:0;border-radius:10px;background:transparent;text-decoration:none;cursor:pointer;color:var(--text)}.profile-dropdown a:hover,.profile-dropdown button:hover{background:var(--surface-soft)}
        .main{padding:18px;width:100%;max-width:1500px}.page-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:18px}.eyebrow{color:var(--primary);font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.08em}h1{margin:4px 0;font-size:clamp(26px,7vw,38px);letter-spacing:-.04em}p{color:var(--muted)}.card{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius);padding:18px;box-shadow:var(--shadow)}.grid{display:grid;grid-template-columns:1fr;gap:12px}.stat{font-size:32px;font-weight:900;letter-spacing:-.05em;margin-top:8px}.stat-label,.muted{color:var(--muted)}.table-wrap{overflow-x:auto;border-radius:var(--radius);border:1px solid var(--line);background:#fff}table{width:100%;border-collapse:collapse;min-width:720px}th,td{text-align:left;padding:14px;border-bottom:1px solid var(--line);white-space:nowrap}th{color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:.06em;background:var(--surface-soft)}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;background:var(--primary);color:#fff;padding:11px 15px;min-height:42px;border-radius:14px;text-decoration:none;border:0;cursor:pointer;font-weight:700}.btn:hover{background:var(--primary-dark)}.btn-light{background:var(--surface-soft);color:var(--text)}.btn-light:hover{background:#e2e8f0}.badge{display:inline-flex;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:800;background:var(--primary-soft);color:var(--primary-dark)}input,select,textarea{width:100%;padding:12px 13px;border:1px solid var(--line);border-radius:14px;background:#fff;color:var(--text);outline:none}input:focus,select:focus,textarea:focus{border-color:var(--primary);box-shadow:0 0 0 4px var(--primary-soft)}label{display:block;margin:14px 0 7px;font-weight:800;font-size:14px}.form-grid{display:grid;grid-template-columns:1fr;gap:12px}.error,.text-danger{color:var(--danger)}.alert{margin-bottom:16px;background:#ecfdf5;border-color:#99f6e4;color:#115e59}.alert-danger{background:#fef2f2;border-color:#fecaca;color:#991b1b}.stack{display:flex;gap:10px;align-items:center;flex-wrap:wrap}.product-image{width:54px;height:54px;object-fit:cover;border-radius:12px;border:1px solid var(--line);background:var(--surface-soft)}.image-preview{display:block;width:150px;height:150px;margin-top:12px;object-fit:contain;border-radius:16px;border:1px dashed var(--line);background:var(--surface-soft)}.summary-row{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:9px 0;border-bottom:1px solid var(--line)}.summary-row.total{border-bottom:0;font-size:20px;font-weight:900;padding-top:14px}.pos-layout{display:grid;grid-template-columns:1fr;gap:16px;align-items:start}.pos-summary{position:static}.text-success{color:#047857}.empty-state{padding:34px;text-align:center;color:var(--muted)}.search-row{display:flex;gap:10px;flex-wrap:wrap}.search-row input{flex:1 1 240px}.live-search-status{min-height:20px;margin:8px 2px 0;font-size:13px;color:var(--muted)}[data-live-results].is-loading{opacity:.55;pointer-events:none;transition:opacity .15s ease}
        @media(min-width:760px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}.form-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.main{padding:28px}}
        @media(min-width:1100px){body.sidebar-open{overflow:auto}.app{display:grid;grid-template-columns:280px 1fr}.sidebar{position:sticky;top:0;width:auto;min-height:100vh;padding:28px 18px;transform:none;box-shadow:none}.sidebar-close,.menu-toggle,.sidebar-backdrop{display:none}.nav{grid-template-columns:1fr}.grid{grid-template-columns:repeat(4,minmax(0,1fr))}.pos-layout{grid-template-columns:minmax(0,1.7fr) minmax(320px,.7fr)}.pos-summary{position:sticky;top:88px}}
        @media(max-width:759px){.topbar-user-copy{display:none}.topbar{padding:10px 12px}.topbar-brand span{max-width:130px}.topbar-actions .btn{display:none}.profile-menu summary{padding:5px}.main{padding:14px}}
        @media(prefers-reduced-motion:reduce){.sidebar,.sidebar-backdrop,.menu-toggle{transition:none}}
    </style>
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
<script>
(() => {
    const sidebar = document.querySelector('#admin-sidebar');
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const closeButton = document.querySelector('[data-sidebar-close]');
    const backdrop = document.querySelector('[data-sidebar-backdrop]');
    const mobileSidebar = () => window.matchMedia('(max-width: 1099px)').matches;
    const setSidebar = open => {
        if (!sidebar || !toggle || !backdrop) return;
        sidebar.classList.toggle('is-open', open);
        backdrop.classList.toggle('is-visible', open);
        document.body.classList.toggle('sidebar-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        backdrop.setAttribute('aria-hidden', String(!open));
        if (open) closeButton?.focus();
    };
    toggle?.addEventListener('click', () => setSidebar(!sidebar.classList.contains('is-open')));
    closeButton?.addEventListener('click', () => { setSidebar(false); toggle?.focus(); });
    backdrop?.addEventListener('click', () => setSidebar(false));
    sidebar?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
        if (mobileSidebar()) setSidebar(false);
    }));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && sidebar?.classList.contains('is-open')) {
            setSidebar(false);
            toggle?.focus();
        }
    });
    window.addEventListener('resize', () => {
        if (!mobileSidebar()) setSidebar(false);
    });

    document.querySelectorAll('[data-live-search]').forEach(form => {
        const input = form.querySelector('input[name="search"]');
        const results = document.querySelector('[data-live-results]');
        const status = form.querySelector('[data-live-search-status]');
        if (!input || !results) return;
        let timer, controller;
        const loadResults = async url => {
            controller?.abort(); controller = new AbortController(); results.classList.add('is-loading');
            if(status) status.textContent='Searching...';
            try {
                const response=await fetch(url,{headers:{'X-Requested-With':'XMLHttpRequest'},signal:controller.signal});
                if(!response.ok) throw new Error();
                const next=new DOMParser().parseFromString(await response.text(),'text/html').querySelector('[data-live-results]');
                if(!next) throw new Error();
                results.innerHTML=next.innerHTML; history.replaceState({},'',url);
                if(status) status.textContent=input.value.trim()?'Results updated.':'';
            } catch(error) { if(error.name!=='AbortError'&&status) status.textContent='Could not update results. Please try again.'; }
            finally { results.classList.remove('is-loading'); }
        };
        const search=()=>{const url=new URL(form.action||window.location.href),value=input.value.trim();value?url.searchParams.set('search',value):url.searchParams.delete('search');url.searchParams.delete('page');loadResults(url)};
        input.addEventListener('input',()=>{clearTimeout(timer);timer=setTimeout(search,300)});
        form.addEventListener('submit',event=>{event.preventDefault();clearTimeout(timer);search()});
        results.addEventListener('click',event=>{const link=event.target.closest('a');if(!link||!link.closest('nav[role="navigation"]'))return;event.preventDefault();loadResults(new URL(link.href))});
    });
})();
</script>
</body>
</html>
