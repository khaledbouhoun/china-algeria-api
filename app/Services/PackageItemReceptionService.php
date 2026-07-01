<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PackageItemReception;

class PackageItemReceptionService
{
    public function list(array $filters = []): mixed
    {
        return PackageItemReception::query()->get();
    }

    public function find(int $id): ?PackageItemReception
    {
        return PackageItemReception::find($id);
    }

    public function create(array $data): PackageItemReception
    {
        $payload = array_diff_key($data, array_flip(['difference_quantity', 'difference_weight']));

        return PackageItemReception::create($payload);
    }

    public function update(int $id, array $data): PackageItemReception
    {
        $model = PackageItemReception::findOrFail($id);
        $payload = array_diff_key($data, array_flip(['difference_quantity', 'difference_weight']));
        $model->fill($payload);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = PackageItemReception::findOrFail($id);

        return (bool) $model->delete();
    }
}
