<?php

namespace App\Auth;

use Lcobucci\JWT\Parser;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Lcobucci\JWT\ValidationData;
use Illuminate\Auth\GuardHelpers;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;

class JwtGuard implements Guard
{
    use GuardHelpers;

    protected $request;

    protected $key;

    public function __construct(UserProvider $provider, Request $request, $key = 'api_token')
    {
        $this->key = $key;
        $this->request = $request;
        $this->provider = $provider;
    }

    public function user()
    {

        if (!is_null($this->user)) {
            return $this->user;
        }

        try {
            $token = (new Parser)->parse($this->getTokenForRequest());

            $data = new ValidationData;
            $data->setIssuer($token->getClaim('iss'));
            $data->setAudience($token->getClaim('aud'));
            $data->setSubject($token->getClaim('sub'));

            if (!$token->verify(new Sha256(), config('app.key')) || !$token->validate($data)) {
                return;
            }

            return $this->user = $this->provider->retrieveById($token->getClaim('sub'));
        } catch (InvalidArgumentException $exception) {
            return;
        }
    }

    public function getTokenForRequest()
    {
        $token = $this->request->query($this->key);

        if (empty($token)) {
            $token = $this->request->input($this->key);
        }

        if (empty($token)) {
            $token = $this->request->bearerToken();
        }

        if (empty($token)) {
            $token = $this->request->getPassword();
        }

        return $token;
    }

    public function validate(array $credentials = [])
    {

        if (empty($credentials['sub'])) {
            return false;
        }

        if ($this->provider->retrieveById($credentials['sub'])) {
            return true;
        }

        return false;
    }
}
