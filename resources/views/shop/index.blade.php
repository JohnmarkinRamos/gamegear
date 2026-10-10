@extends('layouts.app')

@section('content')
<div class="shop-heading">
    <div><p class="eyebrow">GEAR UP</p><h1>Gaming essentials</h1></div>
    <span class="result-count">{{ $products->count() }} products</span>
</div>
<form class="filter-bar" action="{{ route('shop') }}" method="get">
    <div class="category-list" aria-label="Categories">
        <a class="chip {{ $category === 'All' ? 'active' : '' }}" href="{{ route('shop', array_filter(['q' => $search, 'sort' => $sort])) }}">All gear</a>
        @foreach ($categories as $item)
            <a class="chip {{ $category === $item ? 'active' : '' }}" href="{{ route('shop', array_filter(['category' => $item, 'q' => $search, 'sort' => $sort])) }}">{{ $item }}</a>
        @endforeach
    </div>
    <input type="hidden" name="category" value="{{ $category }}">
    <input type="hidden" name="q" value="{{ $search }}">
    <label class="sort-control">Sort
        <select name="sort" onchange="this.form.submit()">
            <option value="pop" @selected($sort === 'pop')>Best selling</option>
            <option value="lo" @selected($sort === 'lo')>Price: low to high</option>
            <option value="hi" @selected($sort === 'hi')>Price: high to low</option>
        </select>
    </label>
</form>
<div class="product-grid">
    @forelse ($products as $product)
        <article class="product-card">
            <a class="product-card-link" href="{{ route('products.show', $product) }}">
                @include('components.product-art', ['product' => $product])
            </a>
            <div class="product-category">{{ $product->category }}</div>
            <h2><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h2>
            <p class="product-meta">{{ $product->brand }} <span>·</span> {{ number_format($product->sold) }} sold</p>
            <a class="product-rating-link" href="{{ route('products.show', $product) }}#reviews" aria-label="{{ number_format((float) $product->reviews_avg_rating, 1) }} out of 5 stars, {{ $product->reviews_count }} reviews">
                @if ($product->reviews_count)
                    <span class="rating-stars">★★★★★</span><b>{{ number_format((float) $product->reviews_avg_rating, 1) }}</b><small>({{ $product->reviews_count }})</small>
                @else
                    <small class="no-rating">No reviews yet</small>
                @endif
            </a>
            <div class="product-bottom">
                <strong class="price">₱{{ number_format((float) $product->price, 2) }}</strong>
                @if ($product->stock === 0)
                    <span class="stock sold-out">Sold out</span>
                @elseif ($product->stock <= 5)
                    <span class="stock">{{ $product->stock }} left</span>
                @endif
            </div>
            <div class="product-actions">
                <form method="post" action="{{ route('cart.add', $product) }}">
                    @csrf
                    <button class="button outline" type="submit" @disabled(! $product->stock)>Add to cart</button>
                </form>
                <form method="post" action="{{ route('buy.now', $product) }}">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button class="button primary" type="submit" @disabled(! $product->stock)>Buy now</button>
                </form>
            </div>
        </article>
    @empty
        <div class="empty-state"><h2>No products found</h2><p>Try another search or category.</p></div>
    @endforelse
</div>
@endsection