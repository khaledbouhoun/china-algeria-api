<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Zone;

class ZoneService
{
  public function list(User $user, array $filters = []): mixed
  {
    return Zone::query()->get();
  }

  public function find(User $user, int $id): ?Zone
  {
    return Zone::find($id);
  }

  public function create(User $user, array $data): Zone
  {
    return Zone::create($data);
  }

  public function update(User $user, int $id, array $data): Zone
  {
    $model = Zone::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $model = Zone::findOrFail($id);

    return (bool) $model->delete();
  }
}
