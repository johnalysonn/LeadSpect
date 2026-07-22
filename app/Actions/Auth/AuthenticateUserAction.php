<?php

namespace App\Actions\Auth;

use App\Models\User;

class AuthenticateUserAction
{
    /**
     * Backward compatibility wrapper for AuthenticateWithGithubAction.
     */
    public function execute(array $data): User
    {
        if (empty($data['github_id']) && !empty($data['google_id'])) {
            $data['github_id'] = $data['google_id'];
        }

        return app(AuthenticateWithGithubAction::class)->execute($data);
    }
}
