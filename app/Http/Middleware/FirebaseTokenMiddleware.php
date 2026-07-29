<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Symfony\Component\HttpFoundation\Response;

class FirebaseTokenMiddleware
{
  /**
   * Verify Firebase ID Token and attach Firebase user
   * information to the current request.
   */


  #
  public function handle(Request $request, Closure $next, string $needVerify = 'true'): Response
  {
    $needVerify = filter_var($needVerify, FILTER_VALIDATE_BOOLEAN);

    $token = $request->bearerToken();

    if (!$token) {
      return $this->unauthorized('Missing authentication token.');
    }

    try {
      $verifiedToken = Firebase::auth()->verifyIdToken($token);

      $claims = $verifiedToken->claims();

      $firebaseUser = [
        'uid' => $claims->get('sub'),
        'email' => $claims->get('email'),
        'email_verified' => (bool) $claims->get('email_verified', false),
        'name' => $claims->get('name'),
        'picture' => $claims->get('picture'),
      ];

      if ($needVerify && !$firebaseUser['email_verified']) {
        return response()->json([
          'status' => 'error',
          'message' => 'Email address is not verified.',
        ], Response::HTTP_FORBIDDEN);
      }

      $request->attributes->set('firebase_uid', $firebaseUser['uid']);
      $request->attributes->set('firebase_user', $firebaseUser);

      return $next($request);

    } catch (FailedToVerifyToken $e) {

      return $this->unauthorized('Invalid or expired authentication token.');

    } catch (\Throwable $e) {

      Log::error('Firebase authentication failed.', [
        'exception' => $e,
      ]);

      return $this->unauthorized('Authentication failed (unauthorized).');
    }
  }

  /**
   * Return a standard unauthorized response.
   */
  private function unauthorized(string $message): Response
  {
    return response()->json([
      'status' => 'error',
      'message' => $message,
    ], Response::HTTP_UNAUTHORIZED);
  }
}
