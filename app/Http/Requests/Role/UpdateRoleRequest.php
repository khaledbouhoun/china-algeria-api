<?php

declare(strict_types=1);

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
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
    $id = $this->route('id');

    return [
      'code' => ['sometimes', 'string', 'max:50', Rule::unique('roles', 'code')->ignore($id)],
      'name' => ['sometimes', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($id)],
    ];
  }
}
