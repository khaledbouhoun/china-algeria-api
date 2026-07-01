<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Package;

class PackageService
{
    public function list(array $filters = []): mixed
    {
        return Package::query()->get();
    }

    public function find(int $id): ?Package
    {
        return Package::find($id);
    }

    public function create(array $data): Package
    {
        return Package::create($data);
    }

    public function update(int $id, array $data): Package
    {
        $model = Package::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = Package::findOrFail($id);

        return (bool) $model->delete();
    }
}
