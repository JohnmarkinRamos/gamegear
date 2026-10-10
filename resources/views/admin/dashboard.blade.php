@extends('layouts.app')

@section('title', 'Admin overview | GameGear')

@section('content')
<div class="admin-shell">
    @include('admin.partials.navigation')
    <section class="admin-content">
        <div class="admin-heading"><div><p class="eyebrow">STORE MANAGEMENT</p><h1>Overview</h1><p class="muted">Today at a glance.</p></div><a class="button primary compact" href="{{ route('admin.products.create') }}">Add product</a></div>
        <div class="admin-stats">
            <article class="admin-stat"><span>Catalog products</span><b>{{ number_format($productCount) }}</b><a href="{{ route('admin.products.index') }}">Manage catalog</a></article>
            <article class="admin-stat"><span>Open orders</span><b>{{ number_format($openOrders) }}</b><a href="{{ route('admin.orders.index') }}">View orders</a></article>
            <article class="admin-stat"><span>Low stock</span><b>{{ number_format($lowStockCount) }}</b><small>5 or fewer units</small></article>
            <article class="admin-stat"><span>Completed sales</span><b>₱{{ number_format((float) $revenue, 2) }}</b><small>Completed orders</small></article>
        </div>
        <section class="admin-panel">
            <div class="admin-panel-heading"><h2>Recent orders</h2><a class="text-link" href="{{ route('admin.orders.index') }}">All orders</a></div>
            <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th></tr></thead><tbody>
                @forelse ($orders as $order)
                    <tr><td><b>{{ $order->order_number }}</b></td><td>{{ $order->user->name }}</td><td>{{ $order->created_at->format('M j, Y') }}</td><td>₱{{ number_format((float) $order->total, 2) }}</td><td><span class="status-tag status-{{ strtolower(str_replace(' ', '-', $order->status)) }}">{{ $order->status }}</span></td></tr>
                @empty
                    <tr><td colspan="5" class="table-empty">No orders have been placed yet.</td></tr>
                @endforelse
            </tbody></table></div>
        </section>
    </section>
</div>
@endsection
