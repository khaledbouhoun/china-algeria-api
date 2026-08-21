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
      $orderItem = OrderItem::query()
        ->lockForUpdate()
        ->findOrFail($data['order_item_id']);

      $requestedWeight = (float) ($data['weight_total_allocated'] ?? 0);
      $existingAllocatedWeight = (float) PackageItem::query()
        ->where('order_item_id', $orderItem->id)
        ->sum('weight_total_allocated');

      $remainingWeight = (float) $orderItem->weight_total - $existingAllocatedWeight;

      if ($requestedWeight > $remainingWeight) {
        throw ValidationException::withMessages([
          'weight_total_allocated' => 
            "Only {$remainingWeight} g weight is remaining for this order item.",
        ]);
      }

      // --- NEW LOGIC: Branch based on item type (Bulk vs Discrete) ---
      $isBulkItem = is_null($orderItem->quantity_declared);
      
      $quantityAllocated = null;
      $weightTotal = $requestedWeight; 
      $amountTotal = 0.0;
      $unitPrice = (float) ($orderItem->price_unit_declared ?? 0);

      if ($isBulkItem) {
        // ---------------------------------------------------------
        // MODE A: BULK ITEM (Weight only, no quantity)
        // ---------------------------------------------------------
        $quantityAllocated = null; // Explicitly null for uncounted items
        
        // If pricing is based on weight for bulk items (e.g., $5 per kg)
        if ($unitPrice > 0 && $requestedWeight > 0) {
            $amountTotal = $unitPrice * $requestedWeight; 
        }

      } else {
        // ---------------------------------------------------------
        // MODE B: DISCRETE ITEM (Countable books, phones, etc.)
        // ---------------------------------------------------------
        $requestedQuantity = (int) ($data['quantity_allocated'] ?? 0);
        $unitWeight = (float) ($orderItem->weight_unit_declared ?? 0);
        
        $quantityAllocated = $requestedQuantity;

        // Auto-calculate quantity if missing but unit weight exists
        if ($quantityAllocated <= 0 && $unitWeight > 0) {
          $quantityAllocated = (int) round($requestedWeight / $unitWeight);
        }

        // Calculate missing total weight based on pieces if weight wasn't sent
        if ($weightTotal <= 0 && $unitWeight > 0) {
          $weightTotal = $quantityAllocated * $unitWeight;
        }

        // Calculate amount based on discrete pieces
        if ($unitPrice > 0 && $quantityAllocated > 0) {
          $amountTotal = $unitPrice * $quantityAllocated;
        }
      }

      // Create the PackageItem
      $item = PackageItem::create([
        'package_id' => $data['package_id'],
        'order_item_id' => $orderItem->id,
        'quantity_allocated' => $quantityAllocated, 
        'weight_total_allocated' => $weightTotal,
        'amount_total_allocated' => $amountTotal,
        'created_by' => $user->id,
        'updated_by' => $user->id,
      ]);

      $step = $item->steps()->create([
        'status_id' => Status::PACKAGE_ITEM_PACKAGED,
        'zone_id' => $user->zone_id,
        'user_id' => $user->id,
      ]);

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
