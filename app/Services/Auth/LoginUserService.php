<?php

namespace App\Services\Auth;

use App\Models\User;

class LoginUserService
{
  public function execute(User $user): User
  {
    /** @var User|null $user */
    $user->load(['role', 'zone']);

    // Persist the login timestamp.
    $user->last_login_at = now();
    $user->save();

    // Create a new user session.
    $user->userSessions()->create();

    return $user;
  }
}
