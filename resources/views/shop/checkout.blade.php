@extends('layouts.app')

@section('title', 'Checkout | GameGear')

@section('content')
@php
    $subtotal = $cart->sum(fn ($product) => (float) $product->price * $product->cart_quantity);
    $user = auth()->user();
@endphp
<div class="page-heading"><div><p class="eyebrow">ALMOST THERE</p><h1>Checkout</h1></div><a class="text-link" href="{{ route('cart') }}">Back to cart</a></div>
<div class="checkout-steps" aria-label="Checkout progress"><span>Cart</span><i></i><b>Delivery & payment</b><i></i><span>Order placed</span></div>
<form method="post" action="{{ route('orders.store') }}" class="checkout-layout">
    @csrf
    <div class="checkout-main">
        <section class="surface checkout-address">
            <div class="checkout-section-heading"><div><span class="section-mark">01</span><h2>Delivery address</h2></div><span class="address-hint">Where should we send your order?</span></div>
            <div class="form-grid address-fields">
                <label>Recipient name<input name="delivery_name" value="{{ old('delivery_name', $user->name) }}" autocomplete="name" required></label>
                <label>Phone number<input name="delivery_phone" value="{{ old('delivery_phone', $user->phone) }}" inputmode="tel" autocomplete="tel" placeholder="09XX XXX XXXX" required></label>
                <label class="wide">Street address, barangay, city and province<textarea name="delivery_address" rows="2" placeholder="House no., street, barangay, city, province" autocomplete="street-address" required>{{ old('delivery_address', $user->address) }}</textarea></label>
            </div>
        </section>

        <section class="surface checkout-items">
            <div class="checkout-section-heading"><div><span class="section-mark">02</span><h2>Products</h2></div><span class="seller-name"><span class="seller-dot"></span>GameGear Official Store</span></div>
            @foreach ($cart as $product)
                <div class="checkout-product">
                    @include('components.product-art', ['product' => $product])
                    <div class="checkout-product-name"><b>{{ $product->name }}</b><small>{{ $product->brand }} · {{ $product->category }}</small></div>
                    <span class="checkout-product-quantity">× {{ $product->cart_quantity }}</span>
                    <div class="checkout-product-prices"><small>₱{{ number_format((float) $product->price, 2) }} each</small><b>₱{{ number_format((float) $product->price * $product->cart_quantity, 2) }}</b></div>
                </div>
            @endforeach
            <div class="seller-subtotal"><span>Merchandise subtotal</span><b>₱{{ number_format($subtotal, 2) }}</b></div>
        </section>

        <section class="surface">
            <div class="checkout-section-heading"><div><span class="section-mark">03</span><h2>Delivery option</h2></div></div>
            <div class="delivery-choices">
                <label class="delivery-choice">
                    <input type="radio" name="shipping_option" value="std" @checked(old('shipping_option', 'std') === 'std')>
                    <span class="radio-mark"></span>
                    <span class="delivery-choice-copy"><b>Standard delivery</b><small>Arrives in 3–5 days</small></span>
                    <b class="delivery-choice-price">{{ $subtotal >= 1500 ? 'Free' : '₱80.00' }}</b>
                </label>
                <label class="delivery-choice">
                    <input type="radio" name="shipping_option" value="exp" @checked(old('shipping_option') === 'exp')>
                    <span class="radio-mark"></span>
                    <span class="delivery-choice-copy"><b>Express delivery</b><small>Arrives in 1–2 days</small></span>
                    <b class="delivery-choice-price">₱150.00</b>
                </label>
            </div>
        </section>

        <section class="surface">
            <div class="checkout-section-heading"><div><span class="section-mark">04</span><h2>Payment method</h2></div></div>
            <div class="payment-choices">
                <label class="payment-choice"><input type="radio" name="payment_method" value="COD" @checked(old('payment_method', 'COD') === 'COD')><span class="payment-badge cod">COD</span><b>Cash on delivery</b></label>
                <label class="payment-choice"><input type="radio" name="payment_method" value="GCash" @checked(old('payment_method') === 'GCash')><span class="payment-badge gcash">G</span><b>GCash</b></label>
                <label class="payment-choice"><input type="radio" name="payment_method" value="Card" @checked(old('payment_method') === 'Card')><span class="payment-badge card-mark">••</span><b>Credit / debit card</b></label>
            </div>
            <p class="muted small payment-note">GCash and card selection are recorded with the order. Online payment processing is not connected.</p>
        </section>
    </div>
    <aside class="surface summary checkout-summary">
        <div class="checkout-section-heading"><div><span class="section-mark">05</span><h2>Order total</h2></div></div>
        <div class="summary-line"><span>Merchandise subtotal</span><b>₱{{ number_format($subtotal, 2) }}</b></div>
        <div class="summary-line"><span>Shipping fee</span><b id="checkout-shipping">{{ $subtotal >= 1500 ? 'Free' : '₱80.00' }}</b></div>
        <div class="summary-divider"></div>
        <div class="summary-line amount-due"><span>Total payment</span><b id="checkout-total">₱{{ number_format($subtotal + ($subtotal >= 1500 ? 0 : 80), 2) }}</b></div>
        <button class="button primary place-order-button" type="submit">Place order</button>
        <p class="checkout-terms">By placing your order, you agree to GameGear’s order terms.</p>
    </aside>
</form>
<script>
    const standardShipping = {{ $subtotal >= 1500 ? 0 : 80 }};
    const checkoutSubtotal = {{ json_encode($subtotal) }};
    const peso = amount => '₱' + Number(amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const updateTotal = option => {
        const shipping = option.value === 'exp' ? 150 : standardShipping;
        document.getElementById('checkout-shipping').textContent = shipping ? peso(shipping) : 'Free';
        document.getElementById('checkout-total').textContent = peso(checkoutSubtotal + shipping);
    };
    document.querySelectorAll('input[name="shipping_option"]').forEach(option => {
        option.addEventListener('change', () => updateTotal(option));
    });
    updateTotal(document.querySelector('input[name="shipping_option"]:checked'));
</script>
@endsection