<?php

declare(strict_types=1);

namespace App\Http\Requests\Zone;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreZoneRequest extends FormRequest
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
      'name' => ['required', 'string', 'max:100'],
      'zone_type' => ['required', 'string', 'in:ZONE_A,ZONE_B,ZONE_C,EVERYWHERE'],
      'description' => ['nullable', 'string'],
    ];
  }
}
