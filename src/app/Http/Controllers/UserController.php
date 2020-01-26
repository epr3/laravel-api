<?php


namespace App\Http\Controllers;

use App\Services\UserService;
use App\Transformers\UserTransformer;

class UserController extends Controller
{
    public function profile()
    {

        $userService = app(UserService::class);

        $user = $userService->getProfile();

        $response = fractal($user)->transformWith(UserTransformer::class)->toArray();

        return response()->json($response);
    }
}
