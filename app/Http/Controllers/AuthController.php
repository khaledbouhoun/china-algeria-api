<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Get current authenticated user info.
     * 
     * Frontend uses role_id to determine UI/permissions.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
                'status' => 'error',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'firebase_uid' => $user->firebase_uid,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'role' => $user->role?->name,
                    'zone_id' => $user->zone_id,
                    'zone' => $user->zone?->name,
                    'status' => $user->status,
                ],
            ],
        ], 200);
    }
}
