<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PackageItemStep;

class PackageItemStepService
{
    public function list(array $filters = []): mixed
    {
        return PackageItemStep::query()->get();
    }

    public function find(int $id): ?PackageItemStep
    {
        return PackageItemStep::find($id);
    }

    public function create(array $data): PackageItemStep
    {
        return PackageItemStep::create($data);
    }

    public function update(int $id, array $data): PackageItemStep
    {
        $model = PackageItemStep::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = PackageItemStep::findOrFail($id);

        return (bool) $model->delete();
    }
}
