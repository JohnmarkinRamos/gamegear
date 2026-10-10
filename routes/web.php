<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth', 'customer'])->name('logout');

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.store');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('admin')->name('admin.logout');

Route::middleware(['auth', 'customer'])->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('shop');
    Route::get('/products/{product}', [ShopController::class, 'showProduct'])->name('products.show');
    Route::post('/products/{product}/reviews', [ShopController::class, 'storeReview'])->name('products.reviews.store');
    Route::get('/cart', [ShopController::class, 'cart'])->name('cart');
    Route::post('/cart/{product}', [ShopController::class, 'addToCart'])->name('cart.add');
    Route::post('/buy/{product}', [ShopController::class, 'buyNow'])->name('buy.now');
    Route::patch('/cart/{product}', [ShopController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/{product}', [ShopController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/checkout', [ShopController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [ShopController::class, 'placeOrder'])->name('orders.store');
    Route::get('/orders', [ShopController::class, 'orders'])->name('orders.index');
    Route::patch('/orders/{order}/cancel', [ShopController::class, 'cancelOrder'])->name('orders.cancel');
    Route::patch('/orders/{order}/advance', [ShopController::class, 'advanceOrder'])->name('orders.advance');
});

Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/products', [AdminController::class, 'products'])->name('products.index');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.destroy');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews.index');
    Route::patch('/orders/{order}', [AdminController::class, 'updateOrder'])->name('orders.update');
});
