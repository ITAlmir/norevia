<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Prvo provjeri da li tvoj nalog postoji
        $yourAccount = User::where('email', 'almirredzic@live.com')->first();
        
        if (!$yourAccount) {
            // Kreiraj tvoj nalog
            User::create([
                'name' => 'Almir',
                'email' => 'almirredzic@live.com', // PROMIJENI!
                'password' => Hash::make('Almir1982'), // PROMIJENI!
                'role' => 'super_admin',
            ]);
        } else {
            // Ažuriraj postojeći nalog
            $yourAccount->update([
                'role' => 'super_admin'
            ]);
        }

        // Kreiraj test korisnike
        User::create([
            'name' => 'Almir',
            'email' => 'almirredzic@live.com',
            'password' => Hash::make('Almir1982'),
            'role' => 'super_admin',
        ]);

        User::create([
            'name' => 'Content Admin',
            'email' => 'content@admin.com',
            'password' => Hash::make('123'),
            'role' => 'content_admin',
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('123'),
            'role' => 'user',
        ]);

        echo "Users created/updated:\n";
        echo "- Your Account: almirredzic@live.com / Almir1982 (SUPER ADMIN)\n";
        echo "- Test Super Admin: almirredzic@live.com / 123\n";
        echo "- Test Content Admin: content@admin.com / 123\n";
        echo "- Test Regular User: user@example.com / 123\n";
    }
}