@php($store = \App\Models\Setting::current())
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', $store->store_name)</title>
    @if($store->favicon_path)<link rel="icon" href="{{ asset('storage/'.$store->favicon_path) }}">@endif
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        :root{--p:#0f766e;--d:#115e59;--bg:#f8fafc;--line:#e2e8f0;--muted:#64748b}*{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:var(--bg);font-family:Inter,system-ui;color:#0f172a}body.menu-open{overflow:hidden}a{text-decoration:none;color:inherit}.container{width:min(1180px,calc(100% - 32px));margin:auto}.top{background:rgba(15,23,42,.97);color:white;position:sticky;top:0;z-index:40;border-bottom:1px solid #ffffff12;box-shadow:0 8px 30px rgba(15,23,42,.12);backdrop-filter:blur(16px)}.nav{min-height:76px;display:flex;align-items:center;justify-content:space-between;gap:24px}.site-brand{display:flex;align-items:center;gap:11px;min-width:0;font-weight:900;font-size:20px;letter-spacing:-.02em}.site-brand>span:last-child{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.site-logo,.site-logo-fallback{width:44px;height:44px;flex:0 0 auto;border-radius:13px;background:#ffffff18}.site-logo{object-fit:contain}.site-logo-fallback{display:grid;place-items:center;color:#99f6e4;font-size:14px}.links{display:flex;gap:5px;align-items:center}.links a{display:flex;align-items:center;gap:8px;padding:10px 12px;border-radius:11px;color:#cbd5e1;font-size:14px;font-weight:700;transition:background .2s,color .2s}.links a:hover,.links a.active{background:#ffffff14;color:#fff}.links .staff-link{margin-left:5px;border:1px solid #ffffff1f;background:#ffffff0d}.cart-icon{width:19px;height:19px}.cart-count{min-width:21px;height:21px;display:inline-grid;place-items:center;padding:0 6px;border-radius:999px;background:#14b8a6;color:#042f2e;font-size:11px;font-weight:900}.menu-toggle{display:none;width:44px;height:44px;flex:0 0 auto;place-items:center;border:1px solid #ffffff24;border-radius:13px;background:#ffffff12;color:#fff;cursor:pointer}.menu-toggle:hover{background:#ffffff20}.menu-toggle svg{width:23px;height:23px}.menu-toggle .icon-close{display:none}.menu-toggle[aria-expanded=true] .icon-menu{display:none}.menu-toggle[aria-expanded=true] .icon-close{display:block}.menu-backdrop{display:none}.hero{padding:64px 0;background:linear-gradient(135deg,#ccfbf1,#fff7ed)}h1{font-size:clamp(32px,6vw,58px);margin:0 0 12px}.btn{display:inline-flex;padding:11px 16px;border:0;border-radius:12px;background:var(--p);color:white;font-weight:700;cursor:pointer}.btn-light{background:#e2e8f0;color:#0f172a}.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:18px}.card{background:white;border:1px solid var(--line);border-radius:18px;padding:18px;box-shadow:0 12px 30px #0f172a0d}.product-img{width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:14px;background:#f1f5f9}.section{padding:34px 0}.price{font-size:22px;font-weight:900;color:var(--d)}input,select,textarea{width:100%;padding:12px;border:1px solid var(--line);border-radius:12px}.form-grid{display:grid;grid-template-columns:1fr;gap:14px}.alert{padding:14px;border-radius:12px;background:#ecfdf5;color:#065f46;margin:16px 0}.error{background:#fef2f2;color:#991b1b}.table-wrap{overflow:auto;background:white;border:1px solid var(--line);border-radius:16px}table{width:100%;border-collapse:collapse;min-width:680px}th,td{padding:13px;border-bottom:1px solid var(--line);text-align:left}
        @media(max-width:800px){.nav{min-height:68px}.site-brand{font-size:18px}.site-logo,.site-logo-fallback{width:40px;height:40px}.menu-toggle{display:grid}.links{position:fixed;left:16px;right:16px;top:78px;z-index:42;display:grid;gap:5px;padding:12px;border:1px solid #ffffff1c;border-radius:18px;background:#0f172a;box-shadow:0 24px 65px rgba(2,6,23,.42);opacity:0;visibility:hidden;transform:translateY(-12px) scale(.98);transform-origin:top;transition:opacity .2s,visibility .2s,transform .2s}.links.is-open{opacity:1;visibility:visible;transform:translateY(0) scale(1)}.links a{min-height:48px;padding:12px 14px;font-size:15px}.links .staff-link{margin:5px 0 0;border-color:#ffffff24;background:#ffffff12}.cart-count{margin-left:auto}.menu-backdrop{position:fixed;inset:68px 0 0;z-index:39;display:block;background:rgba(2,6,23,.48);backdrop-filter:blur(2px);opacity:0;visibility:hidden;transition:opacity .2s,visibility .2s}.menu-backdrop.is-visible{opacity:1;visibility:visible}}
        @media(min-width:760px){.form-grid{grid-template-columns:1fr 1fr}}
        @media(max-width:420px){.container{width:min(100% - 24px,1180px)}.site-brand>span:last-child{max-width:180px}.links{left:12px;right:12px}}
        @media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}.links,.menu-backdrop{transition:none}}
    </style>
</head>
<body>
<header class="top">
    <div class="container nav">
        <a class="site-brand" href="{{ route('home') }}">
            @if($store->logo_path)
                <img class="site-logo" src="{{ asset('storage/'.$store->logo_path) }}" alt="{{ $store->store_name }}">
            @else
                <span class="site-logo-fallback" aria-hidden="true">{{ strtoupper(substr($store->store_name, 0, 2)) }}</span>
            @endif
            <span>{{ $store->store_name }}</span>
        </a>
        <button class="menu-toggle" type="button" aria-label="Open navigation" aria-controls="store-navigation" aria-expanded="false" data-menu-toggle>
            <svg class="icon-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg class="icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <nav class="links" id="store-navigation" aria-label="Store navigation">
            <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
            <a class="{{ request()->routeIs('shop.*') ? 'active' : '' }}" href="{{ route('shop.index') }}">Shop</a>
            <a class="{{ request()->routeIs('orders.track*') ? 'active' : '' }}" href="{{ route('orders.track') }}">My Orders</a>
            <a class="{{ request()->routeIs('cart.*', 'checkout.*') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                <svg class="cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="20" r="1"/><circle cx="19" cy="20" r="1"/><path d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 8H6"/></svg>
                <span>Cart</span>
                <span class="cart-count" aria-label="{{ count(session('cart', [])) }} items in cart">{{ count(session('cart', [])) }}</span>
            </a>
            @auth
                <a class="staff-link" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
            @else
                <a class="staff-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Staff Login</a>
            @endauth
        </nav>
    </div>
    <div class="menu-backdrop" data-menu-backdrop aria-hidden="true"></div>
</header>
<main>
    @if(session('success'))<div class="container alert">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="container alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
</main>
<footer class="section"><div class="container" style="color:var(--muted)">&copy; {{ date('Y') }} {{ $store->store_name }}. All rights reserved.</div></footer>
<script>
(() => {
    const toggle = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('#store-navigation');
    const backdrop = document.querySelector('[data-menu-backdrop]');
    const mobile = () => window.matchMedia('(max-width: 800px)').matches;
    const setMenu = open => {
        if (!toggle || !menu || !backdrop) return;
        menu.classList.toggle('is-open', open);
        backdrop.classList.toggle('is-visible', open);
        document.body.classList.toggle('menu-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
        backdrop.setAttribute('aria-hidden', String(!open));
    };

    toggle?.addEventListener('click', () => setMenu(!menu.classList.contains('is-open')));
    backdrop?.addEventListener('click', () => setMenu(false));
    menu?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
        if (mobile()) setMenu(false);
    }));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && menu?.classList.contains('is-open')) {
            setMenu(false);
            toggle?.focus();
        }
    });
    window.addEventListener('resize', () => {
        if (!mobile()) setMenu(false);
    });
})();
</script>
</body>
</html>
