<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class PinMiddleware
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
        if (!is_null($request->query('pin'))) {
            $company = Company::where(['pin' => $request->query('pin')])->count();
            if ($company > 0) {
                return $next($request);
            } else {
                throw new AuthorizationException('PIN is invalid!');
            }
        }
        throw new AuthorizationException('PIN is invalid!');
    }
}
