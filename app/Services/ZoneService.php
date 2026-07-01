<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Zone;

class ZoneService
{
    public function list(array $filters = []): mixed
    {
        return Zone::query()->get();
    }

    public function find(int $id): ?Zone
    {
        return Zone::find($id);
    }

    public function create(array $data): Zone
    {
        return Zone::create($data);
    }

    public function update(int $id, array $data): Zone
    {
        $model = Zone::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = Zone::findOrFail($id);

        return (bool) $model->delete();
    }
}
