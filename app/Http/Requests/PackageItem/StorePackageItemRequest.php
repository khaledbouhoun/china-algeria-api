<?php

declare(strict_types=1);

namespace App\Http\Requests\PackageItem;

use App\Models\OrderItem;
use App\Models\Package;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePackageItemRequest extends FormRequest
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
      'package_id' => ['required', 'integer', Rule::exists(Package::class, 'id'),],
      'order_item_id' => ['required', 'integer', Rule::exists(OrderItem::class, 'id'),],
      'quantity_allocated' => ['required', 'integer', 'min:1',],
      // 'weight_total_allocated' => ['required', 'numeric', 'min:0',],
      // 'amount_total_allocated' => ['required', 'numeric', 'min:0',],
    ];
  }
}
