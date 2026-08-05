<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use App\Http\Resources\Role\RoleResource;
use App\Http\Resources\Zone\ZoneResource;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'public_code' => $this->public_code,
      'full_name' => $this->full_name,
      'email' => $this->email,
      'phone' => $this->phone,
      'address' => $this->address,
      'role_id' => $this->role_id,
      'zone_id' => $this->zone_id,
      'status' => $this->status,
      'proved_at' => $this->proved_at?->toISOString(),
      'last_login_at' => $this->last_login_at?->toISOString(),
      'created_at' => $this->created_at?->toISOString(),
      'updated_at' => $this->updated_at?->toISOString(),
      'deleted_at' => $this->deleted_at?->toISOString(),
      'role' => $this->whenLoaded('role', fn() => new RoleResource($this->role)),
      'zone' => $this->whenLoaded('zone', fn() => new ZoneResource($this->zone)),
    ];
  }
}
