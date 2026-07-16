<?php

declare(strict_types=1);

namespace App\Http\Requests\Package;

use App\Models\PackageStep;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePackageRequest extends FormRequest
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
      'qr_code' => ['required', 'string', 'max:255', Rule::unique('packages', 'qr_code')],
      'items_count' => ['nullable', 'integer'],
      'weight' => ['nullable', 'numeric'],
      'amount' => ['nullable', 'numeric'],
      'comment' => ['nullable', 'string'],
      'created_by' => ['nullable', 'integer', Rule::exists(User::class, 'id')],
      'updated_by' => ['nullable', 'integer', Rule::exists(User::class, 'id')],
      'gladiator_id' => ['nullable', 'integer', Rule::exists(User::class, 'id')],
      'current_step_id' => ['required', 'integer', Rule::exists(PackageStep::class, 'id')],
    ];
  }
}
