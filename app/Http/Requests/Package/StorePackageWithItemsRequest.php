<?php

declare(strict_types=1);

namespace App\Http\Requests\Package;

use App\Models\OrderItem;
use App\Models\PackageStep;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePackageWithItemsRequest extends FormRequest
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
      'qr_code' => ['required', 'string', 'max:255', Rule::unique('packages', 'qr_code')],
      'weight' => ['nullable', 'numeric'],
      'amount' => ['nullable', 'numeric'],
      'comment' => ['nullable', 'string'],
      'items' => ['required', 'array'],
      'items.*.order_item_id' => ['required', 'integer', Rule::exists(OrderItem::class, 'id')],
      'items.*.quantity_allocated' => ['required', 'integer', 'min:1'],
      // 'items.*.weight_total_allocated' => ['required', 'numeric', 'min:0'],
      // 'items.*.amount_total_allocated' => ['required', 'numeric', 'min:0'],
    ];
  }
}
