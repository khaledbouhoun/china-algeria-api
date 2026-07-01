<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Status;

class StatusService
{
    public function list(array $filters = []): mixed
    {
        return Status::query()->get();
    }

    public function find(int $id): ?Status
    {
        return Status::find($id);
    }

    public function create(array $data): Status
    {
        return Status::create($data);
    }

    public function update(int $id, array $data): Status
    {
        $model = Status::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = Status::findOrFail($id);

        return (bool) $model->delete();
    }
}
