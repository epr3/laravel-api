<?php

namespace App\Enums;

final class UserError
{
    const INVALID_CREDENTIALS = 1;

    const ALREADY_VERIFIED = 2;

    const FAILED_SEND_RESET_LINK = 3;

    const FAILED_RESET_PASSWORD = 4;

    const INVALID_REFRESH_TOKEN = 5;

    const EMAIL_NOT_VERIFIED = 6;
}
