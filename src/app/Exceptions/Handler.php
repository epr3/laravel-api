<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if (app()->environment() != "testing" && app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'type' => array_slice(explode("\\", get_class($exception)), -1)[0],
                'message' => 'Entry for ' . str_replace('App\\', '', $exception->getModel()) . ' not found',
                'code' => $exception->getCode()
            ], 404);
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'type' => array_slice(explode("\\", get_class($exception)), -1)[0],
                'errors' => $exception->validator->messages(),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ], 422);
        }

        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'type' => array_slice(explode("\\", get_class($exception)), -1)[0],
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ], 403);
        }

        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'type' => array_slice(explode("\\", get_class($exception)), -1)[0],
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ], 401);
        }

        if ($exception instanceof Exception) {
            return response()->json([
                'type' => array_slice(explode("\\", get_class($exception)), -1)[0],
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
