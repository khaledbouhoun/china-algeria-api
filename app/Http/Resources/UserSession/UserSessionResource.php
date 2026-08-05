<?php

declare(strict_types=1);

namespace App\Http\Resources\UserSession;

use App\Http\Resources\User\UserResource;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSessionResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'user_id' => $this->user_id,
      'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
      'created_at' => $this->created_at?->toISOString(),
    ];
  }
}
