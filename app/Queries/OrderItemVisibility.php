<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class OrderItemVisibility
{
  public static function apply(Builder $query, User $user): Builder
  {
    if ($user->hasRole(User::ROLE_ADMIN, User::ROLE_CASHIER, User::ROLE_AGENT_A, User::ROLE_AGENT_C, User::ROLE_RESPONSABLE_A, User::ROLE_RESPONSABLE_C, User::ROLE_DELIVERY, User::ROLE_VERIFIER)) {
      return $query;
    }

    // TODO: Add role-specific visibility rules once the business access matrix is clarified.
    return $query->whereRaw('1 = 0');
  }
}
