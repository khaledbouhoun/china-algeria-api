<?php

declare(strict_types=1);

namespace App\Http\Requests\UserSession;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserSessionRequest extends FormRequest
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
      'user_id' => ['required', 'integer', 'exists:users,id'],
    ];
  }
}
