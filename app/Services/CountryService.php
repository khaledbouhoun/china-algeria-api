<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Country;

class CountryService
{
    public function list(array $filters = []): mixed
    {
        return Country::query()->get();
    }

    public function find(int $id): ?Country
    {
        return Country::find($id);
    }

    public function create(array $data): Country
    {
        return Country::create($data);
    }

    public function update(int $id, array $data): Country
    {
        $model = Country::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = Country::findOrFail($id);

        return (bool) $model->delete();
    }
}
