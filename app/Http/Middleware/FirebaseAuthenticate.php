<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Symfony\Component\HttpFoundation\Response;

class FirebaseAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * Reads the Authorization Bearer token, verifies it against Firebase,
     * extracts uid / email / email_verified and stores them on the request
     * attributes so downstream controllers and services can use them.
     *
     * The middleware does NOT look up the local DB user — that is the
     * responsibility of each service (register / login / me).
     *
     * Returns 401 when the token is missing or invalid.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->extractBearerToken($request);

        if ($token === null) {
            return $this->unauthorizedResponse('Missing authentication token.');
        }

        try {
            $auth            = Firebase::auth();
            $verifiedToken   = $auth->verifyIdToken($token);
            $claims          = $verifiedToken->claims();

            $request->attributes->set('firebase_user', [
                'uid'            => $claims->get('sub'),
                'email'          => $claims->get('email'),
                'email_verified' => (bool) $claims->get('email_verified', false),
            ]);

            return $next($request);

        } catch (FailedToVerifyToken $e) {
            return $this->unauthorizedResponse('Invalid or expired authentication token.');

        } catch (\Throwable $e) {
            return $this->unauthorizedResponse('Authentication failed.');
        }
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Extract the raw token string from the Authorization header.
     */
    private function extractBearerToken(Request $request): ?string
    {
        $header = $request->header('Authorization', '');

        if (! str_starts_with((string) $header, 'Bearer ')) {
            return null;
        }

        $token = trim(substr($header, 7));

        return $token !== '' ? $token : null;
    }

    /**
     * Build a consistent 401 JSON response.
     */
    private function unauthorizedResponse(string $message): Response
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
        ], Response::HTTP_UNAUTHORIZED);
    }
}
