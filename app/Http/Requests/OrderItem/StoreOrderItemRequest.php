<?php

declare(strict_types=1);

namespace App\Http\Requests\OrderItem;

use App\Models\Order;
use App\Models\OrderItemStep;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderItemRequest extends FormRequest
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
      'item_code' => ['nullable', 'string', 'max:50', Rule::unique('order_items', 'item_code')],
      'order_id' => ['required', 'integer', Rule::exists(Order::class, 'id')],
      'designation' => ['required', 'string', 'max:255'],
      'price_unit_declared' => ['required', 'numeric'],
      'quantity_declared' => ['nullable', 'integer'],
      'weight_unit_declared' => ['nullable', 'numeric'],
      'weight_total' => ['required', 'numeric'],
      'comment' => ['nullable', 'string'],
    ];
  }
}
