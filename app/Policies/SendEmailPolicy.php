<?php

namespace App\Policies;

use App\Models\SendEmail;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SendEmailPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SendEmail $sendEmail): bool
    {
        return $user->id === $sendEmail->user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SendEmail $sendEmail): bool
    {
        return $user->id === $sendEmail->user->id;
    }
}
