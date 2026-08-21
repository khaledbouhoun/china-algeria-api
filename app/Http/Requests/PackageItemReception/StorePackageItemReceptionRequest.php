<?php

declare(strict_types=1);

namespace App\Http\Requests\PackageItemReception;

use App\Models\PackageItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePackageItemReceptionRequest extends FormRequest
{
  public function authorize(): bool
  {
    // Role-based authorization (3-strike escalation) is enforced
    // in PackageItemReceptionService, not here.
    return true;
  }
  /**
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    return [
      'package_item_id' => ['required', 'integer', Rule::exists('package_items', 'id')],
      'received_quantity' => [
        Rule::requiredIf(function () {
          $packageItem = PackageItem::find($this->input('package_item_id'));

          return $packageItem && !is_null($packageItem->quantity_allocated);
        }),
        'nullable',
        'integer',
        'min:0',
      ],
      'received_weight' => ['required', 'numeric', 'min:0'],
      'comment' => ['nullable', 'string'],
    ];
  }
}
