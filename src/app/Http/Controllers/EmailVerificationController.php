<?php

namespace App\Http\Controllers;

use App\Enums\UserError;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    use VerifiesEmails;

    public function verify(Request $request)
    {
        if (!hash_equals((string) $request->route('id'), (string) $request->user()->getKey())) {
            throw new AuthorizationException;
        }

        if (!hash_equals((string) $request->route('hash'), sha1($request->user()->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email verified!']);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json(['message' => 'Email verified!']);
    }

    public function resend(Request $request)
    {

        if ($request->user()->hasVerifiedEmail()) {
            throw new AuthorizationException("User has already verified his mail", UserError::ALREADY_VERIFIED);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'The notification has been resubmitted']);
    }
}
