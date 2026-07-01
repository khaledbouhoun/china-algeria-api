<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderItemStep;

class OrderItemStepService
{
    public function list(array $filters = []): mixed
    {
        return OrderItemStep::query()->get();
    }

    public function find(int $id): ?OrderItemStep
    {
        return OrderItemStep::find($id);
    }

    public function create(array $data): OrderItemStep
    {
        return OrderItemStep::create($data);
    }

    public function update(int $id, array $data): OrderItemStep
    {
        $model = OrderItemStep::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = OrderItemStep::findOrFail($id);

        return (bool) $model->delete();
    }
}
