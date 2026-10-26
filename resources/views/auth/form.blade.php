@extends('layouts.app')

@section('title', $mode === 'register' ? 'Create account | GameGear' : 'Log in | GameGear')

@section('content')
<div class="auth-frame">
    <section class="auth-form-panel">
        <a class="auth-brand" href="{{ route('login') }}" aria-label="GameGear home">
            <span class="auth-brand-mark" aria-hidden="true">G</span>
            <span>GameGear</span>
        </a>
        <div class="auth-form-content">
            <h1>{{ $mode === 'register' ? 'Create Account' : 'Welcome Back' }}</h1>
            <p class="auth-form-intro">{{ $mode === 'register' ? 'Create an account to find gear that fits your game.' : 'Enter your username and password to access your account.' }}</p>
            <form method="post" action="{{ $mode === 'register' ? route('register.store') : route('login.store') }}" class="form-stack">
                @csrf
                @if ($mode === 'register')
                    <label>Full name<input name="name" value="{{ old('name') }}" autocomplete="name" required></label>
                    <label>Email address<input name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required></label>
                @endif
                <label>{{ $mode === 'register' ? 'Username' : 'Email or username' }}<input name="username" value="{{ old('username') }}" autocomplete="username" minlength="3" required autofocus></label>
                <label>Password<input name="password" type="password" autocomplete="{{ $mode === 'register' ? 'new-password' : 'current-password' }}" required></label>
                @if ($mode === 'register')
                    <label>Confirm password<input name="password_confirmation" type="password" autocomplete="new-password" required></label>
                @endif
                @if ($mode === 'login')
                    <div class="auth-form-options">
                        <label class="remember-choice"><input type="checkbox" name="remember" value="1">Remember me</label>
                        <a href="{{ route('about') }}">Need help?</a>
                    </div>
                @endif
                <button class="button primary" type="submit">{{ $mode === 'register' ? 'Create account' : 'Log In' }}</button>
            </form>
            @if ($mode === 'login')
                <div class="auth-divider"><span>OR KEEP EXPLORING</span></div>
                <a class="auth-secondary-link" href="{{ route('about') }}">Get to know GameGear</a>
            @endif
            <p class="auth-switch">
                {{ $mode === 'register' ? 'Already have an account?' : 'Don’t have an account?' }}
                <a href="{{ $mode === 'register' ? route('login') : route('register') }}">{{ $mode === 'register' ? 'Log in' : 'Register now' }}</a>
            </p>
        </div>
        <footer class="auth-footer">
            <span>© {{ now()->year }} GameGear</span>
            <a href="{{ route('about') }}">About Us</a>
        </footer>
    </section>
    @include('auth.promo', ['adminMode' => false])
</div>
@endsection