<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class DeleteUserAction
{
    /**
     * Safely delete a user, preventing self-deletion of current authenticated user if desired.
     */
    public function execute(User $user, User $authenticatedUser): bool
    {
        if ($user->id === $authenticatedUser->id) {
            throw ValidationException::withMessages([
                'user' => ['Você não pode excluir sua própria conta de usuário enquanto estiver autenticado.'],
            ]);
        }

        return $user->delete();
    }
}
