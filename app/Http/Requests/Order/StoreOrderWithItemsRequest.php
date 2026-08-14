<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderWithItemsRequest extends FormRequest
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
      'client_id' => ['required', 'integer', Rule::exists(User::class, 'id')],
      'comment' => ['nullable', 'string'],
      'items' => ['required', 'array'],
      'items.*.qr_code' => ['required', 'string', 'max:50', Rule::unique('order_items', 'qr_code')],
      'items.*.designation' => ['required', 'string', 'max:255'],
      'items.*.price_unit_declared' => ['required', 'numeric'],
      'items.*.quantity_declared' => ['nullable', 'integer'],
      'items.*.weight_unit_declared' => ['nullable', 'numeric'],
      'items.*.weight_total' => ['required', 'numeric'],
      'items.*.comment' => ['nullable', 'string'],
    ];
  }
}
