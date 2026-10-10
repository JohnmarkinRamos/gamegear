@extends('layouts.app')

@section('title', 'My orders | GameGear')

@section('content')
<div class="page-heading"><div><p class="eyebrow">PURCHASE HISTORY</p><h1>My orders</h1></div><a class="button outline compact" href="{{ route('shop') }}">Shop gear</a></div>
<nav class="tabs" aria-label="Filter orders">
    @foreach (['All', 'To ship', 'To receive', 'Completed', 'Cancelled'] as $filter)
        <a class="{{ $status === $filter ? 'active' : '' }}" href="{{ route('orders.index', ['status' => $filter]) }}">{{ $filter }}</a>
    @endforeach
</nav>
@if (session('order_placed'))
    <div class="notice success">Order {{ session('order_placed') }} is confirmed.</div>
@endif
<div class="order-list">
    @forelse ($orders as $order)
        <article class="surface order-card">
            <div class="order-head"><div><b>{{ $order->order_number }}</b><small>Placed {{ $order->created_at->format('M j, Y · g:i A') }}</small></div><span class="status-tag status-{{ strtolower(str_replace(' ', '-', $order->status)) }}">{{ $order->status }}</span></div>
            <div class="order-products">
                @foreach ($order->items as $item)
                    <div class="order-product-line"><span>{{ $item->product_name }} <small>× {{ $item->quantity }}</small></span><b>₱{{ number_format((float) $item->unit_price * $item->quantity, 2) }}</b></div>
                    @if ($order->status === 'Completed' && $item->product_id && $item->product)
                        @php($review = $item->product->reviews->first())
                        <details class="order-review-form" open>
                            <summary>{{ $review ? 'Edit your rating and comment' : 'Rate and comment' }}</summary>
                            <form method="post" action="{{ route('products.reviews.store', $item->product) }}">
                                @csrf
                                <fieldset class="inline-rating">
                                    <legend>Your rating</legend>
                                    @for ($score = 5; $score >= 1; $score--)
                                        <label><input type="radio" name="rating" value="{{ $score }}" @checked(old('rating', $review?->rating ?? 5) == $score) required><span aria-hidden="true">{{ str_repeat('★', $score) }}</span><small>{{ $score }}</small></label>
                                    @endfor
                                </fieldset>
                                <label>Title <input name="title" value="{{ old('title', $review?->title) }}" maxlength="160" placeholder="Sum up your experience"></label>
                                <label>Comment <textarea name="body" rows="3" minlength="8" maxlength="2000" required placeholder="What should other buyers know?">{{ old('body', $review?->body) }}</textarea></label>
                                <button class="button primary compact" type="submit">{{ $review ? 'Update review' : 'Post review' }}</button>
                            </form>
                        </details>
                    @endif
                @endforeach
            </div>
            <div class="summary-line order-total"><span>Total · {{ $order->payment_method === 'COD' ? 'pay on delivery' : $order->payment_method }}</span><b>₱{{ number_format((float) $order->total, 2) }}</b></div>
            @if ($order->status === 'To ship' || $order->status === 'To receive')
                <p class="delivery-note">Estimated delivery {{ $order->eta_from->format('M j') }}–{{ $order->eta_to->format('M j') }}</p>
            @endif
            <details class="tracking">
                <summary>Track order</summary>
                <ol class="timeline">
                    @foreach ($order->events as $event)
                        <li><b>{{ $event->label }}</b><small>{{ $event->occurred_at->format('M j, Y · g:i A') }}</small></li>
                    @endforeach
                </ol>
                <p class="muted small">Deliver to {{ $order->delivery_name }}, {{ $order->delivery_phone }}<br>{{ $order->delivery_address }}</p>
            </details>
            <div class="order-actions">
                @if ($order->status === 'To ship')
                    <form method="post" action="{{ route('orders.cancel', $order) }}">@csrf @method('PATCH')<button class="button outline compact" type="submit">Cancel order</button></form>
                    <form method="post" action="{{ route('orders.advance', $order) }}">@csrf @method('PATCH')<button class="button outline compact" type="submit">Demo: ship it</button></form>
                @elseif ($order->status === 'To receive')
                    <form method="post" action="{{ route('orders.advance', $order) }}">@csrf @method('PATCH')<button class="button primary compact" type="submit">Order received</button></form>
                @endif
            </div>
        </article>
    @empty
        <div class="empty-state"><h2>No orders here yet</h2><p>Your purchases will show up here.</p><a class="button primary compact" href="{{ route('shop') }}">Start shopping</a></div>
    @endforelse
</div>
@endsection