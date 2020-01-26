<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function getProfile()
    {
        $currentUser = Auth::user();

        $user = User::find($currentUser->id);

        return $user;
    }
}
