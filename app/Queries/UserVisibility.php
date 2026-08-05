<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserVisibility
{
  public static function apply(Builder $query, User $user): Builder
  {
    if ($user->hasRole(User::ROLE_ADMIN)) {
      return $query;
    }


    // TODO: Add role-specific visibility rules once the business access matrix is clarified.
    return $query->whereRaw('1 = 0');

  }
}
