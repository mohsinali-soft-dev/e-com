<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'minimum_order', 'maximum_discount', 'expires_at', 'is_active'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'is_active' => 'boolean'];
    }

    public function isUsable(float $subtotal): bool
    {
        return $this->is_active
            && (! $this->expires_at || $this->expires_at->isFuture())
            && $subtotal >= (float) $this->minimum_order;
    }

    public function discountFor(float $subtotal): float
    {
        if (! $this->isUsable($subtotal)) {
            return 0;
        }
        $discount = $this->type === 'percentage' ? $subtotal * ((float) $this->value / 100) : (float) $this->value;
        if ($this->maximum_discount !== null) {
            $discount = min($discount, (float) $this->maximum_discount);
        }

        return round(min($discount, $subtotal), 2);
    }
}
