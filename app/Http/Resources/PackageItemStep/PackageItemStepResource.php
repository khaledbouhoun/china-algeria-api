<?php

declare(strict_types=1);

namespace App\Http\Resources\PackageItemStep;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageItemStepResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'package_item_id' => $this->package_item_id,
      'status_id' => $this->status_id,
      'zone_id' => $this->zone_id,
      'user_id' => $this->user_id,
      'comment' => $this->comment,
      'created_by' => $this->created_by,
      'created_at' => $this->created_at?->toISOString(),
      'package_item' => $this->whenLoaded('packageItem', fn() => new \App\Http\Resources\PackageItem\PackageItemResource($this->packageItem)),
      'status' => $this->whenLoaded('status', fn() => new \App\Http\Resources\Status\StatusResource($this->status)),
      'zone' => $this->whenLoaded('zone', fn() => new \App\Http\Resources\Zone\ZoneResource($this->zone)),
      'user' => $this->whenLoaded('user', fn() => new \App\Http\Resources\User\UserResource($this->user)),
      'creator' => $this->whenLoaded('creator', fn() => new \App\Http\Resources\User\UserResource($this->creator)),
    ];
  }
}
