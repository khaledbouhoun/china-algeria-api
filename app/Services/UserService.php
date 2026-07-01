<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

class UserService
{
    public function list(array $filters = []): mixed
    {
        return User::query()->get();
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $model = User::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = User::findOrFail($id);

        return (bool) $model->delete();
    }
}
