<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderItem;
use App\Models\Status;
use App\Models\User;
use App\Queries\OrderItemVisibility;
use Illuminate\Support\Facades\DB;

class OrderItemService
{
  public function list(User $user, array $filters = []): mixed
  {
    $query = OrderItem::query();
    OrderItemVisibility::apply($query, $user);

    if (!empty($filters['order'])) {
      $query->where('order_id', $filters['order']);
    }
    if (!empty($filters['designation'])) {
      $query->where('designation', 'like', "%{$filters['designation']}%");
    }

    return $query->with(['currentStep'])->get();
  }

  public function find(User $user, int $id): ?OrderItem
  {
    $query = OrderItem::query()->whereKey($id);
    OrderItemVisibility::apply($query, $user);

    return $query->with(['currentStep'])->firstOrFail();
  }



  public function create(User $user, array $data): OrderItem
  {
    return DB::transaction(function () use ($user, $data): OrderItem {
      $item = OrderItem::create($data);

      $step = $item->steps()->create([
        'status_id' => Status::ITEM_CL_CREATED,
        'zone_id' => $user->zone_id,
        'user_id' => $user->id,
        'created_by' => $user->id,
      ]);

      $item->update([
        'current_step_id' => $step->id,
      ]);

      return $item->fresh()->load([
        'currentStep',
      ]);
    });
  }

  public function update(User $user, int $id, array $data): OrderItem
  {
    $query = OrderItem::query()->whereKey($id);
    OrderItemVisibility::apply($query, $user);

    $model = $query->firstOrFail();
    $model->fill($data);
    $model->save();

    return $model->fresh()->load(['currentStep']);
  }

  public function delete(User $user, int $id): bool
  {
    $query = OrderItem::query()->whereKey($id);
    OrderItemVisibility::apply($query, $user);

    $model = $query->firstOrFail();

    return (bool) $model->delete();
  }
}
