<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Package;
use App\Models\Status;
use App\Models\User;
use App\Queries\PackageVisibility;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PackageService
{
  public function __construct(
    private readonly PackageItemService $packageItemService,
  ) {
  }

  public function list(User $user, array $filters = []): mixed
  {
    $query = Package::query();
    PackageVisibility::apply($query, $user);

    if (!empty($filters['gladiator_id'])) {
      $query->where('gladiator_id', $filters['gladiator_id']);
    }

    return $query->with(['currentStep', 'steps', 'items'])->get();
  }

  public function find(User $user, int $id): Package
  {
    $query = Package::query()->whereKey($id);
    PackageVisibility::apply($query, $user);

    return $query->with(['currentStep', 'steps', 'items'])->firstOrFail();
  }

  public function create(User $user, array $data): Package
  {
    return DB::transaction(function () use ($user, $data): Package {

      $package = Package::create([
        ...$data,
        'created_by' => $user->id,
        'updated_by' => $user->id,
      ]);

      $step = $package->steps()->create([
        'status_id' => Status::PACKAGE_A_CREATED,
        'zone_id' => $user->zone_id,
        'user_id' => $user->id,
      ]);

      $package->update([
        'current_step_id' => $step->id,
      ]);

      return $package->fresh()->load([
        'currentStep',
      ]);
    });
  }

  public function update(User $user, int $id, array $data): Package
  {
    $query = Package::query()->whereKey($id);
    PackageVisibility::apply($query, $user);

    $model = $query->firstOrFail();
    $model->fill($data);
    $model->save();

    return $model->fresh(['currentStep', 'steps', 'items']);
  }

  public function delete(User $user, int $id): bool
  {
    $query = Package::query()->whereKey($id);
    PackageVisibility::apply($query, $user);

    $model = $query->firstOrFail();

    return (bool) $model->delete();
  }

  // ==========================================
  // Actions Functions
  // ==========================================

  public function createWithItems(User $user, array $data): Package
  {
    return DB::transaction(function () use ($user, $data) {
      $items = $data['items'] ?? [];
      unset($data['items']);
  
      $package = $this->create($user, $data);

      foreach ($items as $item) {
        $item['package_id'] = $package->id;
        $this->packageItemService->create($user, $item);
      }

      return $package->fresh(['currentStep', 'steps', 'items']);
    });
  }

  public function receive(User $user, array $packageIds): Collection
  {
    return DB::transaction(function () use ($user, $packageIds) {

      $query = Package::query()->whereIn('id', $packageIds);
      PackageVisibility::apply($query, $user);
      $packages = $query->get();

      foreach ($packages as $package) {
        if ($package->currentStep?->status_id === Status::PACKAGE_B_RECEIVED) {
          continue;
        }

        $step = $package->steps()->create([
          'status_id' => Status::PACKAGE_B_RECEIVED,
          'zone_id' => $user->zone_id,
          'user_id' => $user->id,
        ]);

        $package->updateQuietly([
          'current_step_id' => $step->id,
        ]);
      }

      return $packages->load(['currentStep', 'steps', 'items']);
    });
  }
}
