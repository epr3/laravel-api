<?php

namespace App\Http\Middleware;

use App\Enums\UserError;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class RefreshTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasCookie('refresh_token')) {
            throw new AuthorizationException('Token is invalid', UserError::INVALID_REFRESH_TOKEN);
        }

        return $next($request);
    }
}
