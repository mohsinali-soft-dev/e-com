@extends('auth.layout')

@section('title', 'Check Your Email')

@section('content')
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

        <a class="submit open-email-button" href="mailto:{{ session('reset_email') }}">
            Open email app
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
