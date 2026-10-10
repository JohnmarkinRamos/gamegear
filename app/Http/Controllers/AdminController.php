<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const CATEGORIES = ['Controllers', 'Headsets', 'Keyboards', 'Mouse', 'Processors', 'Graphics Cards', 'Memory', 'Storage', 'Monitors'];

    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'productCount' => Product::count(),
            'lowStockCount' => Product::where('stock', '<=', 5)->count(),
            'openOrders' => Order::whereIn('status', ['To ship', 'To receive'])->count(),
            'revenue' => Order::where('status', 'Completed')->sum('total'),
            'orders' => Order::with('user')->latest()->limit(8)->get(),
        ]);
    }

    public function products(Request $request): View
    {
        $products = Product::query()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q')->trim().'%'))
            ->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function createProduct(): View
    {
        return view('admin.products.form', ['product' => new Product(), 'categories' => self::CATEGORIES]);
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:'.implode(',', self::CATEGORIES)],
            'price' => ['required', 'numeric', 'min:0.01', 'max:99999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $data['image_path'] = 'storage/'.$request->file('image')->storePublicly('products', 'public');
        unset($data['image']);
        Product::create($data);

        return redirect()->route('admin.products.index')->with('status', 'Product added.');
    }

    public function editProduct(Product $product): View
    {
        return view('admin.products.form', ['product' => $product, 'categories' => self::CATEGORIES]);
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:'.implode(',', self::CATEGORIES)],
            'price' => ['required', 'numeric', 'min:0.01', 'max:99999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        if ($request->hasFile('image')) {
            $this->deleteUploadedImage($product->image_path);
            $data['image_path'] = 'storage/'.$request->file('image')->storePublicly('products', 'public');
        }
        unset($data['image']);
        $product->update($data);

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    public function deleteProduct(Product $product): RedirectResponse
    {
        $this->deleteUploadedImage($product->image_path);
        $product->delete();

        return back()->with('status', 'Product deleted.');
    }

    public function orders(Request $request): View
    {
        $status = $request->query('status', 'All');
        $orders = Order::with(['user', 'items', 'events'])
            ->when(in_array($status, ['To ship', 'To receive', 'Completed', 'Cancelled'], true), fn ($query) => $query->where('status', $status))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders', 'status'));
    }

    public function reviews(Request $request): View
    {
        $query = ProductReview::with('product')->latest();
        $search = trim((string) $request->query('q', ''));
        $rating = $request->query('rating');
        $productId = $request->query('product');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('reviewer_name', 'like', '%'.$search.'%')
                    ->orWhere('body', 'like', '%'.$search.'%')
                    ->orWhereHas('product', fn ($productQuery) => $productQuery->where('name', 'like', '%'.$search.'%'));
            });
        }
        if (in_array((int) $rating, [1, 2, 3, 4, 5], true)) {
            $query->where('rating', (int) $rating);
        }
        if (filter_var($productId, FILTER_VALIDATE_INT)) {
            $query->where('product_id', (int) $productId);
        }

        $reviews = $query->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'search', 'rating', 'productId'));
    }

    public function updateOrder(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:To ship,To receive,Completed,Cancelled']]);
        $updated = DB::transaction(function () use ($order, $data): bool {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $allowed = match ($order->status) {
                'To ship' => ['To receive', 'Cancelled'],
                'To receive' => ['Completed'],
                default => [],
            };
            if (! in_array($data['status'], $allowed, true)) {
                return false;
            }

            if ($data['status'] === 'Cancelled') {
                $order->load('items');
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                        Product::whereKey($item->product_id)->where('sold', '>=', $item->quantity)->decrement('sold', $item->quantity);
                    }
                }
            }

            $order->update(['status' => $data['status']]);
            $event = match ($data['status']) {
                'To receive' => 'Out for delivery',
                'Completed' => 'Delivered',
                'Cancelled' => 'Cancelled',
            };
            $order->events()->create(['label' => $event, 'occurred_at' => now()]);

            return true;
        });
        if (! $updated) {
            return back()->withErrors(['status' => 'That order status change is not allowed.']);
        }

        return back()->with('status', 'Order status updated.');
    }

    private function deleteUploadedImage(?string $imagePath): void
    {
        if (Str::startsWith($imagePath ?? '', 'storage/products/')) {
            Storage::disk('public')->delete(Str::after($imagePath, 'storage/'));
        }
    }
}