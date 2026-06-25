<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_module_pages_render_successfully(): void
    {
        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.pos.index'))->assertOk();
        $this->get(route('admin.sales.index'))->assertOk();
        $this->get(route('admin.inventory.low-stock'))->assertOk();
        $this->get(route('admin.inventory.adjustments'))->assertOk();
    }

    public function test_product_creation_generates_a_unique_barcode(): void
    {
        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'pc',
            'type' => 'piece',
            'decimal_places' => 0,
            'is_active' => true,
        ]);

        $response = $this->post(route('admin.products.store'), [
            'unit_id' => $unit->id,
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'sale_type' => 'piece',
            'purchase_price' => 10,
            'selling_price' => 15,
            'stock_quantity' => 20,
            'low_stock_alert' => 5,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $product = Product::firstOrFail();
        $this->assertDatabaseHas('product_barcodes', [
            'product_id' => $product->id,
            'is_primary' => true,
        ]);
    }

    public function test_low_stock_alert_cannot_exceed_stock_quantity(): void
    {
        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'pc',
            'type' => 'piece',
            'decimal_places' => 0,
            'is_active' => true,
        ]);

        $this->post(route('admin.products.store'), [
            'unit_id' => $unit->id,
            'name' => 'Invalid Stock Alert Product',
            'sku' => 'INVALID-STOCK-ALERT',
            'sale_type' => 'piece',
            'purchase_price' => 10,
            'selling_price' => 15,
            'stock_quantity' => 5,
            'low_stock_alert' => 10,
            'is_active' => 1,
        ])->assertSessionHasErrors('low_stock_alert');

        $this->assertDatabaseMissing('products', ['sku' => 'INVALID-STOCK-ALERT']);
    }

    public function test_checkout_saves_invoice_and_reduces_stock(): void
    {
        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'pc',
            'type' => 'piece',
            'decimal_places' => 0,
            'is_active' => true,
        ]);
        $product = Product::create([
            'unit_id' => $unit->id,
            'name' => 'Milk',
            'slug' => 'milk',
            'sku' => 'MILK-1',
            'sale_type' => 'piece',
            'purchase_price' => 80,
            'selling_price' => 100,
            'stock_quantity' => 10,
            'low_stock_alert' => 2,
            'is_active' => true,
        ]);
        $barcode = ProductBarcode::create([
            'product_id' => $product->id,
            'barcode' => 'ECM00000101',
            'type' => 'store',
            'is_primary' => true,
        ]);

        $response = $this->postJson(route('admin.pos.checkout'), [
            'paid_amount' => 200,
            'discount' => 10,
            'payment_method' => 'cash',
            'items' => [[
                'id' => $product->id,
                'qty' => 2,
                'barcode' => $barcode->barcode,
            ]],
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('grand_total', 190);

        $this->assertEquals(8, (float) $product->fresh()->stock_quantity);
        $this->assertDatabaseHas('sales', ['subtotal' => 200, 'discount_total' => 10, 'grand_total' => 190]);
        $this->assertDatabaseHas('stock_adjustments', ['product_id' => $product->id, 'type' => 'decrease']);
    }
}
