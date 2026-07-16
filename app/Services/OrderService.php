<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Queries\OrderVisibility;

class OrderService
{
  public function list(User $user, array $filters = []): mixed
  {
    $query = Order::query();
    OrderVisibility::apply($query, $user);

    if (isset($filters['client_id'])) {
      $query->where('client_id', $filters['client_id']);
    }

    return $query->with(['client', 'items'])->get();
  }

  public function find(User $user, int $id): ?Order
  {
    $query = Order::query()->whereKey($id);
    OrderVisibility::apply($query, $user);

    return $query->with(['client', 'items'])->first();
  }

  public function create(User $user, array $data): Order
  {
    $model = Order::create($data);
    $model->load(['client', 'items']);

    return $model;
  }

  public function update(User $user, int $id, array $data): Order
  {
    $model = Order::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh()->load(['client', 'items']);
  }

  public function delete(User $user, int $id): bool
  {
    $model = Order::findOrFail($id);

    return (bool) $model->delete();
  }
}
