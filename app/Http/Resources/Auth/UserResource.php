<?php

namespace App\Http\Resources\Auth;

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
            'id'                => $this->id,
            'public_code'       => $this->public_code,
            'full_name'         => $this->full_name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'address'           => $this->address,
            'status'            => $this->status,
            'role_id'           => $this->role_id,
            'zone_id'           => $this->zone_id,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'last_login_at'     => $this->last_login_at?->toIso8601String(),
            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),

            // Relations — present only when the relation was loaded.
            'role' => $this->whenLoaded('role', fn() => [
                'id'   => $this->role->id,
                'code' => $this->role->code,
                'name' => $this->role->name,
            ]),
            'zone' => $this->whenLoaded('zone', fn() => $this->zone ? [
                'id'   => $this->zone->id,
                'code' => $this->zone->code,
                'name' => $this->zone->name,
            ] : null),
        ];
    }

}
