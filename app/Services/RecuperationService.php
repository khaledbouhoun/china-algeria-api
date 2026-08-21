<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderItem;
use App\Models\PackageItem;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RecuperationService
{
    /**
     * Process client recuperation for one or multiple package items at Zone C hub.
     *
     * Business rules:
     *  - Authorization: Only ROLE_AGENT_C or ROLE_CASHIER can execute recuperation.
     *  - Discrete vs Bulk:
     *      * Discrete items require quantity_recupered (must be <= allocated).
     *      * Bulk items leave quantity_recupered as null and require weight_total_recupered.
     *  - Concurrency & State Machine:
     *      * Pessimistic row locking (lockForUpdate) on package items & parent order items.
     *      * Package items must be in CL_CONFIRMED status before handover.
     *      * Advances PackageItem to PACKAGE_ITEM_CL_RECEIVED (CLIENT_DELIVERED).
     *      * If sum of all recuperated quantities/weights across all package items meets or
     *        exceeds the original OrderItem declaration, updates OrderItem to ITEM_CL_FULFILLED.
     *
     * @param User $user The authenticated agent/cashier performing the handover.
     * @param array{items: list<array{package_item_id: int, quantity_recupered?: ?int, weight_total_recupered?: ?float, amount_total_recupered?: ?float, comment?: ?string}>, comment?: ?string} $data
     * @return array<PackageItem>
     */
    public function recuperate(User $user, array $data): array
    {
        // ── 1. Authorization: Only Zone C Agent or Cashier ──────────
        if (! $user->hasRole(User::ROLE_AGENT_C, User::ROLE_CASHIER)) {
            throw new AccessDeniedHttpException(
                'Only an Agent (Zone C) or Cashier can perform client item recuperations.'
            );
        }

        return DB::transaction(function () use ($user, $data): array {
            $results = [];
            $touchedOrderItemIds = [];

            // Sort incoming items by package_item_id to maintain consistent lock ordering and avoid deadlocks
            $itemsPayload = $data['items'];
            usort($itemsPayload, fn ($a, $b) => ($a['package_item_id'] ?? 0) <=> ($b['package_item_id'] ?? 0));

            foreach ($itemsPayload as $itemData) {
                $packageItemId = (int) $itemData['package_item_id'];

                // ── 2. Concurrency Lock: Lock PackageItem for update ─
                $packageItem = PackageItem::query()
                    ->lockForUpdate()
                    ->findOrFail($packageItemId);

                // ── 3. Pre-condition: Check Current Step Status ──────
                $currentStatusId = $packageItem->currentStep?->status_id;

                if ($currentStatusId === Status::PACKAGE_ITEM_CL_RECEIVED || $currentStatusId === Status::PACKAGE_ITEM_CLIENT_DELIVERED) {
                    throw ValidationException::withMessages([
                        'package_item_id' => "Package item #{$packageItemId} has already been recuperated by the client.",
                    ]);
                }

                if ($currentStatusId !== Status::PACKAGE_ITEM_CL_CONFIRMED) {
                    throw ValidationException::withMessages([
                        'package_item_id' => "Package item #{$packageItemId} is not ready for recuperation. It must be in CL_CONFIRMED status (current status ID: {$currentStatusId}).",
                    ]);
                }

                // ── 4. Lock and Load Parent OrderItem ────────────────
                $orderItem = OrderItem::query()
                    ->lockForUpdate()
                    ->findOrFail($packageItem->order_item_id);

                $unitPrice = (float) ($orderItem->price_unit_declared ?? 0);
                $unitWeight = (float) ($orderItem->weight_unit_declared ?? 0);
                $isBulk = is_null($packageItem->quantity_allocated);

                // ── 5. Process Bulk vs. Discrete ─────────────────────
                if ($isBulk) {
                    // MODE A: BULK ITEM (Weight only, no quantity)
                    $quantityRecupered = null;
                    $weightRecupered = (float) ($itemData['weight_total_recupered'] ?? $packageItem->weight_total_allocated);

                    if ($weightRecupered <= 0) {
                        throw ValidationException::withMessages([
                            'weight_total_recupered' => "Recuperated weight must be greater than 0 for bulk package item #{$packageItemId}.",
                        ]);
                    }

                    if ($weightRecupered > (float) $packageItem->weight_total_allocated) {
                        throw ValidationException::withMessages([
                            'weight_total_recupered' => "Recuperated weight ({$weightRecupered}g) cannot exceed allocated weight ({$packageItem->weight_total_allocated}g) for item #{$packageItemId}.",
                        ]);
                    }

                    // Calculate amount based on weight if not explicitly supplied
                    if (isset($itemData['amount_total_recupered'])) {
                        $amountRecupered = (float) $itemData['amount_total_recupered'];
                    } elseif ($unitPrice > 0 && $weightRecupered > 0) {
                        $amountRecupered = round($unitPrice * $weightRecupered, 2);
                    } else {
                        $amountRecupered = (float) $packageItem->amount_total_allocated;
                    }
                } else {
                    // MODE B: DISCRETE ITEM (Countable pieces)
                    $quantityRecupered = (int) ($itemData['quantity_recupered'] ?? $packageItem->quantity_allocated);

                    if ($quantityRecupered <= 0) {
                        throw ValidationException::withMessages([
                            'quantity_recupered' => "Recuperated quantity must be greater than 0 for discrete package item #{$packageItemId}.",
                        ]);
                    }

                    if ($quantityRecupered > (int) $packageItem->quantity_allocated) {
                        throw ValidationException::withMessages([
                            'quantity_recupered' => "Recuperated quantity ({$quantityRecupered}) cannot exceed allocated quantity ({$packageItem->quantity_allocated}) for item #{$packageItemId}.",
                        ]);
                    }

                    // Calculate weight if not explicitly supplied
                    if (isset($itemData['weight_total_recupered'])) {
                        $weightRecupered = (float) $itemData['weight_total_recupered'];
                    } elseif ($unitWeight > 0) {
                        $weightRecupered = round($quantityRecupered * $unitWeight, 3);
                    } else {
                        $weightRecupered = (float) $packageItem->weight_total_allocated;
                    }

                    // Calculate amount based on discrete pieces if not explicitly supplied
                    if (isset($itemData['amount_total_recupered'])) {
                        $amountRecupered = (float) $itemData['amount_total_recupered'];
                    } elseif ($unitPrice > 0) {
                        $amountRecupered = round($unitPrice * $quantityRecupered, 2);
                    } else {
                        $amountRecupered = (float) $packageItem->amount_total_allocated;
                    }
                }

                // ── 6. Update PackageItem Record ─────────────────────
                $packageItem->update([
                    'quantity_recupered'     => $quantityRecupered,
                    'weight_total_recupered' => $weightRecupered,
                    'amount_total_recupered' => $amountRecupered,
                    'updated_by'             => $user->id,
                ]);

                // ── 7. Create PackageItemStep (CLIENT_DELIVERED) ─────
                $step = $packageItem->steps()->create([
                    'status_id'   => Status::PACKAGE_ITEM_CL_RECEIVED,
                    'zone_id'     => $user->zone_id,
                    'user_id'     => $user->id,
                    'comment'     => $itemData['comment'] ?? ($data['comment'] ?? 'Recuperated by client at destination hub.'),
                    'created_by'  => $user->id,
                ]);

                $packageItem->update([
                    'current_step_id' => $step->id,
                ]);

                $touchedOrderItemIds[$orderItem->id] = $orderItem;
                $results[] = $packageItem;
            }

            // ── 8. Check Parent OrderItem Master Status ──────────────
            foreach ($touchedOrderItemIds as $orderItemId => $orderItem) {
                $isBulkOrderItem = is_null($orderItem->quantity_declared);

                if ($isBulkOrderItem) {
                    // Bulk order item: aggregate sum of recuperated weights
                    $totalRecuperedWeight = (float) PackageItem::query()
                        ->where('order_item_id', $orderItemId)
                        ->sum('weight_total_recupered');

                    $declaredWeight = (float) $orderItem->weight_total;
                    // Allow 0.005kg (5g) tolerance for scale discrepancies
                    $isFulfilled = ($totalRecuperedWeight >= $declaredWeight) || (abs($totalRecuperedWeight - $declaredWeight) <= 0.005);
                } else {
                    // Discrete order item: aggregate sum of recuperated quantities
                    $totalRecuperedQuantity = (int) PackageItem::query()
                        ->where('order_item_id', $orderItemId)
                        ->sum('quantity_recupered');

                    $declaredQuantity = (int) $orderItem->quantity_declared;
                    $isFulfilled = $totalRecuperedQuantity >= $declaredQuantity;
                }

                // If completely fulfilled and not already marked fulfilled
                if ($isFulfilled && $orderItem->currentStep?->status_id !== Status::ITEM_CL_FULFILLED) {
                    $orderItemStep = $orderItem->steps()->create([
                        'status_id'  => Status::ITEM_CL_FULFILLED,
                        'zone_id'    => $user->zone_id,
                        'user_id'    => $user->id,
                        'comment'    => 'Order item completely fulfilled and recuperated by client.',
                        'created_by' => $user->id,
                    ]);

                    $orderItem->updateQuietly([
                        'current_step_id' => $orderItemStep->id,
                    ]);
                }
            }

            // Reload relationships for clean API responses
            return array_map(function (PackageItem $item) {
                return $item->fresh()->load([
                    'currentStep.status',
                    'orderItem.currentStep.status',
                    'package',
                ]);
            }, $results);
        });
    }
}
