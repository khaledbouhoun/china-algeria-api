<?php

namespace App\Services\Auth;

use App\Models\User;

use Illuminate\Support\Facades\DB;

class LoginUserService
{
  public function execute(User $user): User
  {
    return DB::transaction(function () use ($user) {
      /** @var User|null $user */
      $user->load(['role', 'zone']);

      // Persist the login timestamp.
      $user->last_login_at = now();
      $user->save();

      // Create a new user session.
      $user->userSessions()->create();

      return $user;
    });
  }
}
