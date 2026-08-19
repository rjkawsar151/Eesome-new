<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->validator->errors()->first() ?: 'Validation failed.',
                        'errors'  => $e->validator->errors(),
                    ], 422);
                }

                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException || $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The requested item or resource was not found.',
                    ], 404);
                }

                if (! config('app.debug')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'An unexpected error occurred. Please try again or contact support.',
                    ], 500);
                }
            }

            return null;
        });
    }
}
