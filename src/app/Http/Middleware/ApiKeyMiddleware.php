<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Illuminate\Auth\Access\AuthorizationException;
use Closure;

class ApiKeyMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!is_null($request->query('api_key'))) {
            $company = Company::where(['api_key' => $request->query('api_key')])->count();
            if ($company > 0) {
                return $next($request);
            } else {
                throw new AuthorizationException('Invalid api key provided!');
            }
        } else {
            throw new AuthorizationException('Invalid api key provided!');
        }
    }
}
