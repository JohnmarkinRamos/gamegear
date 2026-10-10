<div class="product-art art-{{ str_replace(' ', '-', strtolower($product->category)) }}">
    @if ($product->image_path)
        <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
    @elseif ($product->category === 'Controllers')
        <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 16h20c5 0 8 4 9 11l1 7c.5 3-2 5-4 3l-5-5H13l-5 5c-2 2-4.5 0-4-3l1-7c1-7 4-11 9-11Z"/><path d="M15 22v6m-3-3h6"/><circle cx="31" cy="23" r="1"/><circle cx="35" cy="26" r="1"/></svg>
    @elseif ($product->category === 'Headsets')
        <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M10 28v-4a14 14 0 0 1 28 0v4"/><rect x="7" y="27" width="8" height="12" rx="3"/><rect x="33" y="27" width="8" height="12" rx="3"/><path d="M37 39c0 3-3 4-8 4"/></svg>
    @elseif ($product->category === 'Keyboards')
        <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="12" width="40" height="24" rx="4"/><path d="M10 19h2m4 0h2m4 0h2m4 0h2m4 0h4M10 25h2m4 0h2m4 0h2m4 0h2m4 0h4m-23 6h18"/></svg>
    @else
        <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="14" y="6" width="20" height="36" rx="10"/><path d="M24 6v13m-10 0h20"/></svg>
    @endif
</div>