<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_no', 'customer_id', 'coupon_id', 'status', 'subtotal', 'discount_total', 'tax_total',
        'grand_total', 'payment_method', 'customer_name', 'customer_phone', 'customer_email',
        'shipping_address', 'notes', 'delivered_at', 'cancelled_at', 'stock_deducted',
    ];

    protected function casts(): array
    {
        return ['delivered_at' => 'datetime', 'cancelled_at' => 'datetime', 'stock_deducted' => 'boolean'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
