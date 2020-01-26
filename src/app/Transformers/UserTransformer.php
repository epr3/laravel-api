<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{

    public function transform(User $user)
    {
        return [
            'id' => $user['id'],
            'name' => $user['name'],
            'surname' => $user['surname'],
            'avatar' => $user['avatar'],
            'email' => $user['email'],
            'email_is_verified' => $user->hasVerifiedEmail(),
            'role' => $user['role']['role']
        ];
    }
}
