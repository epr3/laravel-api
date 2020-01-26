<?php

use App\Models\Token;
use Faker\Generator as Faker;

$factory->define(Token::class, function (Faker $faker) {
    return [
        'token' => 'random',
        'expires_at' => now(),
        'type' => 'refresh',
        'user_id' => ''
    ];
});
