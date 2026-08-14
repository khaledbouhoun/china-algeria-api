<?php

declare(strict_types=1);

namespace App\Http\Requests\Package;

use App\Models\Package;
use App\Models\PackageStep;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePackageReceivedRequest extends FormRequest
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
      'package_ids' => ['required', 'array', 'min:1'],
      'package_ids.*' => ['required', 'integer', Rule::exists(Package::class, 'id')],
    ];
  }
}
