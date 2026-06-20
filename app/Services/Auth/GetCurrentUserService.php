<?php

namespace App\Services\Auth;

use App\Models\User;

class GetCurrentUserService
{
    /**
     * Find the local user by Firebase UID and return it with role/zone loaded.
     *
     * Used by the GET /api/auth/me endpoint to hydrate the authenticated
     * session for the React frontend.
     *
     * @param  string  $firebaseUid
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException  404 when user not found.
     */
    public function execute(string $firebaseUid): User
    {
        /** @var User|null $user */
        $user = User::where('firebase_uid', $firebaseUid)
            ->with('role', 'zone')
            ->first();

        if ($user === null) {
            abort(response()->json([
                'status'  => 'error',
                'message' => 'Authenticated Firebase user has no local account. Please register first.',
            ], 404));
        }

        return $user;
    }
}
