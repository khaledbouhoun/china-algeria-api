<?php

declare(strict_types=1);

namespace App\Http\Requests\PackageItem;

use App\Models\OrderItem;
use App\Models\Package;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePackageItemRequest extends FormRequest
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
      'package_id' => ['sometimes', 'integer', Rule::exists(Package::class, 'id'),],
      'order_item_id' => ['sometimes', 'integer', Rule::exists(OrderItem::class, 'id'),],
      'quantity_allocated' => ['sometimes', 'integer', 'min:1',],
      'weight_total_allocated' => ['sometimes', 'numeric', 'min:0',],
      'amount_total_allocated' => ['sometimes', 'numeric', 'min:0',],
      'quantity_recupered' => ['sometimes', 'integer', 'min:0',],
      'weight_total_recupered' => ['sometimes', 'numeric', 'min:0',],
      'amount_total_recupered' => ['sometimes', 'numeric', 'min:0',],
    ];
  }
}
