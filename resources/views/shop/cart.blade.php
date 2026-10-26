@extends('layouts.app')

@section('title', 'Your cart | GameGear')

@section('content')
<div class="page-heading"><div><p class="eyebrow">YOUR SELECTION</p><h1>Shopping cart</h1></div><a class="text-link" href="{{ route('shop') }}">Continue shopping</a></div>
@include('components.momo-note', ['label' => 'MOMO’S CART CHECK', 'message' => 'Your picks are looking paw-some. Ready when you are.', 'class' => 'momo-note-cart', 'pose' => 'roll'])
@if ($cart->isEmpty())
    <div class="empty-state"><h2>Your cart is empty</h2><p>There is plenty of gear waiting for you.</p><a class="button primary compact" href="{{ route('shop') }}">Browse products</a></div>
@else
    @php($subtotal = $cart->sum(fn ($product) => (float) $product->price * $product->cart_quantity))
    <div class="checkout-layout">
        <section class="surface">
            <h2 class="section-title">Items <span>{{ $cart->sum('cart_quantity') }}</span></h2>
            @foreach ($cart as $product)
                <div class="cart-row">
                    @include('components.product-art', ['product' => $product])
                    <div class="cart-product"><b>{{ $product->name }}</b><small>{{ $product->brand }} · ₱{{ number_format((float) $product->price, 2) }}</small></div>
                    <form class="quantity-form" method="post" action="{{ route('cart.update', $product) }}">
                        @csrf @method('PATCH')
                        <label class="sr-only" for="quantity-{{ $product->id }}">Quantity</label>
                        <input id="quantity-{{ $product->id }}" type="number" name="quantity" min="1" max="{{ min(99, $product->stock) }}" value="{{ $product->cart_quantity }}" onchange="this.form.submit()">
                    </form>
                    <form method="post" action="{{ route('cart.remove', $product) }}">@csrf @method('DELETE')<button class="remove-button" type="submit">Remove</button></form>
                </div>
            @endforeach
        </section>
        <aside class="surface summary">
            <h2 class="section-title">Order summary</h2>
            <div class="summary-line"><span>Subtotal</span><b>₱{{ number_format($subtotal, 2) }}</b></div>
            <p class="muted small">Shipping is calculated at checkout.</p>
            <a class="button primary" href="{{ route('checkout') }}">Continue to checkout</a>
        </aside>
    </div>
@endif
@endsection