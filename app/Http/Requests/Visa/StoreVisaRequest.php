<?php

declare(strict_types=1);

namespace App\Http\Requests\Visa;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVisaRequest extends FormRequest
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
      'user_id' => ['required', 'integer', Rule::exists(User::class, 'id'), Rule::unique('visas', 'user_id')],
      'visa_status' => ['required', 'string', 'max:20'],
      'date_from' => ['required', 'date'],
      'date_to' => ['required', 'date', 'after_or_equal:date_from'],
      'number' => ['required', 'string', 'max:50', Rule::unique('visas', 'number')],
      'created_by' => ['required', 'integer', Rule::exists(User::class, 'id')],
    ];
  }
}
