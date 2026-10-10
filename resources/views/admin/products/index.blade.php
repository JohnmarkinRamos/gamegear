@extends('layouts.app')

@section('title', 'Products | GameGear Admin')

@section('content')
<div class="admin-shell">
    @include('admin.partials.navigation')
    <section class="admin-content">
        <div class="admin-heading"><div><p class="eyebrow">CATALOG</p><h1>Products</h1><p class="muted">Manage prices, inventory and product photos.</p></div><a class="button primary compact" href="{{ route('admin.products.create') }}">Add product</a></div>
        <section class="admin-panel">
            <form class="admin-search" method="get" action="{{ route('admin.products.index') }}"><input name="q" value="{{ request('q') }}" placeholder="Search product name"><button class="button outline compact" type="submit">Search</button></form>
            <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Sold</th><th>Rating</th><th></th></tr></thead><tbody>
                @forelse ($products as $product)
                    <tr>
                        <td><div class="admin-product-cell">@if ($product->image_path)<img src="{{ asset($product->image_path) }}" alt="">@else<div class="admin-image-empty">No image</div>@endif<div><b>{{ $product->name }}</b><small>{{ $product->brand }}</small></div></div></td>
                        <td>{{ $product->category }}</td><td>₱{{ number_format((float) $product->price, 2) }}</td><td><span class="{{ $product->stock <= 5 ? 'stock-alert' : '' }}">{{ $product->stock }}</span></td><td>{{ number_format($product->sold) }}</td>
                        <td><a class="text-link" href="{{ route('admin.reviews.index', ['product' => $product->id]) }}">{{ $product->reviews_count ? number_format((float) $product->reviews_avg_rating, 1).' · '.$product->reviews_count : 'No reviews' }}</a></td>
                        <td><div class="admin-row-actions"><a class="button outline compact" href="{{ route('admin.products.edit', $product) }}">Edit</a><form method="post" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">@csrf @method('DELETE')<button class="button danger compact" type="submit">Delete</button></form></div></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="table-empty">No matching products.</td></tr>
                @endforelse
            </tbody></table></div>
            <div class="admin-pagination">{{ $products->links() }}</div>
        </section>
    </section>
</div>
@endsection
