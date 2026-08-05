<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Package;
use App\Models\User;
use App\Queries\PackageVisibility;

class PackageService
{
  public function list(User $user, array $filters = []): mixed
  {
    $query = Package::query();
    PackageVisibility::apply($query, $user);

    if (!empty($filters['gladiator_id'])) {
      $query->where('gladiator_id', $filters['gladiator_id']);
    }

    return $query->with(['currentStep', 'steps', 'items'])->get();
  }

  public function find(User $user, int $id): ?Package
  {
    $query = Package::query()->whereKey($id);
    PackageVisibility::apply($query, $user);

    return $query->with(['currentStep', 'steps', 'items'])->first();
  }

  public function create(User $user, array $data): Package
  {
    $model = Package::create($data);
    $model->load(['currentStep', 'steps', 'items']);

    return $model;
  }

  public function update(User $user, int $id, array $data): Package
  {
    $model = Package::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh()->load(['currentStep', 'steps', 'items']);
  }

  public function delete(User $user, int $id): bool
  {
    $model = Package::findOrFail($id);

    return (bool) $model->delete();
  }
}
