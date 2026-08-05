<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderItemStep;
use App\Models\User;

class OrderItemStepService
{
  public function list(User $user, array $filters = []): mixed
  {
    return OrderItemStep::query()->get();
  }

  public function find(User $user, int $id): ?OrderItemStep
  {
    return OrderItemStep::find($id);
  }

  public function create(User $user, array $data): OrderItemStep
  {
    return OrderItemStep::create($data);
  }

  public function update(User $user, int $id, array $data): OrderItemStep
  {
    $model = OrderItemStep::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $model = OrderItemStep::findOrFail($id);

    return (bool) $model->delete();
  }
}
