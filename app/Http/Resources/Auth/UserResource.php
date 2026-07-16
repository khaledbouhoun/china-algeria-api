<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\Role\RoleResource;
use App\Http\Resources\Zone\ZoneResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforms a User model into a consistent JSON representation.
 *
 * Included relations (eager-loaded by services):
 *   - role  (id, code, name)
 *   - zone  (id, code, name)
 *
 * Sensitive fields (firebase_uid, deleted_at) are intentionally excluded.
 */
class UserResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      ...parent::toArray($request),

      'role' => RoleResource::make($this->whenLoaded('role')),
      'zone' => ZoneResource::make($this->whenLoaded('zone')),
    ];
  }

}
