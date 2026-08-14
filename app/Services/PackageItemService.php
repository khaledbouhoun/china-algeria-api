<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderItem;
use App\Models\PackageItem;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PackageItemService
{
  public function list(User $user, array $filters = []): mixed
  {
    return PackageItem::query()->get();
  }

  public function find(User $user, int $id): ?PackageItem
  {
    return PackageItem::findOrFail($id);
  }

  // public function create(User $user, array $data): PackageItem
  // {
  //   return DB::transaction(function () use ($user, $data): PackageItem {
  //     $item = PackageItem::create($data);

  //     $step = $item->steps()->create([
  //       'status_id' => Status::PACKAGE_ITEM_PACKAGED,
  //       'zone_id' => $user->zone_id,
  //       'user_id' => $user->id,
  //     ]);

  //     $item->update([
  //       'current_step_id' => $step->id,
  //     ]);

  //     return $item->fresh()->load([
  //       'currentStep',
  //     ]);
  //   });
  // }

  public function create(User $user, array $data): PackageItem
{
    return DB::transaction(function () use ($user, $data): PackageItem {

        // Lock the order item to prevent concurrent over-allocation.
        $orderItem = OrderItem::query()
            ->lockForUpdate()
            ->findOrFail($data['order_item_id']);



        // Calculate how much of the order item has already been allocated.
        $totalSumItemsQuantity = PackageItem::query()
            ->where('order_item_id', $orderItem->id)
            ->sum('quantity_allocated');

        $requestedQuantity = (int) $data['quantity_allocated'];

        $remainingQuantity =
            $orderItem->quantity_declared - $totalSumItemsQuantity;


        if ($requestedQuantity > $remainingQuantity) {
            throw ValidationException::withMessages([
                'quantity_allocated' =>
                    "Only {$remainingQuantity} quantity is remaining for this order item.",
            ]);
        }

        // Calculate derived values on the server.
        $weightTotal = $requestedQuantity
            * $orderItem->weight_unit_declared;

        $amountTotal = $requestedQuantity
            * $orderItem->price_unit_declared;

        // Create the package item.
        $item = PackageItem::create([
            'package_id' => $data['package_id'],
            'order_item_id' => $orderItem->id,
            'quantity_allocated' => $requestedQuantity,
            'weight_total_allocated' => $weightTotal,
            'amount_total_allocated' => $amountTotal,
        ]);

        // Create the initial package-item step.
        $step = $item->steps()->create([
            'status_id' => Status::PACKAGE_ITEM_PACKAGED,
            'zone_id' => $user->zone_id,
            'user_id' => $user->id,
        ]);

        // Set the current step.
        $item->update([
            'current_step_id' => $step->id,
        ]);

        return $item->fresh()->load([
            'currentStep',
        ]);
    });
}
  public function update(User $user, int $id, array $data): PackageItem
  {
    $model = PackageItem::findOrFail($id);
    $model->fill($data);
    $model->save();

    return $model->fresh();
  }

  public function delete(User $user, int $id): bool
  {
    $model = PackageItem::findOrFail($id);

    return (bool) $model->delete();
  }
}
