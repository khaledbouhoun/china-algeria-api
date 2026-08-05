<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;
use App\Models\User;

class RoleService
{
  public function list(User $user, array $filters = []): mixed
  {
    return Role::query()->get();
  }

  public function find(User $user, int $id): ?Role
  {
    return Role::find($id);
  }

  public function create(User $user, array $data): Role
  {
    return Role::create($data);
  }

  public function update(User $user, int $id, array $data): Role
  {
    $model = Role::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $model = Role::findOrFail($id);

    return (bool) $model->delete();
  }
}
