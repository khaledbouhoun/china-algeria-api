<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;

class RoleService
{
    public function list(array $filters = []): mixed
    {
        return Role::query()->get();
    }

    public function find(int $id): ?Role
    {
        return Role::find($id);
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(int $id, array $data): Role
    {
        $model = Role::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = Role::findOrFail($id);

        return (bool) $model->delete();
    }
}
