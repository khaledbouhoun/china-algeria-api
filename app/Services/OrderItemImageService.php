<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderItemImage;

class OrderItemImageService
{
    public function list(array $filters = []): mixed
    {
        return OrderItemImage::query()->get();
    }

    public function find(int $id): ?OrderItemImage
    {
        return OrderItemImage::find($id);
    }

    public function create(array $data): OrderItemImage
    {
        return OrderItemImage::create($data);
    }

    public function update(int $id, array $data): OrderItemImage
    {
        $model = OrderItemImage::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = OrderItemImage::findOrFail($id);

        return (bool) $model->delete();
    }
}
