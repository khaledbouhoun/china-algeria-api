<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Models\Role;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
      'public_code' => ['sometimes', 'string', 'max:50', Rule::unique('users', 'public_code')->ignore($this->route('user'))],
      'full_name' => ['sometimes', 'string', 'max:255'],
      'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
      'phone' => ['nullable', 'string', 'max:50'],
      'address' => ['nullable', 'string'],
      'firebase_uid' => ['nullable', 'string', 'max:255'],
      'role_id' => ['sometimes', 'integer', Rule::exists(Role::class, 'id')],
      'zone_id' => ['nullable', 'integer', Rule::exists(Zone::class, 'id')],
      'status' => ['nullable', 'string', 'max:20', Rule::in(User::STATUSES)],
      'proved_at' => ['nullable', 'date'],
      'last_login_at' => ['nullable', 'date'],
    ];
  }
}
