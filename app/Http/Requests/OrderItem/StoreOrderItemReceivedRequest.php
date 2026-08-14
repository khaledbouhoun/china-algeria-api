<?php

declare(strict_types=1);

namespace App\Http\Requests\OrderItem;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemStep;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderItemReceivedRequest extends FormRequest
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
      'items_ids' => ['required', 'array', 'min:1'],
      'items_ids.*' => ['required', 'integer', Rule::exists(OrderItem::class, 'id')],
    ];
  }
}
