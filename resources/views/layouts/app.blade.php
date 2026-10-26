<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GameGear Shop')</title>
    <link rel="stylesheet" href="{{ asset('css/gamegear.css') }}">
</head>
@php($authScreen = request()->routeIs('login', 'register', 'admin.login'))
<body class="{{ $authScreen ? 'auth-page' : '' }}">
    @php($adminArea = request()->routeIs('admin.*') || (auth()->check() && auth()->user()->is_admin))
    @unless ($authScreen)
    <header class="site-header">
        <a class="brand" href="{{ $adminArea ? (auth()->check() && auth()->user()->is_admin ? route('admin.dashboard') : route('admin.login')) : (auth()->check() ? route('shop') : route('login')) }}">GameGear <span>{{ $adminArea ? 'Admin' : 'Shop' }}</span></a>
        @include('components.nav-momo')
        @if ($adminArea)
            @auth
                @if (auth()->user()->is_admin)
                    <nav class="nav-links admin-top-nav" aria-label="Admin navigation">
                        <span class="nav-user">{{ auth()->user()->name }}</span>
                        <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="nav-action" type="submit">Admin log out</button></form>
                    </nav>
                @endif
            @endauth
            @guest
                <nav class="nav-links guest-nav" aria-label="Main navigation">
                    <a href="{{ route('about') }}">About Us</a>
                    @unless (request()->routeIs('admin.login'))
                        <a href="{{ route('login') }}">Customer login</a>
                    @endunless
                </nav>
            @endguest
        @else
            @auth
                <form class="search" action="{{ route('shop') }}" method="get">
                    <input aria-label="Search products" name="q" placeholder="Search products" value="{{ request('q') }}">
                    <button type="submit" aria-label="Search">Search</button>
                </form>
                <nav class="nav-links" aria-label="Main navigation">
                    <a href="{{ route('about') }}">About Us</a>
                    <a href="{{ route('orders.index') }}">My orders</a>
                    <a href="{{ route('cart') }}">Cart <span class="cart-count">{{ array_sum(session('cart', [])) }}</span></a>
                    <span class="nav-user">{{ auth()->user()->name }}</span>
                    <form method="post" action="{{ route('logout') }}">@csrf<button class="nav-action" type="submit">Log out</button></form>
                </nav>
            @endauth
            @guest
                <nav class="nav-links guest-nav" aria-label="Main navigation">
                    <a href="{{ route('about') }}">About Us</a>
                    @unless (request()->routeIs('login'))
                        <a href="{{ route('login') }}">Log in</a>
                    @endunless
                    <a href="{{ route('register') }}">Create account</a>
                </nav>
            @endguest
        @endif
    </header>
    @endunless
    <main class="page-shell">
        @if (session('status'))
            <div class="notice success" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="notice error" role="alert">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>