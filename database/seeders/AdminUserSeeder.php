<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Store Admin',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
