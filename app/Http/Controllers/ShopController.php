<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ShopController extends Controller
{
    private const CATEGORIES = ['Controllers', 'Headsets', 'Keyboards', 'Mouse', 'Processors', 'Graphics Cards', 'Memory', 'Storage', 'Monitors'];

    private const SHIPPING = ['std' => ['days' => [3, 5], 'fee' => 80], 'exp' => ['days' => [1, 2], 'fee' => 150]];

    public function index(Request $request): View
    {
        $query = Product::query();
        $category = $request->query('category', 'All');
        $search = trim($request->query('q', ''));
        $sort = $request->query('sort', 'pop');

        if (in_array($category, self::CATEGORIES, true)) {
            $query->where('category', $category);
        }
        if ($search !== '') {
            $query->where(fn ($builder) => $builder->where('name', 'like', "%{$search}%")->orWhere('brand', 'like', "%{$search}%"));
        }
        match ($sort) {
            'lo' => $query->orderBy('price'),
            'hi' => $query->orderByDesc('price'),
            default => $query->orderByDesc('sold'),
        };

        return view('shop.index', ['products' => $query->withCount('reviews')->withAvg('reviews', 'rating')->get(), 'categories' => self::CATEGORIES, 'category' => $category, 'search' => $search, 'sort' => $sort]);
    }

    public function showProduct(Request $request, Product $product): View
    {
        $product->load(['images', 'reviews' => fn ($query) => $query->latest()]);
        $gallery = collect([$product->image_path])
            ->merge($product->images->pluck('image_path'))
            ->filter()
            ->unique()
            ->values();
        $userReview = $product->reviews->firstWhere('user_id', $request->user()->id);
        return view('shop.product', compact('product', 'gallery', 'userReview'));
    }

    public function storeReview(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:160'],
            'body' => ['required', 'string', 'min:8', 'max:2000'],
        ]);
        $product->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['reviewer_name' => $request->user()->name, 'rating' => $data['rating'], 'title' => $data['title'] ?? null, 'body' => $data['body'], 'is_sample' => false],
        );

        return redirect()->to(route('products.show', $product).'#reviews')->with('status', 'Your review has been saved.');
    }

    public function cart(Request $request): View
    {
        $request->session()->forget('checkout_items');

        return view('shop.cart', ['cart' => $this->cartProducts($request)]);
    }

    public function buyNow(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        if ($data['quantity'] > $product->stock) {
            return back()->withErrors(['cart' => "Only {$product->stock} available."]);
        }

        $request->session()->put('checkout_items', [$product->id => $data['quantity']]);

        return redirect()->route('checkout');
    }

    public function addToCart(Request $request, Product $product): RedirectResponse
    {
        $quantity = max(1, (int) $request->input('quantity', 1));
        $cart = $request->session()->get('cart', []);
        $nextQuantity = ($cart[$product->id] ?? 0) + $quantity;
        if ($nextQuantity > $product->stock) {
            return back()->withErrors(['cart' => "Only {$product->stock} available."]);
        }

        $cart[$product->id] = $nextQuantity;
        $request->session()->forget('checkout_items');
        $request->session()->put('cart', $cart);

        return back()->with('status', 'Added to cart.');
    }

    public function updateCart(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        if ($data['quantity'] > $product->stock) {
            return back()->withErrors(['cart' => "Only {$product->stock} available."]);
        }
        $cart = $request->session()->get('cart', []);
        if (array_key_exists($product->id, $cart)) {
            $cart[$product->id] = $data['quantity'];
            $request->session()->put('cart', $cart);
        }

        return back();
    }

    public function removeFromCart(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);

        return back();
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $cartItems = $request->session()->get('checkout_items', $request->session()->get('cart', []));
        $cart = $this->cartProducts($request, $cartItems);
        if ($cart->isEmpty()) {
            return redirect()->route('cart')->withErrors(['cart' => 'Your cart is empty.']);
        }

        return view('shop.checkout', ['cart' => $cart]);
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'delivery_name' => ['required', 'string', 'max:255'],
            'delivery_phone' => ['required', 'string', 'max:32'],
            'delivery_address' => ['required', 'string', 'min:8', 'max:2000'],
            'shipping_option' => ['required', 'in:std,exp'],
            'payment_method' => ['required', 'in:COD,GCash,Card'],
        ]);
        $usingBuyNow = $request->session()->has('checkout_items');
        $cart = $request->session()->get('checkout_items', $request->session()->get('cart', []));
        if ($cart === []) {
            return redirect()->route('cart')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $order = DB::transaction(function () use ($request, $data, $cart) {
            $products = Product::query()->whereIn('id', array_keys($cart))->lockForUpdate()->get()->keyBy('id');
            $subtotalCents = 0;
            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                if (! $product || $quantity < 1 || $quantity > $product->stock) {
                    throw ValidationException::withMessages(['cart' => 'A cart item is no longer available in the requested quantity. Please review your cart.']);
                }
                $subtotalCents += (int) round((float) $product->price * 100) * $quantity;
            }

            $subtotal = $subtotalCents / 100;
            $shipping = self::SHIPPING[$data['shipping_option']];
            $shippingFee = $data['shipping_option'] === 'std' && $subtotal >= 1500 ? 0 : $shipping['fee'];
            $today = now()->startOfDay();
            $order = Order::create([
                'user_id' => $request->user()->id,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $subtotal + $shippingFee,
                'payment_method' => $data['payment_method'],
                'shipping_option' => $data['shipping_option'],
                'eta_from' => $today->copy()->addDays($shipping['days'][0]),
                'eta_to' => $today->copy()->addDays($shipping['days'][1]),
                'status' => 'To ship',
                'delivery_name' => $data['delivery_name'],
                'delivery_phone' => $data['delivery_phone'],
                'delivery_address' => $data['delivery_address'],
            ]);
            $order->update(['order_number' => 'GG'.str_pad((string) ($order->id + 10000), 5, '0', STR_PAD_LEFT)]);

            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'unit_price' => $product->price, 'quantity' => $quantity]);
                $product->decrement('stock', $quantity);
                $product->increment('sold', $quantity);
            }
            $request->user()->update([
                'name' => $data['delivery_name'],
                'phone' => $data['delivery_phone'],
                'address' => $data['delivery_address'],
            ]);
            $order->events()->create(['label' => 'Order placed', 'occurred_at' => now()]);

            return $order;
        });

        $request->session()->forget($usingBuyNow ? 'checkout_items' : 'cart');

        return redirect()->route('orders.index')->with('order_placed', $order->order_number);
    }

    public function orders(Request $request): View
    {
        $status = $request->query('status', 'All');
        $orders = Order::query()->with([
            'items.product.reviews' => fn ($query) => $query->where('user_id', $request->user()->id),
            'events',
        ])->where('user_id', $request->user()->id)
            ->when(in_array($status, ['To ship', 'To receive', 'Completed', 'Cancelled'], true), fn ($query) => $query->where('status', $status))
            ->latest()->get();

        return view('shop.orders', ['orders' => $orders, 'status' => $status]);
    }

    public function cancelOrder(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        if ($order->status !== 'To ship') {
            return back()->withErrors(['order' => 'Only orders that have not shipped can be cancelled.']);
        }

        DB::transaction(function () use ($order) {
            $order->load('items');
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                    Product::whereKey($item->product_id)->where('sold', '>=', $item->quantity)->decrement('sold', $item->quantity);
                }
            }
            $order->update(['status' => 'Cancelled']);
            $order->events()->create(['label' => 'Cancelled', 'occurred_at' => now()]);
        });

        return back()->with('status', 'Order cancelled.');
    }

    public function advanceOrder(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        $next = match ($order->status) {
            'To ship' => ['To receive', 'Out for delivery'],
            'To receive' => ['Completed', 'Delivered'],
            default => null,
        };
        if (! $next) {
            return back();
        }
        $order->update(['status' => $next[0]]);
        $order->events()->create(['label' => $next[1], 'occurred_at' => now()]);

        return back()->with('status', 'Order status updated.');
    }

    private function cartProducts(Request $request, ?array $items = null)
    {
        $cart = $items ?? $request->session()->get('cart', []);

        return Product::query()->whereIn('id', array_keys($cart))->get()->map(function (Product $product) use ($cart) {
            $product->cart_quantity = $cart[$product->id];

            return $product;
        });
    }
}