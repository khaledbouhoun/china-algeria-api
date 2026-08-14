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
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes are automatically prefixed with /api by RouteServiceProvider.
|
*/

// ── Health check ────────────────────────────────────────────────────────────
Route::get('/test', [TestController::class, 'index']);

// ── Auth routes ─────────────────────────────────────────────────────────────
//
// /register  — uses the dedicated Firebase token middleware to verify the
//              Authorization Bearer token before creating the local account.
// /login     — uses the same middleware so the token is read from the header.
// /me        — uses the same middleware for authenticated profile access.

Route::middleware('firebase.token:false')->group(function () {
  Route::post('/register', [AuthController::class, 'register']);
});
Route::middleware('firebase.token')->group(function () {
  Route::get('/me', [AuthController::class, 'me']);
});
Route::middleware([
  'firebase.token',
  'firebase.user',
])->group(function () {

  // ── API routes ──────────────────────────────────────────────────────────────_
  // All routes below require a valid Firebase token and a corresponding local user.

  // ── Auth & Sessions ─────────────────────────────────────────────────────────
  Route::post('/login', [AuthController::class, 'login']);
  Route::apiResource('user_sessions', UserSessionController::class);

  // ── System & Reference Data (Lookups) ───────────────────────────────────────
  Route::apiResource('roles', RoleController::class);
  Route::apiResource('statuses', StatusController::class);
  Route::apiResource('countries', CountryController::class);
  Route::apiResource('zones', ZoneController::class);

  // ── Users, Documents & Financials ───────────────────────────────────────────

  Route::apiResource('users', UserController::class);
  Route::apiResource('visas', VisaController::class);
  Route::apiResource('wallets', WalletController::class);
  Route::apiResource('wallet_transactions', WalletTransactionController::class);

  // ── Orders Domain ───────────────────────────────────────────────────────────
  Route::post('orders/items', [OrderController::class, 'createWithItems']);

  Route::apiResource('orders', OrderController::class);

  Route::post('order_items/receive', [OrderItemController::class, 'receive']);
  Route::apiResource('order_items', OrderItemController::class);
  Route::apiResource('order_item_steps', OrderItemStepController::class);
  Route::apiResource('order_item_images', OrderItemImageController::class);

  // ── Packages & Logistics Domain ─────────────────────────────────────────────
  Route::post('packages/items', [PackageController::class, 'createWithItems']);
  Route::post('packages/receive', [PackageController::class, 'receive']);
  Route::apiResource('packages', PackageController::class);
  Route::apiResource('package_steps', PackageStepController::class);

  Route::apiResource('package_items', PackageItemController::class);
  Route::apiResource('package_item_steps', PackageItemStepController::class);
  Route::apiResource('package_item_receptions', PackageItemReceptionController::class);

});
