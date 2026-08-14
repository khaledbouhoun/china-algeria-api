<?php

declare(strict_types=1);

namespace App\Http\Requests\Package;

use App\Models\OrderItem;
use App\Models\Package;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePackageWithItemsRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'qr_code' => ['required', 'string', 'max:255', Rule::unique('packages', 'qr_code')],

      // Enforce the 23kg limit on the total package weight
      'weight' => ['nullable', 'numeric', 'max:' . Package::MAX_WEIGHT_KG],

      'amount' => ['nullable', 'numeric'],
      'comment' => ['nullable', 'string'],
      'items' => ['required', 'array'],
      'items.*.order_item_id' => ['required', 'integer', Rule::exists(OrderItem::class, 'id')],
      'items.*.quantity_allocated' => ['nullable', 'integer', 'min:0'],

      // Also ensure a single item doesn't exceed the package limit
      'items.*.weight_total_allocated' => ['required', 'numeric', 'min:0', 'max:' . Package::MAX_WEIGHT_KG],

      'items.*.amount_total_allocated' => ['nullable', 'numeric', 'min:0'],
    ];
  }

  // Optional: Add a custom error message so the frontend knows exactly why it failed
  public function messages(): array
  {
    return [
      'weight.max' => 'A package cannot exceed ' . Package::MAX_WEIGHT_KG . ' kg due to airline regulations.',
      'items.*.weight_total_allocated.max' => 'An item cannot exceed the maximum package limit of ' . Package::MAX_WEIGHT_KG . ' kg.',
    ];
  }

  protected function withValidator(Validator $validator): void
  {
    $validator->after(function ($validator) {
      $items = $this->input('items', []);

      // 1. حساب مجموع أوزان العناصر المُرسلة
      $totalItemsWeight = collect($items)->sum('weight_total_allocated');

      // 2. التحقق من أن المجموع لا يتجاوز 23 كغ
      if ($totalItemsWeight > 23.0) {
        $validator->errors()->add(
          'items',
          "The total weight of all items combined ({$totalItemsWeight} kg) exceeds the maximum allowed package weight of 23 kg."
        );
      }

      // 3. (اختياري ويفضل جداً) التحقق من أن وزن الـ Package الخارجي يساوي مجموع أوزان العناصر
      $packageWeight = (float) $this->input('weight', 0);
      if ($packageWeight > 0 && $packageWeight !== (float) $totalItemsWeight) {
        $validator->errors()->add(
          'weight',
          "The declared package weight ({$packageWeight} kg) does not match the sum of its items ({$totalItemsWeight} kg)."
        );
      }
    });
  }
}
