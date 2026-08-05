<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Visa;
use App\Queries\VisaVisibility;

class VisaService
{
  public function list(User $user, array $filters = []): mixed
  {
    $query = Visa::query();
    VisaVisibility::apply($query, $user);

    if (!empty($filters['user_id'])) {
      $query->where('user_id', $filters['user_id']);
    }

    return $query->with(['user', 'creator'])->get();
  }

  public function find(User $user, int $id): ?Visa
  {
    $query = Visa::query()->whereKey($id);
    VisaVisibility::apply($query, $user);

    return $query->with(['user', 'creator'])->first();
  }

  public function create(User $user, array $data): Visa
  {
    $model = Visa::create($data);
    $model->load(['user', 'creator']);

    return $model;
  }

  public function update(User $user, int $id, array $data): Visa
  {
    $model = Visa::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh()->load(['user', 'creator']);
  }

  public function delete(User $user, int $id): bool
  {
    $model = Visa::findOrFail($id);

    return (bool) $model->delete();
  }
}
