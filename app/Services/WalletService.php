<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Wallet;

class WalletService
{
    public function list(array $filters = []): mixed
    {
        return Wallet::query()->get();
    }

    public function find(int $id): ?Wallet
    {
        return Wallet::find($id);
    }

    public function create(array $data): Wallet
    {
        return Wallet::create($data);
    }

    public function update(int $id, array $data): Wallet
    {
        $model = Wallet::findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        $model = Wallet::findOrFail($id);

        return (bool) $model->delete();
    }
}
