<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'user_id' => (string) Str::uuid(),
                'full_name' => 'Test User',
                'slug' => 'test-user',
                'email' => 'test@example.com',
                'type' => 'user',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'user_id' => (string) Str::uuid(),
                'full_name' => 'Admin',
                'slug' => 'admin',
                'email' => 'admin@example.com',
                'type' => 'admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}