<?php

declare(strict_types=1);

namespace App\Http\Requests\PackageItemReception;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePackageItemReceptionRequest extends FormRequest
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
            'package_item_id' => ['sometimes', 'integer', Rule::exists('package_items', 'id')],
            'received_quantity' => [
                Rule::requiredIf(function () {
                    $packageItemId = $this->input('package_item_id');
                    if (!$packageItemId) {
                        $receptionId = $this->route('package_item_reception') ?? $this->input('id');
                        if ($receptionId) {
                            $reception = \App\Models\PackageItemReception::find($receptionId);
                            $packageItemId = $reception?->package_item_id;
                        }
                    }
                    $packageItem = \App\Models\PackageItem::find($packageItemId);
                    return $packageItem && !is_null($packageItem->quantity_allocated);
                }),
                'nullable',
                'integer',
                'min:0',
            ],
            'received_weight' => ['sometimes', 'numeric', 'min:0'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
