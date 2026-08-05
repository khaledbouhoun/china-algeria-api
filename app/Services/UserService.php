<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Queries\UserVisibility;

class UserService
{
  public function list(User $user, array $filters = []): mixed
  {
    $query = User::query();
    UserVisibility::apply($query, $user);
    return $query->get();
  }

  public function find(User $user, int $id): ?User
  {
    return User::find($id);
  }

  public function create(User $user, array $data): User
  {
    return User::create($data);
  }

  public function update(User $user, int $id, array $data): User
  {
    $model = User::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $model = User::findOrFail($id);

    return (bool) $model->delete();
  }
}
