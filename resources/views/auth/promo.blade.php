<aside class="auth-promo">
    <div class="auth-promo-copy">
        <p class="auth-promo-label">{{ $adminMode ? 'GAMEGEAR · STORE MANAGEMENT' : 'FIND YOUR NEXT FAVORITE' }}</p>
        <h2>
            @if ($adminMode)
                Your store,<br>in good hands.
            @else
                Build your<br>perfect setup.
            @endif
        </h2>
        <p>{{ $adminMode ? 'Keep your catalog, orders, and customer feedback in one place.' : 'Discover gaming essentials made for your setup, your style, and your next big win.' }}</p>
    </div>
    <div class="gear-showcase" aria-label="Featured gaming gear">
        <div class="showcase-toolbar">
            <span class="showcase-brand"><span class="auth-brand-mark" aria-hidden="true">G</span> GAMEGEAR</span>
            <span class="showcase-indicator"><i></i> {{ $adminMode ? 'STORE OVERVIEW' : 'YOUR NEXT LEVEL' }}</span>
        </div>
        <div class="showcase-feature">
            <img src="{{ asset('images/products/keyboard.jpg') }}" alt="Gaming keyboard">
            <div class="showcase-feature-copy"><small>MADE FOR YOUR SETUP</small><b>Every session,<br>more yours.</b></div>
            <span class="showcase-arrow" aria-hidden="true">↗</span>
        </div>
        <div class="showcase-bottom">
            <div class="showcase-category"><span class="showcase-category-image"><img src="{{ asset('images/products/headset.jpg') }}" alt=""></span><span><small>01 / AUDIO</small><b>Hear every detail</b></span><span class="showcase-category-arrow" aria-hidden="true">↗</span></div>
            <div class="showcase-category"><span class="showcase-category-image"><img src="{{ asset('images/products/controller.jpg') }}" alt=""></span><span><small>02 / PLAY</small><b>Find your flow</b></span><span class="showcase-category-arrow" aria-hidden="true">↗</span></div>
        </div>
        <span class="showcase-caption">THE GAMEGEAR COLLECTION <span>01 — 03</span></span>
    </div>
    @include('components.momo-note', ['label' => 'MOMO’S MINI TIP', 'message' => 'Your next favorite setup is just around the corner.', 'class' => 'momo-note-auth', 'pose' => 'groom'])
    <span class="promo-orbit promo-orbit-one" aria-hidden="true"></span>
    <span class="promo-orbit promo-orbit-two" aria-hidden="true"></span>
</aside>
