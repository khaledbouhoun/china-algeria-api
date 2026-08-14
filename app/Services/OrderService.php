<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Queries\OrderVisibility;
use Illuminate\Support\Facades\DB;

class OrderService
{
  public function __construct(
    private readonly OrderItemService $orderItemService,
  ) {
  }

  public function list(User $user, array $filters = []): mixed
  {
    $query = Order::query()->withCount('items');
    OrderVisibility::apply($query, $user);

    return $query->get();
  }

  public function find(User $user, int $id): Order
  {
    $query = Order::query()->whereKey($id);
    OrderVisibility::apply($query, $user);

    return $query->firstOrFail();
  }

  public function create(User $user, array $data): Order
  {
    return Order::create($data);
  }

  public function update(User $user, int $id, array $data): Order
  {
    $query = Order::query()->whereKey($id);
    OrderVisibility::apply($query, $user);

    $model = $query->firstOrFail();
    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $query = Order::query()->whereKey($id);
    OrderVisibility::apply($query, $user);

    $model = $query->firstOrFail();

    return (bool) $model->delete();
  }

  // ==========================================
  // Actions Functions
  // ==========================================

  public function createWithItems(User $user, array $data): Order
  {
    return DB::transaction(function () use ($user, $data) {
      $items = $data['items'] ?? [];
      unset($data['items']);

      $order = $this->create($user, $data);

      foreach ($items as $item) {
        $item['order_id'] = $order->id;
        $this->orderItemService->create($user, $item);
      }

      return $order->fresh('items');
    });
  }
}
