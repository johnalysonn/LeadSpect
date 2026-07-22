<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Garante que haja apenas 1 único usuário cadastrado via seed
        User::query()->delete();

        User::create([
            'name' => 'johnalyson',
            'email' => 'johnalyson@gmail.com',
            'password' => Hash::make('johnalyson123'),
            'email_verified_at' => now(),
            'auth_provider' => 'email',
        ]);
    }
}
