<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function list(array $filters = []): mixed
    {
        return Order::query()->get();
    }

    public function find(int $id): ?Order
    {
        return Order::find($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(int $id, array $data): Order
    {
        $model = Order::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = Order::findOrFail($id);

        return (bool) $model->delete();
    }
}
