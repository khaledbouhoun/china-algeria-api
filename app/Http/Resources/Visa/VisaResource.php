<?php

declare(strict_types=1);

namespace App\Http\Resources\Visa;

use App\Http\Resources\User\UserResource;

use Illuminate\Http\Resources\Json\JsonResource;

class VisaResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'user_id' => $this->user_id,
      'visa_status' => $this->visa_status,
      'date_from' => $this->date_from?->toISOString(),
      'date_to' => $this->date_to?->toISOString(),
      'number' => $this->number,
      'created_by' => $this->created_by,
      'created_at' => $this->created_at?->toISOString(),
      'updated_at' => $this->updated_at?->toISOString(),
      'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
      'creator' => $this->whenLoaded('creator', fn() => new UserResource($this->creator)),
    ];
  }
}
