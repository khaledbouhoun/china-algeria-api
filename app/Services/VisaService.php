<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Visa;

class VisaService
{
    public function list(array $filters = []): mixed
    {
        return Visa::query()->get();
    }

    public function find(int $id): ?Visa
    {
        return Visa::find($id);
    }

    public function create(array $data): Visa
    {
        return Visa::create($data);
    }

    public function update(int $id, array $data): Visa
    {
        $model = Visa::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = Visa::findOrFail($id);

        return (bool) $model->delete();
    }
}
