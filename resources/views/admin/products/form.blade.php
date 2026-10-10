@extends('layouts.app')

@section('title', ($product->exists ? 'Edit product' : 'Add product').' | GameGear Admin')

@section('content')
<div class="admin-shell">
    @include('admin.partials.navigation')
    <section class="admin-content">
        <div class="admin-heading"><div><p class="eyebrow">CATALOG</p><h1>{{ $product->exists ? 'Edit product' : 'Add product' }}</h1><p class="muted">Product details appear in the storefront after saving.</p></div><a class="text-link" href="{{ route('admin.products.index') }}">Back to products</a></div>
        <form class="admin-panel admin-product-form" method="post" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
            @csrf
            @if ($product->exists) @method('PUT') @endif
            <div class="admin-form-grid">
                <label>Product name<input name="name" value="{{ old('name', $product->name) }}" required maxlength="255"></label>
                <label>Brand<input name="brand" value="{{ old('brand', $product->brand) }}" required maxlength="100"></label>
                <label>Category<select name="category" required>@foreach ($categories as $category)<option value="{{ $category }}" @selected(old('category', $product->category) === $category)>{{ $category }}</option>@endforeach</select></label>
                <label>Price (PHP)<input type="number" name="price" value="{{ old('price', $product->price) }}" min="0.01" step="0.01" required></label>
                <label>Stock quantity<input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0" step="1" required></label>
                <label class="wide">Product photo<input type="file" name="image" accept="image/jpeg,image/png,image/webp" {{ $product->exists ? '' : 'required' }}><small>JPG, PNG or WebP, up to 5 MB.</small></label>
                @if ($product->image_path)<div class="admin-current-image"><img src="{{ asset($product->image_path) }}" alt="Current photo for {{ $product->name }}"><span>Current product photo</span></div>@endif
            </div>
            <div class="admin-form-actions"><a class="button outline compact" href="{{ route('admin.products.index') }}">Cancel</a><button class="button primary compact" type="submit">{{ $product->exists ? 'Save changes' : 'Add product' }}</button></div>
        </form>
    </section>
</div>
@endsection
