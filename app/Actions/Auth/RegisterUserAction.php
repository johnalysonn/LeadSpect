<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{
    /**
     * Register a new user account with hashed password and log them in.
     */
    public function execute(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'auth_provider' => 'email',
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        return $user;
    }
}
