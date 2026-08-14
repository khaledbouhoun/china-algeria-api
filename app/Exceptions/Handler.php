<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
  protected $dontFlash = [
    'current_password',
    'password',
    'password_confirmation',
  ];

  public function register(): void
  {
    $this->reportable(function (Throwable $e) {
      //
    });

    // 404 - model not found (findOrFail, whereKey()->firstOrFail(), etc.)
    $this->renderable(function (ModelNotFoundException $e, Request $request) {
      if ($request->is('api/*')) {
        return $this->apiError('Resource not found.', 404);
      }
    });

    // 404 - route/URI doesn't exist at all (bad endpoint, wrong id type, etc.)
    $this->renderable(function (NotFoundHttpException $e, Request $request) {
      if ($request->is('api/*')) {
        return $this->apiError('Route not found.', 404);
      }
    });

    // 422 - form request validation failures
    $this->renderable(function (ValidationException $e, Request $request) {
      if ($request->is('api/*')) {
        return response()->json([
          'success' => false,
          'message' => 'Validation failed.',
          'data' => null,
          'errors' => $e->errors(),
        ], 422);
      }
    });

    // 403 - Gate/Policy denial (if you ever add authorize() calls later)
    $this->renderable(function (AuthorizationException $e, Request $request) {
      if ($request->is('api/*')) {
        return $this->apiError('You are not authorized to perform this action.', 403);
      }
    });

    // DB-level failures — catches your 25P02 case and any other PDO/query exception
    $this->renderable(function (QueryException $e, Request $request) {
      if ($request->is('api/*')) {
        report($e); // still log the real error for you

        $sqlState = $e->errorInfo[0] ?? null;

        // 25P02 = current transaction is aborted, commands ignored until end of transaction block
        if ($sqlState === '25P02') {
          return $this->apiError(
            'A previous operation failed and left the transaction in an invalid state. Please retry the request.',
            409
          );
        }

        // 23505 = unique_violation
        if ($sqlState === '23505') {
          return $this->apiError('A record with these details already exists.', 409);
        }

        // 23503 = foreign_key_violation
        if ($sqlState === '23503') {
          return $this->apiError('This action references a record that does not exist or cannot be modified.', 409);
        }

        return $this->apiError('A database error occurred. Please try again later ' . $e->getMessage() . '.', 500);
      }
    });

    // Catch-all — never let a raw exception/stack trace leak to the frontend
    $this->renderable(function (Throwable $e, Request $request) {
      if ($request->is('api/*')) {
        report($e);

        return $this->apiError(
          app()->isProduction() ? 'Something went wrong. Please try again later.' : $e->getMessage(),
          500
        );
      }
    });
  }

  private function apiError(string $message, int $status): JsonResponse
  {
    return response()->json([
      'success' => false,
      'message' => $message,
      'data' => null,
    ], $status);
  }
}
