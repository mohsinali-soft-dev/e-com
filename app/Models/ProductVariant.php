<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name', 'sku', 'purchase_price', 'selling_price', 'stock_quantity', 'is_active'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function barcodes(): HasMany { return $this->hasMany(ProductBarcode::class); }
}
