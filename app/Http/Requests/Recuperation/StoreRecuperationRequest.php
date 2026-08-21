<?php

declare(strict_types=1);

namespace App\Http\Requests\Recuperation;

use App\Models\PackageItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecuperationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'items'                          => ['required', 'array', 'min:1'],
            'items.*.package_item_id'        => ['required', 'integer', Rule::exists('package_items', 'id')],
            'items.*.quantity_recupered'      => ['nullable', 'integer', 'min:0'],
            'items.*.weight_total_recupered' => ['nullable', 'numeric', 'min:0'],
            'items.*.amount_total_recupered' => ['nullable', 'numeric', 'min:0'],
            'items.*.comment'                => ['nullable', 'string', 'max:1000'],
            'comment'                        => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom per-item validation for Discrete vs Bulk items.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            if (!is_array($items) || empty($items)) {
                return;
            }

            $packageItemIds = array_filter(array_column($items, 'package_item_id'));
            if (empty($packageItemIds)) {
                return;
            }

            $packageItems = PackageItem::whereIn('id', $packageItemIds)->get()->keyBy('id');

            foreach ($items as $index => $item) {
                $packageItemId = $item['package_item_id'] ?? null;
                if (! $packageItemId || ! isset($packageItems[$packageItemId])) {
                    continue; // exists rule handles missing records
                }

                $packageItem = $packageItems[$packageItemId];
                $isDiscrete = ! is_null($packageItem->quantity_allocated);

                $recuperedQty = $item['quantity_recupered'] ?? null;
                $recuperedWeight = $item['weight_total_recupered'] ?? null;

                if ($isDiscrete) {
                    // Discrete Item: quantity_recupered is required
                    if (is_null($recuperedQty)) {
                        $validator->errors()->add(
                            "items.{$index}.quantity_recupered",
                            "The quantity recupered is required for discrete package item #{$packageItemId}."
                        );
                    } elseif ($recuperedQty > $packageItem->quantity_allocated) {
                        $validator->errors()->add(
                            "items.{$index}.quantity_recupered",
                            "The quantity recupered ({$recuperedQty}) cannot exceed allocated quantity ({$packageItem->quantity_allocated}) for item #{$packageItemId}."
                        );
                    }
                } else {
                    // Bulk Item: weight_total_recupered is required, quantity must be null
                    if (is_null($recuperedWeight)) {
                        $validator->errors()->add(
                            "items.{$index}.weight_total_recupered",
                            "The weight total recupered is required for bulk package item #{$packageItemId}."
                        );
                    } elseif ((float) $recuperedWeight > (float) $packageItem->weight_total_allocated) {
                        $validator->errors()->add(
                            "items.{$index}.weight_total_recupered",
                            "The weight total recupered ({$recuperedWeight}g) cannot exceed allocated weight ({$packageItem->weight_total_allocated}g) for item #{$packageItemId}."
                        );
                    }
                }
            }
        });
    }
}
