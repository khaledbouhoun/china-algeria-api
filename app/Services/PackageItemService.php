<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PackageItem;
use App\Models\User;

class PackageItemService
{
  public function list(User $user, array $filters = []): mixed
  {
    return PackageItem::query()->get();
  }

  public function find(User $user, int $id): ?PackageItem
  {
    return PackageItem::find($id);
  }

  public function create(User $user, array $data): PackageItem
  {
    return PackageItem::create($data);
  }

  public function update(User $user, int $id, array $data): PackageItem
  {
    $model = PackageItem::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $model = PackageItem::findOrFail($id);

    return (bool) $model->delete();
  }
}
