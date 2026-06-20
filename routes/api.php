<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes are automatically prefixed with /api by RouteServiceProvider.
|
*/

// ── Health check ────────────────────────────────────────────────────────────
Route::get('/health', fn () => response()->json(['status' => 'ok']));

// ── Auth routes ─────────────────────────────────────────────────────────────
//
// All three endpoints are protected by the FirebaseAuthenticate middleware,
// which verifies the Bearer token and stores firebase_user on $request->attributes.
//
// /register  — token proves identity; allows unregistered UID (no local user required)
// /login     — token proves identity; local user must already exist
// /me        — token proves identity; local user must already exist
//
Route::prefix('auth')
    ->middleware('firebase.authenticate')
    ->group(function (): void {
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('/login',    [AuthController::class, 'login'])->name('auth.login');
        Route::get('/me',        [AuthController::class, 'me'])->name('auth.me');
    });
