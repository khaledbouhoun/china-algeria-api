<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;

class UserSessionService
{
  public function list(User $user, array $filters = []): mixed
  {
    return UserSession::query()->get();
  }

  public function find(User $user, int $id): ?UserSession
  {
    return UserSession::find($id);
  }

  public function create(User $user, array $data): UserSession
  {
    return UserSession::create($data);
  }

  public function update(User $user, int $id, array $data): UserSession
  {
    $model = UserSession::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $model = UserSession::findOrFail($id);

    return (bool) $model->delete();
  }
}
