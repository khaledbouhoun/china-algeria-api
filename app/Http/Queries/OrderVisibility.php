<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class OrderVisibility
{
  public static function apply(Builder $query, User $user): Builder
  {
    if ($user->hasRole(User::ROLE_CLIENT)) {
      return $query->where('client_id', $user->id)->where('deleted_at', null);
    }
    return $query->whereRaw('1 = 0');
  }
}
