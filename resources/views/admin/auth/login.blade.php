@extends('layouts.app')

@section('title', 'Admin login | GameGear')

@section('content')
<section class="auth-panel admin-login-panel">
    <p class="eyebrow">GAMEGEAR ADMIN</p>
    <h1>Admin sign in</h1>
    <p class="muted">Store management access only.</p>
    <form method="post" action="{{ route('admin.login.store') }}" class="form-stack">
        @csrf
        <label>Admin username<input name="username" value="{{ old('username') }}" autocomplete="username" required autofocus></label>
        <label>Password<input name="password" type="password" autocomplete="current-password" required></label>
        <button class="button primary" type="submit">Sign in to admin</button>
    </form>
    <a class="admin-login-back" href="{{ route('login') }}">Customer login</a>
</section>
@endsection