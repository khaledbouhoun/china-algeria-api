<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PackageStep;

class PackageStepService
{
    public function list(array $filters = []): mixed
    {
        return PackageStep::query()->get();
    }

    public function find(int $id): ?PackageStep
    {
        return PackageStep::find($id);
    }

    public function create(array $data): PackageStep
    {
        return PackageStep::create($data);
    }

    public function update(int $id, array $data): PackageStep
    {
        $model = PackageStep::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = PackageStep::findOrFail($id);

        return (bool) $model->delete();
    }
}
