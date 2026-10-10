@extends('layouts.app')

@section('title', $product->name.' | GameGear Shop')

@section('content')
@php
    $reviewCount = $product->reviews->count();
    $averageRating = $reviewCount ? $product->reviews->avg('rating') : 0;
    $ratingCounts = $product->reviews->countBy('rating');
@endphp
<a class="product-back" href="{{ route('shop') }}">← Back to shop</a>
<section class="product-detail-layout">
    <div class="product-gallery">
        <div class="product-gallery-main">
            <img id="product-gallery-main-image" src="{{ asset($gallery->first()) }}" alt="{{ $product->name }}">
        </div>
        @if ($gallery->count() > 1)
            <div class="product-gallery-thumbnails" aria-label="Product photos">
                @foreach ($gallery as $index => $image)
                    <button class="product-gallery-thumb {{ $index === 0 ? 'active' : '' }}" type="button" data-image="{{ asset($image) }}" data-alt="{{ $product->name }} view {{ $index + 1 }}" aria-label="View product photo {{ $index + 1 }}" aria-pressed="{{ $index === 0 ? 'true' : 'false' }}">
                        <img src="{{ asset($image) }}" alt="" loading="lazy">
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <aside class="product-purchase-panel">
        <p class="eyebrow">{{ $product->brand }} · {{ $product->category }}</p>
        <h1>{{ $product->name }}</h1>
        <a class="detail-rating" href="#reviews">
            @if ($reviewCount)
                <span class="rating-stars">{{ str_repeat('★', (int) round($averageRating)).str_repeat('☆', 5 - (int) round($averageRating)) }}</span>
                <b>{{ number_format($averageRating, 1) }}</b><span>{{ number_format($reviewCount) }} buyer reviews</span>
            @else
                <span class="no-rating">No buyer reviews yet</span>
            @endif
        </a>
        <div class="detail-price">₱{{ number_format((float) $product->price, 2) }}</div>
        <div class="detail-sales">{{ number_format($product->sold) }} sold <span>·</span> {{ $product->stock > 0 ? $product->stock.' in stock' : 'Sold out' }}</div>
        <div class="detail-delivery"><b>Delivery</b><span>Standard delivery · 3–5 days</span><small>Free standard shipping on orders ₱1,500 and up.</small></div>
        @if ($product->stock > 0)
            <div class="detail-quantity"><label for="detail-quantity">Quantity</label><input id="detail-quantity" type="number" min="1" max="{{ $product->stock }}" value="1" aria-label="Quantity"></div>
            <div class="detail-purchase-actions">
                <form id="detail-cart-form" method="post" action="{{ route('cart.add', $product) }}">@csrf<input class="quantity-submission" type="hidden" name="quantity" value="1"><button class="button outline" type="submit">Add to cart</button></form>
                <form id="detail-buy-form" method="post" action="{{ route('buy.now', $product) }}">@csrf<input class="quantity-submission" type="hidden" name="quantity" value="1"><button class="button primary" type="submit">Buy now</button></form>
            </div>
        @else
            <button class="button disabled" type="button" disabled>Sold out</button>
        @endif
        <div class="detail-trust"><span>Secure checkout</span><span>Buyer order tracking</span></div>
    </aside>
</section>

<section class="product-reviews" id="reviews">
    <div class="reviews-heading"><div><p class="eyebrow">REAL FEEDBACK</p><h2>Buyer reviews</h2></div><span>{{ number_format($reviewCount) }} {{ $reviewCount === 1 ? 'review' : 'reviews' }}</span></div>
    @if ($reviewCount)
        <div class="reviews-summary">
            <div class="reviews-score"><strong>{{ number_format($averageRating, 1) }}</strong><span class="rating-stars">{{ str_repeat('★', (int) round($averageRating)).str_repeat('☆', 5 - (int) round($averageRating)) }}</span><small>out of 5</small></div>
            <div class="rating-breakdown">
                @for ($score = 5; $score >= 1; $score--)
                    @php($scoreCount = $ratingCounts->get($score, 0))
                    <div class="rating-breakdown-row"><span>{{ $score }} star</span><div><i style="width: {{ $reviewCount ? ($scoreCount / $reviewCount) * 100 : 0 }}%"></i></div><small>{{ $scoreCount }}</small></div>
                @endfor
            </div>
        </div>
        <div class="review-list">
            @foreach ($product->reviews as $review)
                <article class="buyer-review">
                    <div class="review-author"><span class="review-avatar">{{ strtoupper(substr($review->reviewer_name, 0, 1)) }}</span><div><b>{{ $review->reviewer_name }}</b><small>{{ $review->created_at->format('M j, Y') }} · @if ($review->is_sample)<span class="sample-review-tag">EXAMPLE</span>@else Community review @endif</small></div></div>
                    <div class="review-stars" aria-label="{{ $review->rating }} out of 5 stars">{{ str_repeat('★', $review->rating).str_repeat('☆', 5 - $review->rating) }}</div>
                    @if ($review->title)<h3>{{ $review->title }}</h3>@endif
                    <p>{{ $review->body }}</p>
                </article>
            @endforeach
        </div>
    @else
        <div class="reviews-empty"><span class="rating-stars">☆☆☆☆☆</span><p>Be the first to review this product.</p></div>
    @endif
    <form class="review-form" method="post" action="{{ route('products.reviews.store', $product) }}">
        @csrf
        <div><p class="eyebrow">COMMUNITY FEEDBACK</p><h3>{{ $userReview ? 'Update your review' : 'Share your experience' }}</h3></div>
        <label>Rating<select name="rating" required>@for ($score = 5; $score >= 1; $score--)<option value="{{ $score }}" @selected(old('rating', $userReview?->rating ?? 5) == $score)>{{ $score }} {{ $score === 1 ? 'star' : 'stars' }}</option>@endfor</select></label>
        <label>Review title<input name="title" value="{{ old('title', $userReview?->title) }}" maxlength="160" placeholder="Sum up your experience"></label>
        <label>Your review<textarea name="body" rows="4" minlength="8" maxlength="2000" required placeholder="What should other buyers know?">{{ old('body', $userReview?->body) }}</textarea></label>
        <button class="button primary compact" type="submit">{{ $userReview ? 'Update review' : 'Post review' }}</button>
    </form>
</section>

<script>
    const quantityInput = document.getElementById('detail-quantity');
    if (quantityInput) {
        quantityInput.addEventListener('input', () => {
            document.querySelectorAll('.quantity-submission').forEach(input => input.value = quantityInput.value);
        });
    }
    document.querySelectorAll('.product-gallery-thumb').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('product-gallery-main-image').src = button.dataset.image;
            document.getElementById('product-gallery-main-image').alt = button.dataset.alt;
            document.querySelectorAll('.product-gallery-thumb').forEach(thumb => {
                thumb.classList.toggle('active', thumb === button);
                thumb.setAttribute('aria-pressed', thumb === button ? 'true' : 'false');
            });
        });
    });
</script>
@endsection