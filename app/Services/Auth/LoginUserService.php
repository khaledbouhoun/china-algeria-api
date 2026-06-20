<?php

namespace App\Services\Auth;

use App\Models\User;

class LoginUserService
{
    /**
     * Resolve the local user from a Firebase UID, update last_login_at,
     * and return the user with role and zone relations loaded.
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
                'message' => 'No local account found for this Firebase user. Please register first.',
            ], 404));
        }

        // Persist the login timestamp.
        $user->last_login_at = now();
        $user->save();

        return $user;
    }
}
