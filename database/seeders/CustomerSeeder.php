<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::updateOrCreate(['phone' => '03001234567'], [
            'name' => 'Demo Customer',
            'email' => 'customer@example.com',
            'address' => 'Demo Street 1, Karachi',
        ]);
    }
}
