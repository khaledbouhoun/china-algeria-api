<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PackageItem;

class PackageItemService
{
    public function list(array $filters = []): mixed
    {
        return PackageItem::query()->get();
    }

    public function find(int $id): ?PackageItem
    {
        return PackageItem::find($id);
    }

    public function create(array $data): PackageItem
    {
        return PackageItem::create($data);
    }

    public function update(int $id, array $data): PackageItem
    {
        $model = PackageItem::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = PackageItem::findOrFail($id);

        return (bool) $model->delete();
    }
}
