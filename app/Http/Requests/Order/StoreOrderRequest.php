<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
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
    ];
  }
}
