<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FirebaseUserMiddleware
{
  public function handle(Request $request, Closure $next): Response
  {
    $firebaseUid = $request->attributes->get('firebase_uid');

    $user = User::where('firebase_uid', $firebaseUid)->first();

    if (!$user) {
      return response()->json([
        'status' => 'error',
        'message' => 'User is not registered.',
      ], Response::HTTP_NOT_FOUND);
    }

    if ($user->proved_at == null) {
      return response()->json([
        'status' => 'error',
        'message' => 'User is not proved.',
      ], Response::HTTP_FORBIDDEN);
    }

    $request->setUserResolver(fn() => $user);

    return $next($request);
  }
}
