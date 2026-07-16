<?php

declare(strict_types=1);

namespace App\Http\Resources\Country;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'country' => $this->country,
      'created_at' => $this->created_at?->toISOString(),
      'updated_at' => $this->updated_at?->toISOString(),
    ];
  }
}
