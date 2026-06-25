<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'brand_id', 'unit_id', 'name', 'slug', 'sku', 'description', 'image_path',
        'sale_type', 'purchase_price', 'selling_price', 'stock_quantity', 'low_stock_alert',
        'has_variants', 'is_active',
    ];

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }
    public function variants(): HasMany { return $this->hasMany(ProductVariant::class); }
    public function barcodes(): HasMany { return $this->hasMany(ProductBarcode::class); }
}
