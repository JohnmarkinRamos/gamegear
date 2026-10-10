@extends('layouts.app')

@section('title', 'Reviews | GameGear Admin')

@section('content')
<div class="admin-shell">
    @include('admin.partials.navigation')
    <section class="admin-content">
        <div class="admin-heading"><div><p class="eyebrow">CUSTOMER FEEDBACK</p><h1>Reviews</h1><p class="muted">Ratings and comments across the catalog.</p></div></div>
        <section class="admin-panel">
            <form class="admin-search admin-review-filters" method="get" action="{{ route('admin.reviews.index') }}">
                <input name="q" value="{{ $search }}" placeholder="Search product, reviewer or comment">
                <select name="rating" aria-label="Filter by star rating">
                    <option value="">All ratings</option>
                    @for ($score = 5; $score >= 1; $score--)
                        <option value="{{ $score }}" @selected((string) $rating === (string) $score)>{{ $score }} stars</option>
                    @endfor
                </select>
                @if ($productId)<input type="hidden" name="product" value="{{ $productId }}">@endif
                <button class="button outline compact" type="submit">Filter</button>
                @if ($search || $rating || $productId)<a class="text-link admin-clear-filter" href="{{ route('admin.reviews.index') }}">Clear filters</a>@endif
            </form>
            <div class="admin-review-list">
                @forelse ($reviews as $review)
                    <article class="admin-review-row">
                        <div class="admin-review-meta">
                            <a href="{{ route('admin.products.edit', $review->product) }}"><b>{{ $review->product->name }}</b></a>
                            <span>{{ $review->reviewer_name }}</span>
                            <span class="review-kind {{ $review->is_sample ? 'sample' : '' }}">{{ $review->is_sample ? 'Example' : 'Community review' }}</span>
                            <time>{{ $review->created_at->format('M j, Y') }}</time>
                        </div>
                        <div class="admin-review-rating"><span class="rating-stars">{{ str_repeat('★', $review->rating).str_repeat('☆', 5 - $review->rating) }}</span><b>{{ $review->rating }} / 5</b></div>
                        @if ($review->title)<h2>{{ $review->title }}</h2>@endif
                        <p>{{ $review->body }}</p>
                    </article>
                @empty
                    <div class="table-empty">No reviews match these filters.</div>
                @endforelse
            </div>
            <div class="admin-pagination">{{ $reviews->links() }}</div>
        </section>
    </section>
</div>
@endsection