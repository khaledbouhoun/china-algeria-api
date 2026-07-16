<?php

declare(strict_types=1);

namespace App\Http\Requests\Status;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
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
      'code' => ['sometimes', 'string', 'max:50', Rule::unique('statuses', 'code')->ignore($id)],
      'name' => ['sometimes', 'string', 'max:100'],
      'type' => ['sometimes', 'string', 'in:ITEM,PACKAGE_ITEM,PACKAGE,INSPECTION'],
    ];
  }
}
