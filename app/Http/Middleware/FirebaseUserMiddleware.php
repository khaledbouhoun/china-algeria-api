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
    $firebase = $request->attributes->get('firebase');

    $user = User::where('firebase_uid', $firebase['uid'])->first();

    if (!$user) {
      return response()->json([
        'status' => 'error',
        'message' => 'User is not registered.',
      ], Response::HTTP_NOT_FOUND);
    }

    $request->setUserResolver(fn() => $user);

    return $next($request);
  }
}
