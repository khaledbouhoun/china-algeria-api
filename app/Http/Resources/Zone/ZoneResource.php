<?php

declare(strict_types=1);

namespace App\Http\Resources\Zone;

use Illuminate\Http\Resources\Json\JsonResource;

class ZoneResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'zone_type' => $this->zone_type,
      'description' => $this->description,
    ];
  }
}
