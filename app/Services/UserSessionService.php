<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserSession;

class UserSessionService
{
    public function list(array $filters = []): mixed
    {
        return UserSession::query()->get();
    }

    public function find(int $id): ?UserSession
    {
        return UserSession::find($id);
    }

    public function create(array $data): UserSession
    {
        return UserSession::create($data);
    }

    public function update(int $id, array $data): UserSession
    {
        $model = UserSession::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = UserSession::findOrFail($id);

        return (bool) $model->delete();
    }
}
