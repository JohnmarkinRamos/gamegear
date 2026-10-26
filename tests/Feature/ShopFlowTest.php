<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShopFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_customer_can_browse_database_products(): void
    {
        $user = User::create(['name' => 'Customer', 'username' => 'customer', 'password' => Hash::make('secure-pass-123')]);
        Product::create([
            'name' => 'Mechanical Keyboard TKL',
            'brand' => 'Keyra',
            'category' => 'Keyboards',
            'image_path' => 'images/products/keyboard.jpg',
            'price' => 2899,
            'stock' => 3,
            'sold' => 430,
        ]);

        $this->actingAs($user)->get(route('shop'))
            ->assertOk()
            ->assertSee('Mechanical Keyboard TKL')
            ->assertSee('Keyra')
            ->assertSee('images/products/keyboard.jpg')
            ->assertSee('Add to cart')
            ->assertSee('Buy now')
            ->assertDontSee(route('admin.dashboard'));
    }

    public function test_buy_now_orders_only_selected_product_and_preserves_cart(): void
    {
        $user = User::create(['name' => 'Customer', 'username' => 'customer', 'password' => Hash::make('secure-pass-123')]);
        $cartProduct = Product::create([
            'name' => 'Cart product', 'brand' => 'Nexa', 'category' => 'Mouse', 'price' => 500, 'stock' => 5, 'sold' => 0,
        ]);
        $buyNowProduct = Product::create([
            'name' => 'Buy now product', 'brand' => 'Sonar', 'category' => 'Headsets', 'price' => 1200, 'stock' => 3, 'sold' => 0,
        ]);

        $this->actingAs($user)->withSession(['cart' => [$cartProduct->id => 1]])
            ->post(route('buy.now', $buyNowProduct), ['quantity' => 1])
            ->assertRedirect(route('checkout'));

        $this->get(route('checkout'))
            ->assertOk()
            ->assertSee('Buy now product')
            ->assertDontSee('Cart product');

        $this->post(route('orders.store'), [
            'delivery_name' => 'Customer',
            'delivery_phone' => '09123456789',
            'delivery_address' => '12 Test Street, Quezon City',
            'shipping_option' => 'std',
            'payment_method' => 'COD',
        ])->assertRedirect(route('orders.index'));

        $this->assertDatabaseHas('order_items', ['product_id' => $buyNowProduct->id, 'quantity' => 1]);
        $this->assertDatabaseMissing('order_items', ['product_id' => $cartProduct->id]);
        $this->assertSame([$cartProduct->id => 1], Session::get('cart'));
    }

    public function test_checkout_shows_marketplace_order_sections(): void
    {
        $user = User::create(['name' => 'Customer', 'username' => 'customer', 'password' => Hash::make('secure-pass-123')]);
        $product = Product::create([
            'name' => 'Pro Wireless Controller',
            'brand' => 'Nexa',
            'category' => 'Controllers',
            'price' => 2499,
            'stock' => 4,
            'sold' => 10,
        ]);

        $this->actingAs($user)->withSession(['cart' => [$product->id => 1]])
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee('GameGear Official Store')
            ->assertSee('Pro Wireless Controller')
            ->assertSee('Delivery option')
            ->assertSee('Cash on delivery')
            ->assertSee('Total payment');
    }

    public function test_cart_displays_its_own_rolling_momo_companion(): void
    {
        $user = User::create(['name' => 'Customer', 'username' => 'customer', 'password' => Hash::make('secure-pass-123')]);
        $product = Product::create([
            'name' => 'Gaming Mouse', 'brand' => 'Nexa', 'category' => 'Mouse', 'price' => 799, 'stock' => 5, 'sold' => 0,
        ]);

        $this->actingAs($user)->withSession(['cart' => [$product->id => 1]])
            ->get(route('cart'))
            ->assertOk()
            ->assertSee('momo-note-roll');
    }

    public function test_product_page_shows_controller_gallery_rating_and_example_buyer_comments(): void
    {
        $this->seed(\Database\Seeders\ProductSeeder::class);
        $user = User::create(['name' => 'Customer', 'username' => 'customer', 'password' => Hash::make('secure-pass-123')]);
        $product = Product::where('name', 'Pro Wireless Controller')->firstOrFail();

        $this->actingAs($user)->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Buyer reviews')
            ->assertSee('4.7')
            ->assertSee('Comfortable for long sessions')
            ->assertSee('EXAMPLE')
            ->assertSee('controller-angle.jpg');
        $this->assertSame(4, ProductImage::where('product_id', $product->id)->count());
    }

    public function test_seeded_products_have_unique_images_and_reseeding_preserves_stock(): void
    {
        $this->seed(\Database\Seeders\ProductSeeder::class);
        $products = Product::all();
        $images = ProductImage::all();

        $this->assertSame($products->count(), $products->pluck('image_path')->unique()->count());
        $this->assertSame($images->count(), $images->pluck('image_path')->unique()->count());
        foreach ($products as $product) {
            $this->assertFileExists(public_path($product->image_path));
        }

        $products->firstWhere('name', 'Pro Wireless Controller')->update(['stock' => 7]);
        $this->seed(\Database\Seeders\ProductSeeder::class);
        $this->assertSame(7, Product::where('name', 'Pro Wireless Controller')->value('stock'));
    }

    public function test_customer_can_review_multiple_products_without_purchase(): void
    {
        $user = User::create(['name' => 'Customer', 'username' => 'customer', 'password' => Hash::make('secure-pass-123')]);
        $firstProduct = Product::create([
            'name' => 'Controller', 'brand' => 'Nexa', 'category' => 'Controllers', 'price' => 2499, 'stock' => 5, 'sold' => 1,
        ]);
        $secondProduct = Product::create([
            'name' => 'Gaming Headset', 'brand' => 'Sonar', 'category' => 'Headsets', 'price' => 1999, 'stock' => 5, 'sold' => 0,
        ]);
        $reviewData = ['rating' => 5, 'title' => 'Great controller', 'body' => 'Feels responsive and comfortable.'];

        $this->actingAs($user)->get(route('products.show', $firstProduct))
            ->assertOk()
            ->assertSee('COMMUNITY FEEDBACK')
            ->assertSee('momo-note-paw');
        $this->post(route('products.reviews.store', $firstProduct), $reviewData)
            ->assertRedirect(route('products.show', $firstProduct).'#reviews');
        $this->post(route('products.reviews.store', $secondProduct), $reviewData)
            ->assertRedirect(route('products.show', $secondProduct).'#reviews');
        $this->assertDatabaseHas('product_reviews', ['product_id' => $firstProduct->id, 'user_id' => $user->id]);
        $this->assertDatabaseHas('product_reviews', ['product_id' => $secondProduct->id, 'user_id' => $user->id]);

        $order = Order::create([
            'order_number' => 'GG10001', 'user_id' => $user->id, 'subtotal' => 1999, 'shipping_fee' => 0, 'total' => 1999,
            'payment_method' => 'COD', 'shipping_option' => 'std', 'eta_from' => now(), 'eta_to' => now(), 'status' => 'Completed',
            'delivery_name' => 'Customer', 'delivery_phone' => '09123456789', 'delivery_address' => '12 Test Street, Quezon City',
        ]);
        $order->items()->create(['product_id' => $secondProduct->id, 'product_name' => $secondProduct->name, 'unit_price' => $secondProduct->price, 'quantity' => 1]);

        $this->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Edit your rating and comment')
            ->assertSee('momo-note-sleep')
            ->assertSee('momo-note-paw')
            ->assertSee('Your rating')
            ->assertSee('Comment');
    }

    public function test_customer_can_register_with_a_normalized_username(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Game Gear Customer',
            'username' => 'GearFan',
            'email' => 'GEARFAN@example.com',
            'password' => 'secure-pass-123',
            'password_confirmation' => 'secure-pass-123',
        ]);

        $response->assertRedirect(route('shop'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['username' => 'gearfan', 'email' => 'gearfan@example.com', 'name' => 'Game Gear Customer']);
        $this->assertTrue(Hash::check('secure-pass-123', User::where('username', 'gearfan')->firstOrFail()->password));
    }

    public function test_checkout_persists_order_and_updates_inventory_atomically(): void
    {
        $user = User::create([
            'name' => 'Customer',
            'username' => 'customer',
            'password' => Hash::make('secure-pass-123'),
        ]);
        $product = Product::create([
            'name' => 'Pro Wireless Controller',
            'brand' => 'Nexa',
            'category' => 'Controllers',
            'price' => 2499,
            'stock' => 4,
            'sold' => 10,
        ]);

        $response = $this->actingAs($user)->withSession(['cart' => [$product->id => 2]])
            ->post(route('orders.store'), [
                'delivery_name' => 'Customer',
                'delivery_phone' => '09123456789',
                'delivery_address' => '12 Test Street, Quezon City',
                'shipping_option' => 'std',
                'payment_method' => 'COD',
            ]);

        $response->assertRedirect(route('orders.index'));
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'subtotal' => 4998,
            'shipping_fee' => 0,
            'total' => 4998,
            'status' => 'To ship',
        ]);
        $this->assertDatabaseHas('order_items', ['product_id' => $product->id, 'quantity' => 2]);
        $this->assertDatabaseHas('order_events', ['label' => 'Order placed']);
        $this->assertSame(2, $product->fresh()->stock);
        $this->assertSame(12, $product->fresh()->sold);
        $this->assertSame('09123456789', $user->fresh()->phone);
        $this->assertSame('12 Test Street, Quezon City', $user->fresh()->address);
        $this->assertFalse(Session::has('cart'));
    }

    public function test_customer_cannot_cancel_another_customers_order(): void
    {
        $owner = User::create(['name' => 'Owner', 'username' => 'owner', 'password' => Hash::make('secure-pass-123')]);
        $other = User::create(['name' => 'Other', 'username' => 'other', 'password' => Hash::make('secure-pass-123')]);
        $order = Order::create([
            'order_number' => 'GG10001',
            'user_id' => $owner->id,
            'subtotal' => 100,
            'shipping_fee' => 80,
            'total' => 180,
            'payment_method' => 'COD',
            'shipping_option' => 'std',
            'eta_from' => now()->addDays(3),
            'eta_to' => now()->addDays(5),
            'status' => 'To ship',
            'delivery_name' => 'Owner',
            'delivery_phone' => '09123456789',
            'delivery_address' => '12 Test Street, Quezon City',
        ]);

        $this->actingAs($other)->patch(route('orders.cancel', $order))->assertNotFound();
        $this->assertSame('To ship', $order->fresh()->status);
    }

    public function test_non_admin_customer_cannot_open_admin_dashboard(): void
    {
        $user = User::create(['name' => 'Customer', 'username' => 'customer', 'password' => Hash::make('secure-pass-123')]);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.reviews.index'))->assertForbidden();
    }

    public function test_admin_can_view_product_ratings_and_comments(): void
    {
        $this->seed(\Database\Seeders\ProductSeeder::class);
        $admin = User::create(['name' => 'Store Admin', 'username' => 'storeadmin', 'password' => Hash::make('secure-pass-123')]);
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)->get(route('admin.reviews.index'))
            ->assertOk()
            ->assertSee('Pro Wireless Controller')
            ->assertSee('Mika R.')
            ->assertSee('Comfortable for long sessions')
            ->assertSee('Example')
            ->assertSee('5 / 5');
    }

    public function test_admin_signs_in_through_admin_login_and_cannot_open_customer_storefront(): void
    {
        $admin = User::create(['name' => 'Dedicated Admin', 'username' => 'gearadmin', 'password' => Hash::make('secure-admin-pass')]);
        $admin->forceFill(['is_admin' => true])->save();

        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->post(route('admin.login.store'), ['username' => 'gearadmin', 'password' => 'secure-admin-pass'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Admin log out')
            ->assertSee('momo-note-wave')
            ->assertDontSee('Cart');
        $this->get(route('shop'))->assertForbidden();
    }

    public function test_admin_account_cannot_sign_in_through_customer_login(): void
    {
        $admin = User::create(['name' => 'Dedicated Admin', 'username' => 'gearadmin', 'password' => Hash::make('secure-admin-pass')]);
        $admin->forceFill(['is_admin' => true])->save();

        $this->from(route('login'))->post(route('login.store'), ['username' => 'gearadmin', 'password' => 'secure-admin-pass'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_admin_can_create_product_with_uploaded_photo(): void
    {
        Storage::fake('public');
        $admin = User::create(['name' => 'Store Admin', 'username' => 'storeadmin', 'password' => Hash::make('secure-pass-123')]);
        $admin->forceFill(['is_admin' => true])->save();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'New Gaming Mouse',
            'brand' => 'Nexa',
            'category' => 'Mouse',
            'price' => 1599.50,
            'stock' => 12,
            'image' => UploadedFile::fake()->image('mouse.jpg'),
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $product = Product::where('name', 'New Gaming Mouse')->firstOrFail();
        $this->assertStringStartsWith('storage/products/', $product->image_path);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $product->image_path));
    }

    public function test_admin_cancellation_restocks_order_items_and_records_event(): void
    {
        $admin = User::create(['name' => 'Store Admin', 'username' => 'storeadmin', 'password' => Hash::make('secure-pass-123')]);
        $admin->forceFill(['is_admin' => true])->save();
        $customer = User::create(['name' => 'Customer', 'username' => 'customer', 'password' => Hash::make('secure-pass-123')]);
        $product = Product::create([
            'name' => 'Gaming Mouse', 'brand' => 'Nexa', 'category' => 'Mouse', 'price' => 1000, 'stock' => 2, 'sold' => 5,
        ]);
        $order = Order::create([
            'order_number' => 'GG10001',
            'user_id' => $customer->id,
            'subtotal' => 1000,
            'shipping_fee' => 80,
            'total' => 1080,
            'payment_method' => 'COD',
            'shipping_option' => 'std',
            'eta_from' => now()->addDays(3),
            'eta_to' => now()->addDays(5),
            'status' => 'To ship',
            'delivery_name' => 'Customer',
            'delivery_phone' => '09123456789',
            'delivery_address' => '12 Test Street, Quezon City',
        ]);
        $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'unit_price' => 1000, 'quantity' => 2]);

        $this->actingAs($admin)->patch(route('admin.orders.update', $order), ['status' => 'Cancelled'])
            ->assertRedirect();

        $this->assertSame('Cancelled', $order->fresh()->status);
        $this->assertSame(4, $product->fresh()->stock);
        $this->assertSame(3, $product->fresh()->sold);
        $this->assertDatabaseHas('order_events', ['order_id' => $order->id, 'label' => 'Cancelled']);
    }

}
