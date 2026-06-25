@extends('auth.layout')

@section('title', 'Staff Login')

@section('content')
    <p class="eyebrow">Welcome back</p>
    <h1>Sign in to your account</h1>
    <p class="intro">Enter your staff credentials to continue to the control center.</p>

    @if(session('status'))<div class="status" role="status">{{ session('status') }}</div>@endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <div class="field">
            <label for="email">Email address</label>
            <div class="field-shell">
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 4h16v16H4z"/><path d="m4 6 8 6 8-6"/></svg>
                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required autofocus>
            </div>
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <div class="field-row">
                <label for="password">Password</label>
                <a class="text-link" href="{{ route('password.request') }}">Forgot password?</a>
            </div>
            <div class="field-shell">
                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                <input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required>
                <button class="password-toggle" type="button" data-password-toggle="password" aria-label="Show password" aria-pressed="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                </button>
            </div>
            @error('password')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-options">
            <label class="remember"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Keep me signed in</label>
        </div>

        <button class="submit" type="submit">Sign in securely</button>
    </form>
@endsection
