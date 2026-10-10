@extends('layouts.app')

@section('title', 'Orders | GameGear Admin')

@section('content')
<div class="admin-shell">
    @include('admin.partials.navigation')
    <section class="admin-content">
        <div class="admin-heading"><div><p class="eyebrow">FULFILLMENT</p><h1>Orders</h1><p class="muted">Review customer purchases and update fulfillment status.</p></div></div>
        <nav class="tabs admin-order-filters" aria-label="Filter orders">
            @foreach (['All', 'To ship', 'To receive', 'Completed', 'Cancelled'] as $filter)
                <a class="{{ $status === $filter ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => $filter]) }}">{{ $filter }}</a>
            @endforeach
        </nav>
        <div class="admin-order-list">
            @forelse ($orders as $order)
                <article class="admin-panel admin-order-card">
                    <div class="admin-order-head"><div><b>{{ $order->order_number }}</b><span>{{ $order->user->name }} · {{ '@'.$order->user->username }} · {{ $order->created_at->format('M j, Y g:i A') }}</span></div><span class="status-tag status-{{ strtolower(str_replace(' ', '-', $order->status)) }}">{{ $order->status }}</span></div>
                    <div class="admin-order-lines">
                        @foreach ($order->items as $item)<div><span>{{ $item->product_name }} <small>× {{ $item->quantity }}</small></span><b>₱{{ number_format((float) $item->unit_price * $item->quantity, 2) }}</b></div>@endforeach
                    </div>
                    <div class="admin-order-details"><span>{{ $order->payment_method }} · {{ $order->shipping_option === 'exp' ? 'Express' : 'Standard' }}</span><b>Total ₱{{ number_format((float) $order->total, 2) }}</b></div>
                    <details class="admin-delivery"><summary>Delivery details and timeline</summary><p>{{ $order->delivery_name }} · {{ $order->delivery_phone }}<br>{{ $order->delivery_address }}</p><ul>@foreach ($order->events as $event)<li>{{ $event->label }} · {{ $event->occurred_at->format('M j, Y g:i A') }}</li>@endforeach</ul></details>
                    @if ($order->status === 'To ship' || $order->status === 'To receive')
                        <form class="admin-status-form" method="post" action="{{ route('admin.orders.update', $order) }}">@csrf @method('PATCH')
                            <label for="status-{{ $order->id }}">Update status</label>
                            <select id="status-{{ $order->id }}" name="status">@if ($order->status === 'To ship')<option value="To receive">Mark as shipped</option><option value="Cancelled">Cancel and restock</option>@else<option value="Completed">Mark as delivered</option>@endif</select>
                            <button class="button primary compact" type="submit">Update order</button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="admin-panel table-empty">No orders match this filter.</div>
            @endforelse
        </div>
        <div class="admin-pagination">{{ $orders->links() }}</div>
    </section>
</div>
@endsection
