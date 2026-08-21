<?php

declare(strict_types=1);

namespace App\Http\Requests\PackageItemReception;

use App\Models\PackageItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWithItemsPackageItemReceptionRequest extends FormRequest
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
            'items'                       => ['required', 'array', 'min:1'],
            'items.*.package_item_id'     => ['required', 'integer', Rule::exists('package_items', 'id')],
            'items.*.received_quantity'   => [
                Rule::requiredIf(function () {
                    // This closure runs once per request, not per item.
                    // Per-item conditional validation is handled below in withValidator().
                    return false;
                }),
                'nullable',
                'integer',
                'min:0',
            ],
            'items.*.received_weight'     => ['required', 'numeric', 'min:0'],
            'items.*.comment'             => ['nullable', 'string'],
        ];
    }

    /**
     * Add per-item conditional validation:
     * received_quantity is required only for Discrete items.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                $packageItemId = $item['package_item_id'] ?? null;
                if (! $packageItemId) {
                    continue;
                }

                $packageItem = PackageItem::find($packageItemId);
                if (! $packageItem) {
                    continue; // exists rule will catch this
                }

                // Discrete item: received_quantity is required
                $isDiscrete = ! is_null($packageItem->quantity_allocated);
                $receivedQty = $item['received_quantity'] ?? null;

                if ($isDiscrete && is_null($receivedQty)) {
                    $validator->errors()->add(
                        "items.{$index}.received_quantity",
                        "The received quantity is required for this discrete item."
                    );
                }
            }
        });
    }
}
