@extends('layouts.app')

@section('title', 'Admin login | GameGear')

@section('content')
<div class="auth-frame">
    <section class="auth-form-panel">
        <a class="auth-brand" href="{{ route('admin.login') }}" aria-label="GameGear admin sign in">
            <span class="auth-brand-mark" aria-hidden="true">G</span>
            <span>GameGear <small>ADMIN</small></span>
        </a>
        <div class="auth-form-content">
            <h1>Admin Sign In</h1>
            <p class="auth-form-intro">Sign in to manage your catalog, customer orders, and reviews.</p>
            <form method="post" action="{{ route('admin.login.store') }}" class="form-stack">
                @csrf
                <label>Admin username<input name="username" value="{{ old('username') }}" autocomplete="username" required autofocus></label>
                <label>Password<input name="password" type="password" autocomplete="current-password" required></label>
                <button class="button primary" type="submit">Sign In</button>
            </form>
            <p class="auth-switch">Looking to shop? <a href="{{ route('login') }}">Customer login</a></p>
        </div>
        <footer class="auth-footer">
            <span>© {{ now()->year }} GameGear</span>
            <a href="{{ route('about') }}">About Us</a>
        </footer>
    </section>
    @include('auth.promo', ['adminMode' => true])
</div>
@endsection