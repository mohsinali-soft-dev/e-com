<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\Sale;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommerceModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_routes_require_authentication_and_roles(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);
        $this->actingAs($cashier)->get(route('admin.pos.index'))->assertOk();
        $this->actingAs($cashier)->get(route('admin.products.index'))->assertForbidden();

        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $this->actingAs($manager)->get(route('admin.products.index'))->assertOk();
        $this->actingAs($manager)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_new_admin_and_storefront_pages_render(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        foreach ([
            route('admin.customers.index'), route('admin.orders.index'), route('admin.coupons.index'),
            route('admin.reports.index'), route('admin.users.index'),
            route('admin.settings.edit'),
        ] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get(route('home'))->assertOk();
        $this->get(route('shop.index'))->assertOk();
        $this->get(route('cart.index'))->assertOk();
        $this->get(route('orders.track'))->assertOk();
        $this->get(route('profile.edit'))->assertOk();
    }

    public function test_variant_creation_generates_barcode(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $product = $this->product();

        $this->actingAs($admin)->post(route('admin.products.variants.store', $product), [
            'name' => '500ml', 'sku' => 'MILK-500', 'purchase_price' => 80,
            'selling_price' => 100, 'stock_quantity' => 10, 'low_stock_alert' => 2, 'is_active' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('product_variants', ['product_id' => $product->id, 'sku' => 'MILK-500']);
        $this->assertDatabaseHas('product_barcodes', ['product_id' => $product->id, 'is_primary' => true]);
    }

    public function test_online_checkout_saves_order_and_cancellation_restores_stock(): void
    {
        $product = $this->product(stock: 10);
        $cart = [
            $product->id.':0' => [
                'key' => $product->id.':0', 'product_id' => $product->id, 'variant_id' => null,
                'name' => $product->name, 'variant_name' => null, 'sku' => $product->sku,
                'price' => 100, 'quantity' => 2, 'image' => null,
            ],
        ];

        $this->withSession(['cart' => $cart])->post(route('checkout.store'), [
            'name' => 'Online Customer', 'phone' => '03001234567', 'email' => 'buyer@example.com',
            'address' => 'Main Street', 'payment_method' => 'cash_on_delivery',
        ])->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertEquals(8, (float) $product->fresh()->stock_quantity);

        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $this->actingAs($manager)->patch(route('admin.orders.status', $order), ['status' => 'cancelled'])->assertSessionHasNoErrors();
        $this->assertEquals(10, (float) $product->fresh()->stock_quantity);
    }

    public function test_sale_return_cannot_exceed_sold_quantity_and_restores_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $product = $this->product(stock: 8);
        $sale = Sale::create([
            'invoice_no' => 'INV-RETURN-1', 'subtotal' => 200, 'grand_total' => 200,
            'paid_amount' => 200, 'change_amount' => 0, 'payment_method' => 'cash', 'status' => 'completed',
        ]);
        $item = $sale->items()->create([
            'product_id' => $product->id, 'product_name' => $product->name, 'sku' => $product->sku,
            'quantity' => 2, 'unit_price' => 100, 'purchase_price' => 70, 'line_total' => 200,
        ]);

        $this->actingAs($admin)->post(route('admin.sales.return.store', $sale), [
            'reason' => 'Customer return', 'items' => [$item->id => 1],
        ])->assertSessionHasNoErrors();

        $this->assertEquals(9, (float) $product->fresh()->stock_quantity);
        $this->assertEquals(1, (float) $item->fresh()->returned_quantity);

        $this->actingAs($admin)->post(route('admin.sales.return.store', $sale), [
            'reason' => 'Invalid return', 'items' => [$item->id => 2],
        ])->assertSessionHasErrors();
    }

    private function product(float $stock = 0): Product
    {
        $unit = Unit::create(['name' => 'Piece '.uniqid(), 'short_name' => 'pc', 'type' => 'piece', 'decimal_places' => 0, 'is_active' => true]);
        $product = Product::create([
            'unit_id' => $unit->id, 'name' => 'Milk '.uniqid(), 'slug' => 'milk-'.uniqid(),
            'sku' => 'MILK-'.uniqid(), 'sale_type' => 'piece', 'purchase_price' => 70,
            'selling_price' => 100, 'stock_quantity' => $stock, 'low_stock_alert' => 0, 'is_active' => true,
        ]);
        ProductBarcode::create(['product_id' => $product->id, 'barcode' => 'BC'.uniqid(), 'type' => 'store', 'is_primary' => true]);

        return $product;
    }
}
