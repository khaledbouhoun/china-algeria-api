<?php

declare(strict_types=1);

namespace App\Http\Requests\OrderItem;

use App\Models\Order;
use App\Models\OrderItemStep;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderItemRequest extends FormRequest
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
      'item_code' => ['sometimes', 'string', 'max:50', Rule::unique('order_items', 'item_code')->ignore($this->route('orderItem'))],
      'order_id' => ['sometimes', 'integer', Rule::exists(Order::class, 'id')],
      'designation' => ['sometimes', 'string', 'max:255'],
      'quantity_declared' => ['nullable', 'integer'],
      'price_unit_declared' => ['nullable', 'numeric'],
      'weight_unit_declared' => ['nullable', 'numeric'],
      'weight_total' => ['nullable', 'numeric'],
      'current_step_id' => ['sometimes', 'integer', Rule::exists(OrderItemStep::class, 'id')],
      'comment' => ['nullable', 'string'],
    ];
  }
}
