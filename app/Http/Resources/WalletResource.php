<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\WalletTransaction\WalletTransactionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'user_id' => $this->user_id,
      'role_id' => $this->role_id,
      'balance' => $this->balance,
      'created_at' => $this->created_at?->toISOString(),
      'updated_at' => $this->updated_at?->toISOString(),
      'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
      'role' => $this->whenLoaded('role', fn() => new RoleResource($this->role)),
      'transactions' => $this->whenLoaded('transactions', fn() => WalletTransactionResource::collection($this->transactions)),
    ];
  }
}
