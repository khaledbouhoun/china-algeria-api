<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use App\Models\Status;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Builder;

class PackageVisibility
{
  public static function apply(Builder $query, User $user): Builder
  {
    // Admins have full access to all packages
    if ($user->hasRole(User::ROLE_ADMIN)) {
      return $query;
    }

    // Responsable A sees packages created within their specific zone
    if ($user->hasRole(User::ROLE_RESPONSABLE_A)) {
      return $query->whereHas('currentStep', function ($q) use ($user) {
        $q->where('status_id', Status::PACKAGE_A_CREATED)
          ->where('zone_id', $user->zone_id);
      });
    }

    // Agent A visibility depends on their assigned zone
    if ($user->hasRole(User::ROLE_AGENT_A)) {
      if ($user->zone_id == Zone::ZONE_A) {
        return $query->whereHas('currentStep', function ($q) use ($user) {
          $q->where('status_id', Status::PACKAGE_A_CREATED)
            ->where('user_id', $user->id)
            ->where('zone_id', $user->zone_id);
        });
      }

      if ($user->zone_id == Zone::ZONE_B) {
        return $query->whereHas('currentStep', function ($q) use ($user) {
          $q->where('status_id', Status::PACKAGE_B_RECEIVED)
            ->where('user_id', $user->id)
            ->where('zone_id', $user->zone_id);
        });
      }
    }

    // Gladiators only see packages assigned to their ID
    if ($user->hasRole(User::ROLE_GLADIATOR)) {
      return $query->where('gladiator_id', $user->id);
    }

    // Delivery sees packages they have personally received
    if ($user->hasRole(User::ROLE_DELIVERY)) {
      return $query->whereHas('currentStep', function ($q) use ($user) {
        $q->where('status_id', Status::PACKAGE_D_RECEIVED)
          ->where('user_id', $user->id);
      });
    }

    // Responsable C sees all received packages within their zone
    if ($user->hasRole(User::ROLE_RESPONSABLE_C)) {
      return $query->whereHas('currentStep', function ($q) use ($user) {
        $q->where('status_id', Status::PACKAGE_C_RECEIVED)
          ->where('zone_id', $user->zone_id);
      });
    }

    // Agent C sees only packages they have personally received
    if ($user->hasRole(User::ROLE_AGENT_C)) {
      return $query->whereHas('currentStep', function ($q) use ($user) {
        $q->where('status_id', Status::PACKAGE_C_RECEIVED)
          ->where('user_id', $user->id);
      });
    }

    // Fallback: deny access if no role matches
    return $query->whereRaw('1 = 0');
  }
}
