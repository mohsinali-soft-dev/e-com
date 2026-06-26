<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Store Manager', 'email' => 'manager@example.com', 'role' => 'manager'],
            ['name' => 'Store Cashier', 'email' => 'cashier@example.com', 'role' => 'cashier'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], [
                'name' => $user['name'],
                'password' => 'password',
                'role' => $user['role'],
                'is_active' => true,
            ]);
        }
    }
}
