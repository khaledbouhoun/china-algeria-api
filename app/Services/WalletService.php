<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Queries\WalletVisibility;

class WalletService
{
  public function list(User $user, array $filters = []): mixed
  {
    $query = Wallet::query();
    WalletVisibility::apply($query, $user);

    return $query->with(['user', 'role', 'transactions'])->get();
  }

  public function find(User $user, int $id): ?Wallet
  {
    $query = Wallet::query()->whereKey($id);
    WalletVisibility::apply($query, $user);

    return $query->with(['user', 'role', 'transactions'])->first();
  }

  public function create(User $user, array $data): Wallet
  {
    $model = Wallet::create($data);
    $model->load(['user', 'role', 'transactions']);

    return $model;
  }

  public function update(User $user, int $id, array $data): Wallet
  {
    $model = Wallet::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh()->load(['user', 'role', 'transactions']);
  }

  public function delete(User $user, int $id): bool
  {
    $model = Wallet::findOrFail($id);

    return (bool) $model->delete();
  }
}
