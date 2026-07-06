<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\MeRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\UserResource;
use App\Models\User;
use App\Services\Auth\GetCurrentUserService;
use App\Services\Auth\LoginUserService;
use App\Services\Auth\RegisterUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * AuthController
 *
 * Thin orchestrator: delegates all business logic to dedicated services
 * and formats responses via UserResource. No DB queries or conditionals here.
 */
class AuthController extends Controller
{
  public function __construct(
    private readonly RegisterUserService $registerUserService,
    private readonly LoginUserService $loginUserService,
    private readonly GetCurrentUserService $getCurrentUserService,
  ) {
  }

  // -------------------------------------------------------------------------
  // POST /api/auth/register
  // -------------------------------------------------------------------------

  /**
   * Register a new local user account.
   *
   * Firebase has already created the Firebase account client-side.
   * This endpoint creates the matching local DB record.
   *
   * @response 201 { "status": "success", "data": { "user": { ... } } }
   * @response 409 When firebase_uid or email already exists.
   * @response 422 When request body validation fails.
   */
  public function register(RegisterRequest $request): JsonResponse
  {
    $firebaseUser = $request->attributes->get('firebase_user');

    $user = $this->registerUserService->execute(
      firebaseUser: $firebaseUser,
      data: $request->validated(),
    );

    return response()->json([
      'status' => 'success',
      'data' => [
        'user' => new UserResource($user),
      ],
    ], 201);
  }

  // -------------------------------------------------------------------------
  // POST /api/auth/login
  // -------------------------------------------------------------------------

  /**
   * Authenticate an existing user via Firebase token.
   *
   * Finds the local DB record, updates last_login_at, and returns
   * the user profile with role and zone loaded.
   *
   * @response 200 { "status": "success", "data": { "user": { ... } } }
   * @response 404 When no local account matches the Firebase UID.
   */
  public function login(LoginRequest $request): JsonResponse
  {
    $firebaseUser = $request->attributes->get('firebase_user');

    $user = $this->loginUserService->execute($firebaseUser['uid']);

    return response()->json([
      'status' => 'success',
      'data' => [
        'user' => new UserResource($user),
      ],
    ], 200);
  }

  // -------------------------------------------------------------------------
  // GET /api/auth/me
  // -------------------------------------------------------------------------

  /**
   * Return the currently authenticated user's profile.
   *
   * Used by the React frontend on page load / token refresh to re-hydrate
   * session state and determine which UI to display based on role_id.
   *
   * @response 200 { "status": "success", "data": { "user": { ... } } }
   * @response 404 When no local account matches the Firebase UID.
   */
  public function me(MeRequest $request): JsonResponse
  {
    $firebaseUid = $request->attributes->get('firebase_uid');

    $user = $this->getCurrentUserService->execute($firebaseUid);

    return response()->json([
      'status' => 'success',
      'data' => [
        'user' => new UserResource($user),
      ],
    ], 200);
  }
}
