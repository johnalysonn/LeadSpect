<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    /**
     * Update user details and optionally update password.
     */
    public function execute(User $user, array $data): User
    {
        $updateData = [
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        return $user;
    }
}
