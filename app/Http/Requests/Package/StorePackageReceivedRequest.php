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
    return $this->user()->hasRole(
      User::ROLE_ADMIN,
      User::ROLE_AGENT_A,
      User::ROLE_GLADIATOR,
      User::ROLE_DELIVERY,
      User::ROLE_AGENT_C
    );
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
