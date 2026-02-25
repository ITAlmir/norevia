<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Kreiraj SUPER ADMINA (TI)
        User::create([
            'name' => 'Almir Super Admin',
            'email' => 'almirredzic@live.com',
            'password' => Hash::make('Almir1982'),
            'role' => 'super_admin',
        ]);

        // Kreiraj CONTENT ADMIN
        User::create([
            'name' => 'Content Admin',
            'email' => 'content@admin.com',
            'password' => Hash::make('content123'),
            'role' => 'content_admin',
        ]);

        // Kreiraj REGULAR USER
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);

        echo "Users created:\n";
        echo "1. SUPER ADMIN: almirredzic@live.com / Almir1982\n";
        echo "2. CONTENT ADMIN: content@admin.com / content123\n";
        echo "3. REGULAR USER: user@example.com / user123\n";
    }
}