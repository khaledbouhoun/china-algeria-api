<?php

declare(strict_types=1);

namespace App\Http\Requests\Zone;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateZoneRequest extends FormRequest
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
      'name' => ['sometimes', 'string', 'max:100'],
      'zone_type' => ['sometimes', 'string', 'in:ZONE_A,ZONE_B,ZONE_C,EVERYWHERE'],
      'description' => ['nullable', 'string'],
    ];
  }
}
