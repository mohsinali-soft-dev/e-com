<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['store_name', 'logo_path', 'favicon_path', 'currency', 'tax_rate', 'invoice_footer', 'receipt_width', 'show_logo_on_receipt'];

    protected function casts(): array
    {
        return ['show_logo_on_receipt' => 'boolean'];
    }

    public static function current(): self
    {
        return static::firstOrCreate([], ['store_name' => 'E-Com POS', 'currency' => 'Rs.']);
    }
}
