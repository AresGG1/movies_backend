<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
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

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->is('api/*')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return parent::unauthenticated($request, $exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
    }
    public function render($request, Throwable $e)
    {

        $statusCode = match (true) {
            $e instanceof ValidationException ||
            $e instanceof InvalidCredentialsException => 400,

            $e instanceof AuthenticationException => 401,
            $e instanceof AuthorizationException => 403,

            $e instanceof ModelNotFoundException ||
            $e instanceof NotFoundHttpException ||
            $e instanceof RouteNotFoundException => 404,

            default => 500,
        };

        $message = $e->getMessage();

        if ($e instanceof ModelNotFoundException) {
            $message = $this->formatNotFoundMessage($e->getModel());
        }

        return response()->json([
            'message' => $message,
        ], $statusCode);
    }

    private function formatNotFoundMessage(string $model): string
    {
        $parts = explode('\\', $model);
        $formattedModelName = strtolower(end($parts));

        return "$formattedModelName not found";
    }
}
