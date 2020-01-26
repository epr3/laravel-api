<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function retrieved(User $user)
    {
        Log::info('User retrieved', ['id' => $user->id]);
    }

    public function created(User $user)
    {
        Log::info('User created', ['id' => $user->id]);
    }

    public function updated(User $user)
    {
        Log::info('User updated', ['id' => $user->id]);
    }

    public function deleted(User $user)
    {
        Log::info('User deleted', ['id' => $user->id]);
    }
}
