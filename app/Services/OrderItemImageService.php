<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderItemImage;
use App\Models\User;

class OrderItemImageService
{
  public function list(User $user, array $filters = []): mixed
  {
    return OrderItemImage::query()->get();
  }

  public function find(User $user, int $id): ?OrderItemImage
  {
    return OrderItemImage::find($id);
  }

  public function create(User $user, array $data): OrderItemImage
  {
    return OrderItemImage::create($data);
  }

  public function update(User $user, int $id, array $data): OrderItemImage
  {
    $model = OrderItemImage::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $model = OrderItemImage::findOrFail($id);

    return (bool) $model->delete();
  }
}
