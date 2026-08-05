<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PackageStep;
use App\Models\User;

class PackageStepService
{
  public function list(User $user, array $filters = []): mixed
  {
    return PackageStep::query()->get();
  }

  public function find(User $user, int $id): ?PackageStep
  {
    return PackageStep::find($id);
  }

  public function create(User $user, array $data): PackageStep
  {
    return PackageStep::create($data);
  }

  public function update(User $user, int $id, array $data): PackageStep
  {
    $model = PackageStep::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $model = PackageStep::findOrFail($id);

    return (bool) $model->delete();
  }
}
