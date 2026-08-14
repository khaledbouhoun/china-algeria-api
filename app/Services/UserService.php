<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Queries\UserVisibility;

class UserService
{
  /**
   * Get users visible to the authenticated user.
   */
  public function list(User $user, array $filters = []): mixed
  {
    $query = User::query();

    UserVisibility::apply($query, $user);

    if (!empty($filters['role'])) {
      $query->where('role_id', $filters['role']);
    }

    return $query->get();
  }

  /**
   * Find a user visible to the authenticated user.
   */
  public function find(User $user, int $id): ?User
  {
    $query = User::query();

    UserVisibility::apply($query, $user);

    return $query->find($id);
  }

  /**
   * Create a user.
   */
  public function create(User $user, array $data): User
  {
    return User::create($data);
  }

  /**
   * Update a user visible to the authenticated user.
   */
  public function update(User $user, int $id, array $data): User
  {
    $query = User::query();

    UserVisibility::apply($query, $user);

    $model = $query->findOrFail($id);

    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  /**
   * Delete a user visible to the authenticated user.
   */
  public function delete(User $user, int $id): bool
  {
    $query = User::query();

    UserVisibility::apply($query, $user);

    $model = $query->findOrFail($id);

    return (bool) $model->delete();
  }

}
