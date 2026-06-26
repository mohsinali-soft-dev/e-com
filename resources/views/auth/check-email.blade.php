@extends('auth.layout')

@section('title', 'Check Your Email')

@section('content')
    @php
        $resetEmail = session('reset_email');
        $emailDomain = $resetEmail && str_contains($resetEmail, '@') ? strtolower(substr(strrchr($resetEmail, '@'), 1)) : '';
        $emailInboxUrl = match (true) {
            in_array($emailDomain, ['gmail.com', 'googlemail.com'], true) => 'https://mail.google.com/mail/u/0/#inbox',
            in_array($emailDomain, ['outlook.com', 'hotmail.com', 'live.com', 'msn.com'], true) => 'https://outlook.live.com/mail/0/inbox',
            in_array($emailDomain, ['yahoo.com', 'ymail.com', 'rocketmail.com'], true) => 'https://mail.yahoo.com/',
            $emailDomain === 'icloud.com' || $emailDomain === 'me.com' || $emailDomain === 'mac.com' => 'https://www.icloud.com/mail/',
            $emailDomain === 'proton.me' || $emailDomain === 'protonmail.com' => 'https://mail.proton.me/',
            $emailDomain === 'zoho.com' || str_ends_with($emailDomain, '.zoho.com') => 'https://mail.zoho.com/',
            $emailDomain === 'aol.com' => 'https://mail.aol.com/',
            default => $emailDomain ? 'https://' . $emailDomain : 'https://mail.google.com/',
        };
    @endphp

    <div class="check-email-content">
        <h1>Check your email</h1>
        <p class="intro">
            We sent a password reset link to
            <strong>{{ session('reset_email', 'your email address') }}</strong>
        </p>

        <div class="email-sent-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="5" width="18" height="14" rx="3"/>
                <path d="m4.5 7 7.5 6 7.5-6"/>
            </svg>
        </div>

        <a class="submit open-email-button" href="{{ $emailInboxUrl }}" target="_blank" rel="noopener">
            <span>Open email inbox</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M15 3h6v6"/>
                <path d="M10 14 21 3"/>
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
            </svg>
        </a>

        @if(session('reset_email'))
            <form class="resend-inline" method="POST" action="{{ route('password.email') }}">
                @csrf
                <input type="hidden" name="email" value="{{ session('reset_email') }}">
                <span>Didn&rsquo;t receive it?</span>
                <button type="submit">Resend email</button>
            </form>
        @endif
    </div>
@endsection
