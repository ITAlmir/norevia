<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class Handler extends ExceptionHandler
{
    // ... ostalo (dontReport, dontFlash itd)

    public function render($request, Throwable $e)
    {
        if ($e instanceof TooManyRequestsHttpException) {
            if (!$request->expectsJson()) {
                return response()->view('errors.429', [], 429);
            }

            return response()->json([
                'message' => 'Too many download requests. Please wait a bit and try again.',
            ], 429);
        }

        return parent::render($request, $e);
    }
}
