<?php

namespace App\Http\Middleware;

use App\Enums\UserError;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EnsureEmailIsVerified
{
    public function handle($request, Closure $next)
    {
        if (!$request->user() || ($request->user() instanceof MustVerifyEmail &&
                !$request->user()->hasVerifiedEmail())
        ) {
            throw new AuthorizationException('Your email address is not verified', UserError::EMAIL_NOT_VERIFIED);
        }

        return $next($request);
    }
}
