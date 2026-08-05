<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Country;
use App\Models\User;

class CountryService
{
  public function list(User $user, array $filters = []): mixed
  {
    return Country::query()->get();
  }

  public function find(User $user, int $id): ?Country
  {
    return Country::find($id);
  }

  public function create(User $user, array $data): Country
  {
    return Country::create($data);
  }

  public function update(User $user, int $id, array $data): Country
  {
    $model = Country::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $model = Country::findOrFail($id);

    return (bool) $model->delete();
  }
}
