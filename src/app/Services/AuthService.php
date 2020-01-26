<?php

namespace App\Services;

use App\Enums\UserError;
use App\Models\Role;
use App\Models\User;
use App\Models\Token;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class AuthService
{
    public function login(array $attributes)
    {
        $credentials = ['email' => $attributes['email'], 'password' => $attributes['password']];

        if (!Auth::attempt($credentials)) {
            throw new AuthorizationException('The credentials are incorrect.', UserError::INVALID_CREDENTIALS);
        }

        $dbToken = Token::where(['user_id' => Auth::user()->id])->first();

        $time = Carbon::now()->timestamp;

        $accessToken = (new Builder())
            ->setHeader('alg', 'HS256')
            ->setHeader('typ', 'JWT')
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy(md5(Auth::user()->id . strval($time + 3600)))
            ->issuedAt($time)
            ->expiresAt($time + 3600)
            ->withClaim('sub', Auth::user()->id)
            ->getToken(new Sha256(), new Key(config('app.key')));

        $refreshToken = (new Builder())
            ->setHeader('alg', 'HS256')
            ->setHeader('typ', 'JWT')
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy(md5(Auth::user()->id . strval($time + 3600)))
            ->issuedAt($time)
            ->expiresAt($time + 604800)
            ->withClaim('aud', Auth::user()->id)
            ->getToken(new Sha256(), new Key(config('app.key')));

        $dbToken = new Token([
            'type' => 'refresh',
            'token' => strval($refreshToken),
            'user_id' => Auth::user()->id,
            'expires_at' => Carbon::createFromTimestamp($refreshToken->getClaim('exp'))
        ]);

        $dbToken->save();

        Log::info('User logged in', ['email' => $attributes['email']]);

        $data = ['access_token' => strval($accessToken), 'refresh_token' => $dbToken->token];

        return $data;
    }

    public function refresh(string $token)
    {
        $dbToken = Token::where(['token' => $token])->first();

        if (is_null($dbToken)) {
            throw new AuthorizationException('Token is invalid', UserError::INVALID_REFRESH_TOKEN);
        }

        $time = Carbon::now()->timestamp;

        $accessToken = (new Builder())
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy(md5($dbToken->user_id . strval($time)))
            ->issuedAt($time)
            ->expiresAt($time + 3600)
            ->withClaim('sub', $dbToken->user_id)
            ->getToken(new Sha256, new Key(config('app.key')));

        $refreshToken = (new Builder())
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy(md5($dbToken->user_id . strval($time)))
            ->issuedAt($time)
            ->expiresAt($time + 604800)
            ->withClaim('sub', $dbToken->user_id)
            ->getToken(new Sha256(), new Key(config('app.key')));

        $dbToken = new Token([
            'type' => 'refresh',
            'token' => strval($refreshToken),
            'user_id' => $dbToken->user_id,
            'expires_at' => Carbon::createFromTimestamp($refreshToken->getClaim('exp'))
        ]);

        $dbToken->save();

        $data = ['access_token' => strval($accessToken), 'refresh_token' => $dbToken->token];

        return $data;
    }

    public function register(array $attributes)
    {
        $user = new User([
            'name' => $attributes['name'],
            'surname' => $attributes['surname'],
            'email' => $attributes['email'],
            'password' => bcrypt($attributes['password'])
        ]);

        $role = Role::where(['role' => 'admin'])->first();

        $user->role_id = $role['id'];

        $user->save();

        $user->sendEmailVerificationNotification();

        return $this->login($attributes);
    }

    public function logout(string $token)
    {
        $dbToken = Token::where(['token' => $token])->first();

        if (is_null($dbToken)) {
            throw new AuthorizationException('Token is invalid', UserError::INVALID_REFRESH_TOKEN);
        }

        $dbToken->delete();
    }
}
