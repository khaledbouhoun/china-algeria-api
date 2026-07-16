<?php

declare(strict_types=1);

namespace App\Http\Requests\Wallet;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWalletRequest extends FormRequest
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
      'user_id' => ['sometimes', 'integer', Rule::exists(User::class, 'id'), Rule::unique('wallets', 'user_id')->ignore($this->route('wallet'))],
      'role_id' => ['sometimes', 'integer', Rule::exists(Role::class, 'id')],
      'balance' => ['nullable', 'numeric'],
    ];
  }
}
