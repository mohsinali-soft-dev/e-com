<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function create()
    {
        abort_if(empty(session('cart', [])), 422, 'Your cart is empty.');

        return view('store.checkout', ['cart' => collect(session('cart')), 'setting' => Setting::current()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'], 'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'], 'address' => ['required', 'string', 'max:2000'],
            'coupon_code' => ['nullable', 'string', 'max:50'], 'payment_method' => ['required', 'in:cash_on_delivery,bank_transfer'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $data['email'] ??= null;
        $data['coupon_code'] ??= null;
        $data['notes'] ??= null;
        $cart = session('cart', []);
        if (! $cart) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $order = DB::transaction(function () use ($data, $cart) {
            $customer = Customer::where('phone', $data['phone'])
                ->when($data['email'], fn ($query, $email) => $query->orWhere('email', $email))
                ->first();
            $customer ??= new Customer;
            $customer->fill(['name' => $data['name'], 'phone' => $data['phone'], 'email' => $data['email'], 'address' => $data['address']])->save();
            $items = [];
            $subtotal = 0;
            foreach ($cart as $cartItem) {
                $product = Product::lockForUpdate()->where('is_active', true)->findOrFail($cartItem['product_id']);
                $variant = $cartItem['variant_id'] ? ProductVariant::lockForUpdate()->where('product_id', $product->id)->where('is_active', true)->findOrFail($cartItem['variant_id']) : null;
                if ($product->has_variants && ! $variant) {
                    throw ValidationException::withMessages(['cart' => $product->name.' requires a variant.']);
                }
                $stockable = $variant ?: $product;
                $quantity = (float) $cartItem['quantity'];
                if ($product->sale_type === 'piece' && floor($quantity) !== $quantity) {
                    throw ValidationException::withMessages(['cart' => $product->name.' requires a whole quantity.']);
                }
                if ($quantity > (float) $stockable->stock_quantity) {
                    throw ValidationException::withMessages(['cart' => $product->name.' no longer has enough stock.']);
                }
                $unitPrice = (float) ($variant?->selling_price ?? $product->selling_price);
                $lineTotal = round($unitPrice * $quantity, 2);
                $subtotal += $lineTotal;
                $items[] = compact('product', 'variant', 'stockable', 'quantity', 'unitPrice', 'lineTotal');
            }
            $setting = Setting::current();
            $coupon = $data['coupon_code'] ? Coupon::where('code', strtoupper(trim($data['coupon_code'])))->first() : null;
            if ($data['coupon_code'] && (! $coupon || ! $coupon->isUsable($subtotal))) {
                throw ValidationException::withMessages(['coupon_code' => 'Coupon is invalid, expired, or does not meet the minimum order.']);
            }
            $discount = $coupon?->discountFor($subtotal) ?? 0;
            $tax = round(($subtotal - $discount) * ((float) $setting->tax_rate / 100), 2);
            do {
                $orderNo = 'ORD-'.now()->format('YmdHis').random_int(100, 999);
            } while (Order::where('order_no', $orderNo)->exists());
            $order = Order::create([
                'order_no' => $orderNo, 'customer_id' => $customer->id, 'coupon_id' => $coupon?->id,
                'subtotal' => $subtotal, 'discount_total' => $discount, 'tax_total' => $tax, 'grand_total' => $subtotal - $discount + $tax,
                'payment_method' => $data['payment_method'], 'customer_name' => $data['name'], 'customer_phone' => $data['phone'],
                'customer_email' => $data['email'], 'shipping_address' => $data['address'], 'notes' => $data['notes'],
            ]);
            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id, 'product_variant_id' => $item['variant']?->id,
                    'product_name' => $item['product']->name, 'variant_name' => $item['variant']?->name,
                    'sku' => $item['variant']?->sku ?? $item['product']->sku, 'quantity' => $item['quantity'],
                    'unit_price' => $item['unitPrice'], 'purchase_price' => $item['variant']?->purchase_price ?? $item['product']->purchase_price,
                    'line_total' => $item['lineTotal'],
                ]);
                $before = (float) $item['stockable']->stock_quantity;
                $item['stockable']->update(['stock_quantity' => $before - $item['quantity']]);
                StockAdjustment::create(['product_id' => $item['product']->id, 'product_variant_id' => $item['variant']?->id, 'type' => 'decrease', 'quantity' => $item['quantity'], 'stock_before' => $before, 'stock_after' => $before - $item['quantity'], 'reason' => 'Online order '.$orderNo]);
            }

            return $order;
        });
        session()->forget('cart');

        return redirect()->route('orders.track.show', ['order' => $order, 'phone' => $order->customer_phone])->with('success', 'Order placed successfully.');
    }
}
