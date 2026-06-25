@extends('auth.layout')

@section('title', 'Forgot Password')

@section('content')
    <p class="eyebrow">Account recovery</p>
    <h1>Forgot your password?</h1>
    <p class="intro">No trouble. Enter your account email and we’ll send you a secure password reset link.</p>

    @if(session('status'))<div class="status" role="status">{{ session('status') }}</div>@endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="field">
            <label for="email">Email address</label>
            <div class="field-shell">
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 4h16v16H4z"/><path d="m4 6 8 6 8-6"/></svg>
                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required autofocus>
            </div>
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <button class="submit" type="submit">Email reset link</button>
    </form>

    <div class="security-note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
        <span>The link expires after 60 minutes and can only be used once.</span>
    </div>
    <div class="back-link"><a class="text-link" href="{{ route('login') }}">← Back to sign in</a></div>
@endsection
