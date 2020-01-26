<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{

    public function login(AuthRequest $request)
    {

        $authService = app(AuthService::class);

        $tokenResult = $authService->login($request->only(['email', 'password']));

        Cookie::queue('refresh_token', $tokenResult['refresh_token'], 10080);

        return response()
            ->json(['access_token' => $tokenResult['access_token']]);
    }

    public function register(RegisterRequest $request)
    {

        $authService = app(AuthService::class);

        $tokenResult = $authService->register($request->only(['email', 'password', 'name', 'surname']));

        Cookie::queue('refresh_token', $tokenResult['refresh_token'], 10080);

        return response()
            ->json(['access_token' => $tokenResult['access_token']], 201);
    }

    public function refresh(Request $request)
    {
        $authService = app(AuthService::class);

        $cookie = $request->cookie('refresh_token');

        $tokenResult = $authService->refresh($cookie);

        Cookie::queue('refresh_token', $tokenResult['refresh_token'], 10080);

        return response()
            ->json(['access_token' => $tokenResult['access_token']]);
    }

    public function logout(Request $request)
    {
        $authService = app(AuthService::class);

        $cookie = $request->cookie('refresh_token');

        $authService->logout($cookie);

        Cookie::queue(Cookie::forget('refresh_token'));

        return response(null, 204);
    }
}
