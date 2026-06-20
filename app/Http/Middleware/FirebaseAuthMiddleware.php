<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Symfony\Component\HttpFoundation\Response;

class FirebaseAuthMiddleware
{
  /**
   * Handle an incoming request.
   * Verifies Firebase ID token and attaches user to request.
   */

  public function handle(Request $request, Closure $next): Response
  {
    $token = $this->getBearerToken($request);

    if (!$token) {
      return response()->json([
        'message' => 'Missing authentication token',
        'status' => 'error',
      ], 401);
    }

    try {

      $auth = Firebase::auth();
      $verifiedIdToken = $auth->verifyIdToken($token);
      // $firebaseAuth = app(FirebaseAuth::class);
      // $verifiedIdToken = $firebaseAuth->verifyIdToken($token);

      $uid = $verifiedIdToken->claims()->get('sub');

      // Find user
      $user = User::where('firebase_uid', $uid)->first();

      if (!$user) {
        return response()->json([
          'message' => 'User not found in database',
          'status' => 'error',
        ], 404);
      }

      // attach user to request
      $request->setUserResolver(fn() => $user);

      return $next($request);

    } catch (FailedToVerifyToken $e) {
      return response()->json([
        'message' => 'Invalid authentication token',
      ], 401);

    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Authentication failed',
        'error' => $e->getMessage(),
      ], 401);
    }
  }

  /**
   * Extract Bearer token from Authorization header.
   */
  private function getBearerToken(Request $request): ?string
  {
    $header = $request->header('Authorization');

    if (!$header || !str_starts_with($header, 'Bearer ')) {
      return null;
    }

    return substr($header, 7);
  }
}
