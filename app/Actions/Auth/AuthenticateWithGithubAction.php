<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthenticateWithGithubAction
{
    /**
     * Authenticate or register a user using GitHub OAuth payload.
     *
     * Rules:
     * - If account linked to GitHub exists (github_id), log in.
     * - If user with same email exists, link GitHub account and log in.
     * - If user does not exist, create new account automatically.
     * - Never create duplicate users.
     */
    public function execute(array $data): User
    {
        $githubId = $data['github_id'] ?? null;
        $email = $data['email'] ?? null;

        $user = null;

        if (!empty($githubId)) {
            $user = User::where('github_id', $githubId)->first();
        }

        if (!$user && !empty($email)) {
            $user = User::where('email', $email)->first();
        }

        if ($user) {
            $user->update([
                'name' => $user->name ?: ($data['name'] ?? 'Usuário GitHub'),
                'github_id' => $githubId ?? $user->github_id,
                'avatar' => $data['avatar'] ?? $user->avatar,
                'auth_provider' => $user->github_id ? $user->auth_provider : 'github',
            ]);
        } else {
            $user = User::create([
                'name' => $data['name'] ?? 'Usuário GitHub',
                'email' => $email,
                'github_id' => $githubId,
                'avatar' => $data['avatar'] ?? null,
                'auth_provider' => 'github',
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, remember: true);

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        return $user;
    }
}
