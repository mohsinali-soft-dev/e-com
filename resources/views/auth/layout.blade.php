@php($setting = \App\Models\Setting::current())
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ $setting->store_name }}</title>
    @if($setting->favicon_path)<link rel="icon" href="{{ asset('storage/'.$setting->favicon_path) }}">@endif
    @vite(['resources/css/app.css'])
    <style>
        :root{--primary:#0f766e;--primary-dark:#115e59;--primary-soft:#ccfbf1;--text:#0f172a;--muted:#64748b;--line:#dbe4ee;--danger:#dc2626}
        *{box-sizing:border-box}body{margin:0;color:var(--text);font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:#f8fafc}.auth-page{min-height:100vh;display:grid;grid-template-columns:minmax(0,1fr)}.auth-art{display:none;position:relative;overflow:hidden;padding:52px;color:#fff;background:linear-gradient(145deg,#0f172a 0%,#0f766e 70%,#14b8a6 100%)}.auth-art:before,.auth-art:after{content:"";position:absolute;border-radius:50%;background:#ffffff0d}.auth-art:before{width:420px;height:420px;right:-170px;top:-130px}.auth-art:after{width:300px;height:300px;left:-110px;bottom:-120px}.art-content{position:relative;z-index:1;display:flex;height:100%;max-width:520px;flex-direction:column;justify-content:space-between}.art-brand{display:flex;align-items:center;gap:14px;font-size:20px;font-weight:900}.art-logo,.auth-logo{display:grid;place-items:center;overflow:hidden;background:#ffffff20}.art-logo{width:52px;height:52px;border-radius:16px}.art-logo img,.auth-logo img{width:100%;height:100%;object-fit:contain}.art-copy h2{max-width:480px;margin:0;font-size:clamp(38px,4vw,60px);line-height:1.04;letter-spacing:-.055em}.art-copy p{max-width:440px;color:#ccfbf1;font-size:17px;line-height:1.7}.art-note{color:#99f6e4;font-size:13px}.auth-main{display:grid;place-items:center;padding:28px 18px}.auth-card{width:min(100%,460px);padding:34px;border:1px solid #e2e8f0;border-radius:26px;background:#fff;box-shadow:0 28px 70px rgba(15,23,42,.11)}.auth-logo{width:64px;height:64px;margin-bottom:22px;border-radius:18px;background:var(--primary-soft);color:var(--primary-dark);font-weight:900;font-size:20px}.eyebrow{margin:0 0 7px;color:var(--primary);font-size:12px;font-weight:900;letter-spacing:.11em;text-transform:uppercase}.auth-card h1{margin:0;font-size:32px;letter-spacing:-.04em}.intro{margin:9px 0 26px;color:var(--muted);line-height:1.55}.field{margin-top:17px}.field-row{display:flex;align-items:center;justify-content:space-between;gap:12px}.field label{display:block;margin-bottom:8px;font-size:14px;font-weight:800}.field-shell{position:relative}.field-icon{position:absolute;left:15px;top:50%;width:20px;height:20px;transform:translateY(-50%);color:#94a3b8;pointer-events:none}.field input{width:100%;height:52px;padding:0 48px;border:1px solid var(--line);border-radius:15px;background:#f8fafc;color:var(--text);font:inherit;outline:none;transition:border-color .2s,box-shadow .2s,background .2s}.field input:hover{border-color:#a8b7c8}.field input:focus{border-color:var(--primary);background:#fff;box-shadow:0 0 0 4px rgba(20,184,166,.13)}.field input::placeholder{color:#94a3b8}.password-toggle{position:absolute;right:8px;top:50%;width:38px;height:38px;display:grid;place-items:center;transform:translateY(-50%);border:0;border-radius:10px;background:transparent;color:#64748b;cursor:pointer}.password-toggle:hover{background:#e2e8f0;color:var(--text)}.password-toggle svg{width:19px;height:19px}.field-error{margin:7px 2px 0;color:var(--danger);font-size:13px}.status{margin:0 0 20px;padding:13px 15px;border:1px solid #99f6e4;border-radius:14px;background:#ecfdf5;color:#065f46;font-size:14px;line-height:1.5}.form-options{display:flex;align-items:center;justify-content:space-between;gap:16px;margin:18px 1px}.remember{display:flex;align-items:center;gap:9px;color:#475569;font-size:14px;cursor:pointer}.remember input{width:17px;height:17px;accent-color:var(--primary)}.text-link{color:var(--primary-dark);font-size:14px;font-weight:800;text-decoration:none}.text-link:hover{text-decoration:underline}.submit{width:100%;height:52px;margin-top:7px;border:0;border-radius:15px;background:linear-gradient(135deg,var(--primary),#0d9488);box-shadow:0 12px 25px rgba(15,118,110,.22);color:#fff;font:inherit;font-weight:900;cursor:pointer;transition:transform .2s,box-shadow .2s}.submit:hover{transform:translateY(-1px);box-shadow:0 16px 30px rgba(15,118,110,.3)}.submit:active{transform:translateY(0)}.back-link{display:flex;justify-content:center;margin-top:23px}.security-note{display:flex;align-items:flex-start;gap:10px;margin-top:22px;padding:13px;border-radius:13px;background:#f1f5f9;color:#64748b;font-size:12px;line-height:1.5}.security-note svg{width:18px;height:18px;flex:0 0 auto;color:var(--primary)}
        .field input[readonly]{cursor:not-allowed;border-color:#e2e8f0;background:#f1f5f9;color:#475569}.field input[readonly]:focus{box-shadow:none}.submit-secondary{background:#f1f5f9;box-shadow:none;color:var(--text)}.submit-secondary:hover{background:#e2e8f0;box-shadow:none}.check-email-content{text-align:center}.check-email-content h1{margin-top:4px;font-size:clamp(32px,4vw,40px);line-height:1.12;letter-spacing:-.045em}.check-email-content .intro{margin:11px 0 0;font-size:15px;line-height:1.6}.check-email-content .intro strong{display:block;margin-top:3px;color:#475569;font-weight:800;overflow-wrap:anywhere}.email-sent-icon{width:104px;height:104px;display:grid;place-items:center;margin:27px auto 25px;border-radius:25px;background:linear-gradient(145deg,#ccfbf1,#bff7eb);color:#0d9488;box-shadow:inset 0 1px 0 rgba(255,255,255,.75)}.email-sent-icon svg{width:49px;height:49px;stroke-width:1.8}.open-email-button{position:relative;display:flex;height:56px;margin-top:0;text-decoration:none;font-size:15px;box-shadow:0 14px 28px rgba(15,118,110,.24)}.open-email-button span{margin:auto}.open-email-button svg{position:absolute;right:16px;width:21px;height:21px;top:17px}.resend-inline{display:flex;align-items:center;justify-content:center;gap:7px;margin-top:28px;color:var(--muted);font-size:14px}.resend-inline button{padding:2px;border:0;background:transparent;color:var(--primary);font:inherit;font-weight:900;cursor:pointer}.resend-inline button:hover{text-decoration:underline}
        .auth-card:has(.check-email-content){padding:36px 34px 34px}.auth-card:has(.check-email-content) .auth-logo{width:62px;height:62px;margin:0 auto 23px;border-radius:16px}.auth-card:has(.check-email-content) .auth-logo img{padding:3px}.auth-card:has(.check-email-content){box-shadow:0 28px 65px rgba(15,23,42,.12)}
        @media(min-width:960px){.auth-page{grid-template-columns:minmax(420px,.9fr) minmax(500px,1.1fr)}.auth-art{display:block}.auth-main{padding:48px}.auth-card{padding:42px}}
        @media(max-width:480px){.auth-card{padding:27px 21px;border-radius:21px}.auth-card h1{font-size:28px}.form-options{align-items:flex-start;flex-direction:column}.auth-card:has(.check-email-content){padding:30px 22px}.check-email-content h1{font-size:32px}.email-sent-icon{width:96px;height:96px}.resend-inline{font-size:13px}}
    </style>
</head>
<body>
<div class="auth-page">
    <aside class="auth-art">
        <div class="art-content">
            <div class="art-brand">
                <div class="art-logo">@if($setting->logo_path)<img src="{{ asset('storage/'.$setting->logo_path) }}" alt="">@else {{ strtoupper(substr($setting->store_name,0,2)) }} @endif</div>
                <span>{{ $setting->store_name }}</span>
            </div>
            <div class="art-copy">
                <h2>Everything your store needs, in one calm place.</h2>
                <p>Manage products, inventory, sales, orders, and reports from your secure retail control center.</p>
            </div>
            <div class="art-note">Secure staff access • {{ now()->year }}</div>
        </div>
    </aside>
    <main class="auth-main">
        <section class="auth-card">
            <div class="auth-logo">@if($setting->logo_path)<img src="{{ asset('storage/'.$setting->logo_path) }}" alt="{{ $setting->store_name }}">@else {{ strtoupper(substr($setting->store_name,0,2)) }} @endif</div>
            @yield('content')
        </section>
    </main>
</div>
<script>
document.querySelectorAll('[data-password-toggle]').forEach(button => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);
        if (!input) return;
        const showing = input.type === 'text';
        input.type = showing ? 'password' : 'text';
        button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
        button.setAttribute('aria-pressed', String(!showing));
    });
});
</script>
</body>
</html>
