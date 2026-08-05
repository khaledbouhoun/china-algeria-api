<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PackageItemStep;
use App\Models\User;

class PackageItemStepService
{
  public function list(User $user, array $filters = []): mixed
  {
    return PackageItemStep::query()->get();
  }

  public function find(User $user, int $id): ?PackageItemStep
  {
    return PackageItemStep::find($id);
  }

  public function create(User $user, array $data): PackageItemStep
  {
    return PackageItemStep::create($data);
  }

  public function update(User $user, int $id, array $data): PackageItemStep
  {
    $model = PackageItemStep::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $model = PackageItemStep::findOrFail($id);

    return (bool) $model->delete();
  }
}
