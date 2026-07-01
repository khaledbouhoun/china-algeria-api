<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderItem;

class OrderItemService
{
    public function list(array $filters = []): mixed
    {
        return OrderItem::query()->get();
    }

    public function find(int $id): ?OrderItem
    {
        return OrderItem::find($id);
    }

    public function create(array $data): OrderItem
    {
        return OrderItem::create($data);
    }

    public function update(int $id, array $data): OrderItem
    {
        $model = OrderItem::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = OrderItem::findOrFail($id);

        return (bool) $model->delete();
    }
}
