<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::query()->updateOrCreate(['id' => 1], [
            'store_name' => 'E-Com POS Demo Store',
            'currency' => 'Rs.',
            'tax_rate' => 5,
            'invoice_footer' => 'Thank you for shopping with us.',
            'receipt_width' => 80,
            'show_logo_on_receipt' => true,
        ]);
    }
}
