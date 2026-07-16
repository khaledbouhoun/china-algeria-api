<?php

declare(strict_types=1);

namespace App\Http\Requests\Status;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStatusRequest extends FormRequest
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
      'code' => ['required', 'string', 'max:50', 'unique:statuses,code'],
      'name' => ['required', 'string', 'max:100'],
      'type' => ['required', 'string', 'in:ITEM,PACKAGE_ITEM,PACKAGE,INSPECTION'],
    ];
  }
}
