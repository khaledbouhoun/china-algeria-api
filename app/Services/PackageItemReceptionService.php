<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PackageItem;
use App\Models\PackageItemReception;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PackageItemReceptionService
{
  public function list(User $user, array $filters = []): mixed
  {
    return PackageItemReception::query()->get();
  }

  public function find(User $user, int $id): ?PackageItemReception
  {
    return PackageItemReception::find($id);
  }

  /**
   * Create a new PackageItemReception (Zone C inspection).
   *
   * Business rules:
   *  - 3-strike escalation: attempts 1–2 require Agent/Verifier,
   *    attempt 3 requires Supervisor (Responsable C), >3 is locked out.
   *  - Bulk items (quantity_allocated IS NULL): ignore received_quantity,
   *    set expected_quantity and received_quantity to null.
   *  - Discrete items: require received_quantity, calculate difference.
   *  - On success: create a PackageItemStep with CL_CONFIRMED status.
   */
  public function create(User $user, array $data): PackageItemReception
  {
    return DB::transaction(function () use ($user, $data): PackageItemReception {

      // ── 1. Lock the PackageItem to prevent concurrent inspections ──
      $packageItem = PackageItem::query()
        ->lockForUpdate()
        ->findOrFail($data['package_item_id']);

      // ── 2. Calculate the current attempt number ────────────────────
      $maxReception = (int) PackageItemReception::query()
        ->where('package_item_id', $packageItem->id)
        ->max('count_reception');

      $currentAttempt = $maxReception + 1;

      // ── 3. Lockout: no more than 3 inspections ────────────────────
      if ($currentAttempt > 3) {
        throw ValidationException::withMessages([
          'package_item_id' => 'This item has already been inspected the maximum of 3 times.',
        ]);
      }

      // ── 4. Enforce the 3-Strike Escalation Role gates ─────────────
      if ($currentAttempt <= 2) {
        // Attempts 1 & 2: standard Agent or Verifier
        if (! $user->hasRole(User::ROLE_AGENT_C, User::ROLE_VERIFIER)) {
          throw new AccessDeniedHttpException(
            'Only an Agent (Zone C) or Verifier can perform inspection attempts 1 and 2.'
          );
        }
      } else {
        // Attempt 3 (final force): Supervisor only
        if (!$user->hasRole(User::ROLE_RESPONSABLE_C)) {
          throw new AccessDeniedHttpException(
            'Only a Supervisor (Responsable C) can perform the 3rd and final inspection attempt.'
          );
        }
      }

      // ── 5. Determine Bulk vs Discrete ─────────────────────────────
      $isBulk = is_null($packageItem->quantity_allocated);

      $expectedWeight = (float) $packageItem->weight_total_allocated;
      $receivedWeight = (float) ($data['received_weight'] ?? 0);

      if ($isBulk) {
        // BULK ITEM: weight-only, no quantity tracking
        $expectedQuantity = null;
        $receivedQuantity = null;
        $differenceQuantity = null;
      } else {
        // DISCRETE ITEM: countable pieces
        $expectedQuantity = (int) $packageItem->quantity_allocated;
        $receivedQuantity = (int) ($data['received_quantity'] ?? 0);
        $differenceQuantity = $receivedQuantity - $expectedQuantity;
      }

      $differenceWeight = $receivedWeight - $expectedWeight;

      // ── 6. Create the PackageItemReception record ─────────────────
      // difference_quantity and difference_weight are GENERATED columns
      // in PostgreSQL, so we exclude them from the INSERT payload.
      $reception = PackageItemReception::create([
        'package_item_id' => $packageItem->id,
        'inspected_by' => $user->id,
        'expected_quantity' => $expectedQuantity,
        'expected_weight' => $expectedWeight,
        'received_quantity' => $receivedQuantity,
        'received_weight' => $receivedWeight,
        'count_reception' => $currentAttempt,
        'comment' => $data['comment'] ?? null,
      ]);

      // ── 7. Advance the PackageItem status to CL_CONFIRMED ────────
      $step = $packageItem->steps()->create([
        'status_id' => Status::PACKAGE_ITEM_CL_CONFIRMED,
        'zone_id' => $user->zone_id,
        'user_id' => $user->id,
      ]);

      $packageItem->update([
        'current_step_id' => $step->id,
      ]);

      // ── 8. Set calculated differences in memory for the response ──
      // These match the DB-generated values but are set here so the
      // returned resource includes them without an extra query.
      $reception->difference_quantity = $differenceQuantity;
      $reception->difference_weight = $differenceWeight;

      return $reception->load(['packageItem.currentStep', 'inspector']);
    });
  }

  /**
   * Batch-create PackageItemReceptions for all items in a package.
   *
   * The entire batch runs inside a single transaction so that either
   * all items are inspected or none are (atomic package opening).
   *
   * @return PackageItemReception[]
   */
  public function createWithItems(User $user, array $data): array
  {
    return DB::transaction(function () use ($user, $data): array {
      $results = [];

      foreach ($data['items'] as $itemData) {
        // ── 1. Lock the PackageItem ─────────────────────────────────
        $packageItem = PackageItem::query()
          ->lockForUpdate()
          ->findOrFail($itemData['package_item_id']);

        // ── 2. Calculate the current attempt number ─────────────────
        $maxReception = (int) PackageItemReception::query()
          ->where('package_item_id', $packageItem->id)
          ->max('count_reception');

        $currentAttempt = $maxReception + 1;

        // ── 3. Lockout: no more than 3 inspections ──────────────────
        if ($currentAttempt > 3) {
          throw ValidationException::withMessages([
            'package_item_id' => "Item #{$packageItem->id} has already been inspected the maximum of 3 times.",
          ]);
        }

        // ── 4. Enforce the 3-Strike Escalation Role gates ───────────
        if ($currentAttempt <= 2) {
          if (! $user->hasRole(User::ROLE_AGENT_C, User::ROLE_VERIFIER)) {
            throw new AccessDeniedHttpException(
              'Only an Agent (Zone C) or Verifier can perform inspection attempts 1 and 2.'
            );
          }
        } else {
          if (! $user->hasRole(User::ROLE_RESPONSABLE_C)) {
            throw new AccessDeniedHttpException(
              'Only a Supervisor (Responsable C) can perform the 3rd and final inspection attempt.'
            );
          }
        }

        // ── 5. Determine Bulk vs Discrete ───────────────────────────
        $isBulk = is_null($packageItem->quantity_allocated);

        $expectedWeight = (float) $packageItem->weight_total_allocated;
        $receivedWeight = (float) ($itemData['received_weight'] ?? 0);

        if ($isBulk) {
          $expectedQuantity  = null;
          $receivedQuantity  = null;
          $differenceQuantity = null;
        } else {
          $expectedQuantity  = (int) $packageItem->quantity_allocated;
          $receivedQuantity  = (int) ($itemData['received_quantity'] ?? 0);
          $differenceQuantity = $receivedQuantity - $expectedQuantity;
        }

        $differenceWeight = $receivedWeight - $expectedWeight;

        // ── 6. Create the PackageItemReception record ───────────────
        $reception = PackageItemReception::create([
          'package_item_id'   => $packageItem->id,
          'inspected_by'      => $user->id,
          'expected_quantity'  => $expectedQuantity,
          'expected_weight'    => $expectedWeight,
          'received_quantity'  => $receivedQuantity,
          'received_weight'    => $receivedWeight,
          'count_reception'    => $currentAttempt,
          'comment'            => $itemData['comment'] ?? null,
        ]);

        // ── 7. Advance the PackageItem status to CL_CONFIRMED ──────
        $step = $packageItem->steps()->create([
          'status_id' => Status::PACKAGE_ITEM_CL_CONFIRMED,
          'zone_id'   => $user->zone_id,
          'user_id'   => $user->id,
        ]);

        $packageItem->update([
          'current_step_id' => $step->id,
        ]);

        // ── 8. Set calculated differences in memory ─────────────────
        $reception->difference_quantity = $differenceQuantity;
        $reception->difference_weight   = $differenceWeight;

        $results[] = $reception->load(['packageItem.currentStep', 'inspector']);
      }

      return $results;
    });
  }

  public function update(User $user, int $id, array $data): PackageItemReception
  {
    $model = PackageItemReception::findOrFail($id);

    $packageItemId = $data['package_item_id'] ?? $model->package_item_id;
    $packageItem = PackageItem::findOrFail($packageItemId);

    $expectedQuantity = $packageItem->quantity_allocated;
    $expectedWeight = (float) $packageItem->weight_total_allocated;

    $receivedWeight = (float) ($data['received_weight'] ?? $model->received_weight);

    // Determine received quantity
    if (array_key_exists('received_quantity', $data)) {
      $receivedQuantity = !is_null($data['received_quantity']) ? (int) $data['received_quantity'] : null;
    } else {
      $receivedQuantity = !is_null($model->received_quantity) ? (int) $model->received_quantity : null;
    }

    $isBulk = is_null($expectedQuantity);

    if ($isBulk) {
      $receivedQuantity = null;
      $differenceQuantity = null;
      $differenceWeight = $receivedWeight - $expectedWeight;
    } else {
      if (is_null($receivedQuantity)) {
        $receivedQuantity = 0;
      }
      $differenceQuantity = $receivedQuantity - (int) $expectedQuantity;
      $differenceWeight = $receivedWeight - $expectedWeight;
    }

    $payload = array_merge($data, [
      'expected_quantity' => $expectedQuantity,
      'expected_weight' => $expectedWeight,
      'received_quantity' => $receivedQuantity,
      'received_weight' => $receivedWeight,
    ]);

    $dbPayload = array_diff_key($payload, array_flip(['difference_quantity', 'difference_weight']));
    $model->fill($dbPayload);
    $model->save();

    $reception = $model->fresh();

    $reception->difference_quantity = $differenceQuantity;
    $reception->difference_weight = $differenceWeight;

    return $reception;
  }

  public function delete(User $user, int $id): bool
  {
    $model = PackageItemReception::findOrFail($id);

    return (bool) $model->delete();
  }
}
