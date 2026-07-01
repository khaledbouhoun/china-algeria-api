<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserSessionController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WalletTransactionController;
use App\Http\Controllers\Api\VisaController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\OrderItemStepController;
use App\Http\Controllers\Api\OrderItemImageController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PackageStepController;
use App\Http\Controllers\Api\PackageItemController;
use App\Http\Controllers\Api\PackageItemStepController;
use App\Http\Controllers\Api\PackageItemReceptionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes are automatically prefixed with /api by RouteServiceProvider.
|
*/

// ── Health check ────────────────────────────────────────────────────────────
Route::get('/health', fn() => response()->json(['status' => 'ok']));

// ── Auth routes ─────────────────────────────────────────────────────────────
//
// All three endpoints are protected by the FirebaseAuthenticate middleware,
// which verifies the Bearer token and stores firebase_user on $request->attributes.
//
// /register  — token proves identity; allows unregistered UID (no local user required)
// /login     — token proves identity; local user must already exist
// /me        — token proves identity; local user must already exist
//
Route::middleware('firebase.auth')->group(function () {
  Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
  Route::post('/login',    [AuthController::class, 'login'])->name('auth.login');
  Route::get('/me',        [AuthController::class, 'me'])->name('auth.me');

  Route::apiResource('roles', RoleController::class);
  Route::apiResource('zones', ZoneController::class);
  Route::apiResource('countries', CountryController::class);
  Route::apiResource('statuses', StatusController::class);
  Route::apiResource('users', UserController::class);
  Route::apiResource('user_sessions', UserSessionController::class);
  Route::apiResource('wallets', WalletController::class);
  Route::apiResource('wallet_transactions', WalletTransactionController::class);
  Route::apiResource('visas', VisaController::class);
  Route::apiResource('orders', OrderController::class);
  Route::apiResource('order_items', OrderItemController::class);
  Route::apiResource('order_item_steps', OrderItemStepController::class);
  Route::apiResource('order_item_images', OrderItemImageController::class);
  Route::apiResource('packages', PackageController::class);
  Route::apiResource('package_steps', PackageStepController::class);
  Route::apiResource('package_items', PackageItemController::class);
  Route::apiResource('package_item_steps', PackageItemStepController::class);
  Route::apiResource('package_item_receptions', PackageItemReceptionController::class);
});
