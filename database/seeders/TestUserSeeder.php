<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        $testUsers = [
            ['name' => 'Admin Test', 'email' => 'admin@test.com'],
            ['name' => 'User Test', 'email' => 'user@test.com'],
            ['name' => 'Demo Test', 'email' => 'demo@test.com'],
        ];

        foreach($testUsers as $userData) {
            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now()
            ]);
        }
    }
}
