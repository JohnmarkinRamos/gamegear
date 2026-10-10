@extends('layouts.app')

@section('title', $mode === 'register' ? 'Create account | GameGear' : 'Log in | GameGear')

@section('content')
<section class="auth-panel">
    <p class="eyebrow">GAMEGEAR SHOP</p>
    <h1>{{ $mode === 'register' ? 'Create your account' : 'Welcome back' }}</h1>
    <p class="muted">{{ $mode === 'register' ? 'Set up your account to start shopping.' : 'Log in to browse gear and manage your orders.' }}</p>
    <form method="post" action="{{ $mode === 'register' ? route('register.store') : route('login.store') }}" class="form-stack">
        @csrf
        @if ($mode === 'register')
            <label>Full name<input name="name" value="{{ old('name') }}" autocomplete="name" required></label>
            <label>Email address<input name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required></label>
        @endif
        <label>Username<input name="username" value="{{ old('username') }}" autocomplete="username" minlength="3" required autofocus></label>
        <label>Password<input name="password" type="password" autocomplete="{{ $mode === 'register' ? 'new-password' : 'current-password' }}" required></label>
        @if ($mode === 'register')
            <label>Confirm password<input name="password_confirmation" type="password" autocomplete="new-password" required></label>
        @endif
        <button class="button primary" type="submit">{{ $mode === 'register' ? 'Create account' : 'Log in' }}</button>
    </form>
    <p class="auth-switch">
        {{ $mode === 'register' ? 'Already have an account?' : 'New to GameGear?' }}
        <a href="{{ $mode === 'register' ? route('login') : route('register') }}">{{ $mode === 'register' ? 'Log in' : 'Create an account' }}</a>
    </p>
</section>
@endsection