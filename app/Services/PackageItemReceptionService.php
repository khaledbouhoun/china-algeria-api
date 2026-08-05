<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PackageItemReception;
use App\Models\User;

class PackageItemReceptionService
{
  public function list(User $user, array $filters = []): mixed
  {
    return PackageItemReception::query()->get();
  }

  public function find(User $user, int $id): ?PackageItemReception
  {
    return PackageItemReception::find($id);
  }

  public function create(User $user, array $data): PackageItemReception
  {
    $payload = array_diff_key($data, array_flip(['difference_quantity', 'difference_weight']));

    return PackageItemReception::create($payload);
  }

  public function update(User $user, int $id, array $data): PackageItemReception
  {
    $model = PackageItemReception::findOrFail($id);
    $payload = array_diff_key($data, array_flip(['difference_quantity', 'difference_weight']));
    $model->fill($payload);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $model = PackageItemReception::findOrFail($id);

    return (bool) $model->delete();
  }
}
