<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderItem;
use App\Models\User;
use App\Queries\OrderItemVisibility;

class OrderItemService
{
  public function list(User $user, array $filters = []): mixed
  {
    $query = OrderItem::query();
    OrderItemVisibility::apply($query, $user);

    if (isset($filters['order_id'])) {
      $query->where('order_id', $filters['order_id']);
    }

    return $query->with(['order', 'currentStep', 'steps', 'images'])->get();
  }

  public function find(User $user, int $id): ?OrderItem
  {
    $query = OrderItem::query()->whereKey($id);
    OrderItemVisibility::apply($query, $user);

    return $query->with(['order', 'currentStep', 'steps', 'images'])->first();
  }

  public function create(User $user, array $data): OrderItem
  {
    $model = OrderItem::create($data);
    $model->load(['order', 'currentStep', 'steps', 'images']);

    return $model;
  }

  public function update(User $user, int $id, array $data): OrderItem
  {
    $model = OrderItem::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh()->load(['order', 'currentStep', 'steps', 'images']);
  }

  public function delete(User $user, int $id): bool
  {
    $model = OrderItem::findOrFail($id);

    return (bool) $model->delete();
  }
}
