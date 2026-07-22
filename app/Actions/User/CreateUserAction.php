<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    /**
     * Create a user from the management panel.
     */
    public function execute(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'auth_provider' => 'email',
            'email_verified_at' => now(),
        ]);
    }
}
