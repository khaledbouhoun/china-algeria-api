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
      'public_code' => ['required', 'string', 'max:50', Rule::unique('order_items', 'public_code')],
      'order_id' => ['required', 'integer', Rule::exists(Order::class, 'id')],
      'designation' => ['required', 'string', 'max:255'],
      'quantity_declared' => ['nullable', 'integer'],
      'price_unit_declared' => ['nullable', 'numeric'],
      'weight_unit_declared' => ['nullable', 'numeric'],
      'weight_total' => ['nullable', 'numeric'],
      'current_step_id' => ['required', 'integer', Rule::exists(OrderItemStep::class, 'id')],
      'comment' => ['nullable', 'string'],
    ];
  }
}
