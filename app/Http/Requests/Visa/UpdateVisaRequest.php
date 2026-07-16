<?php

declare(strict_types=1);

namespace App\Http\Requests\Visa;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVisaRequest extends FormRequest
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
      'user_id' => ['sometimes', 'integer', Rule::exists(User::class, 'id'), Rule::unique('visas', 'user_id')->ignore($this->route('visa'))],
      'visa_status' => ['sometimes', 'string', 'max:20'],
      'date_from' => ['sometimes', 'date'],
      'date_to' => ['sometimes', 'date', 'after_or_equal:date_from'],
      'number' => ['sometimes', 'string', 'max:50', Rule::unique('visas', 'number')->ignore($this->route('visa'))],
      'created_by' => ['sometimes', 'integer', Rule::exists(User::class, 'id')],
    ];
  }
}
