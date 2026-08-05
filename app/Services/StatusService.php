<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Status;
use App\Models\User;

class StatusService
{
  public function list(User $user, array $filters = []): mixed
  {
    return Status::query()->get();
  }

  public function find(User $user, int $id): ?Status
  {
    return Status::find($id);
  }

  public function create(User $user, array $data): Status
  {
    return Status::create($data);
  }

  public function update(User $user, int $id, array $data): Status
  {
    $model = Status::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $model = Status::findOrFail($id);

    return (bool) $model->delete();
  }
}
