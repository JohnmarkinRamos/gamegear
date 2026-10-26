<div>
    <!-- Do what you can, with what you have, where you are. - Theodore Roosevelt -->
</div>
@extends('layouts.app')

@section('title', 'About Us | GameGear')

@section('content')
<section class="about-hero">
    <div class="about-hero-copy">
        <p class="eyebrow">A LITTLE ABOUT US <span aria-hidden="true">✿</span></p>
        <h1>Made for the<br><span>love of the game.</span></h1>
        <p>GameGear is your place to discover gaming essentials for every setup, every style, and every kind of player.</p>
        <a class="button primary compact" href="{{ auth()->check() ? (auth()->user()->is_admin ? route('admin.dashboard') : route('shop')) : route('register') }}">{{ auth()->check() ? (auth()->user()->is_admin ? 'Back to admin' : 'Explore the shop') : 'Get started' }} <span aria-hidden="true">♡</span></a>
    </div>
    <div class="about-hero-art" aria-hidden="true">
        <span class="about-sparkle about-sparkle-one">✦</span>
        <span class="about-sparkle about-sparkle-two">✧</span>
        <div class="about-gear-card about-gear-keyboard">
            <img src="{{ asset('images/products/keyboard.jpg') }}" alt="">
            <span><small>YOUR NEXT FAVORITE</small><b>Keyboard crush ♡</b></span>
        </div>
        <div class="about-gear-card about-gear-headset">
            <img src="{{ asset('images/products/headset.jpg') }}" alt="">
            <span><small>MADE FOR PLAY</small><b>Sound on, world off</b></span>
        </div>
        <span class="about-gear-sticker">PLAY<br>MORE <span>♡</span></span>
    </div>
</section>

<section class="about-intro">
    <div>
        <p class="eyebrow">OUR LITTLE CORNER OF GAMING <span aria-hidden="true">♡</span></p>
        <h2>Better gear.<br>More ways to play.</h2>
    </div>
    <p>Finding the right setup should feel exciting, not complicated. We bring together keyboards, headsets, mice, monitors, and more in one easy-to-explore shop, so you can spend less time searching and more time playing.</p>
</section>

<section class="about-values" aria-label="What you can do with GameGear">
    <article class="about-value">
        <div class="about-value-top"><span class="about-value-number">01</span><span class="about-value-icon" aria-hidden="true">✿</span></div>
        <h2>Find your fit</h2>
        <p>Explore gaming gear by category and compare the details that matter to your setup.</p>
    </article>
    <article class="about-value">
        <div class="about-value-top"><span class="about-value-number">02</span><span class="about-value-icon" aria-hidden="true">✧</span></div>
        <h2>Choose with confidence</h2>
        <p>Read community reviews and share your own experience with the gear you use.</p>
    </article>
    <article class="about-value">
        <div class="about-value-top"><span class="about-value-number">03</span><span class="about-value-icon" aria-hidden="true">♡</span></div>
        <h2>Stay in the loop</h2>
        <p>Review your order history and follow each order from purchase through delivery.</p>
    </article>
</section>

<section class="about-cat-card" aria-labelledby="about-cat-title">
    <div class="about-cat-copy">
        <p class="eyebrow">A TINY PAUSE BETWEEN ROUNDS <span aria-hidden="true">✿</span></p>
        <h2 id="about-cat-title">Meet Momo, our little pixel cat.</h2>
        <p>Momo is on a mission to find the coziest corner of every gaming setup. Catch her doing her happy little zoomies.</p>
        <span class="about-cat-note"><span aria-hidden="true">♡</span> Approved by the cozy-gaming club</span>
    </div>
    <div class="cat-runway" role="img" aria-label="A small animated cat runs across a pastel playroom">
        <span class="cat-cloud cat-cloud-one" aria-hidden="true"></span>
        <span class="cat-cloud cat-cloud-two" aria-hidden="true"></span>
        <span class="cat-sparkle cat-sparkle-one" aria-hidden="true">✦</span>
        <span class="cat-sparkle cat-sparkle-two" aria-hidden="true">✧</span>
        @include('components.momo-cat')
        <span class="cat-runway-floor" aria-hidden="true"></span>
        <span class="cat-runway-label" aria-hidden="true">MOMO’S HAPPY PLACE</span>
    </div>
</section>

<section class="about-cta">
    <div><p class="eyebrow">READY WHEN YOU ARE <span aria-hidden="true">♡</span></p><h2>Your next setup starts here.</h2></div>
    <a class="button primary compact" href="{{ auth()->check() ? (auth()->user()->is_admin ? route('admin.dashboard') : route('shop')) : route('login') }}">Visit GameGear <span aria-hidden="true">→</span></a>
</section>
@endsection
