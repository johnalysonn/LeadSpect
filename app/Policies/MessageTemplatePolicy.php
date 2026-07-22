<?php

namespace App\Policies;

use App\Models\MessageTemplate;
use App\Models\User;

class MessageTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MessageTemplate $template): bool
    {
        return $user->id === $template->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MessageTemplate $template): bool
    {
        return $user->id === $template->user_id;
    }

    public function delete(User $user, MessageTemplate $template): bool
    {
        return $user->id === $template->user_id;
    }
}
