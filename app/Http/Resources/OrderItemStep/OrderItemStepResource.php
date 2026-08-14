<?php

declare(strict_types=1);

namespace App\Http\Resources\OrderItemStep;

use App\Http\Resources\OrderItem\OrderItemResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Zone\ZoneResource;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemStepResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'item_id' => $this->item_id,
      'status_id' => $this->status_id,
      'zone_id' => $this->zone_id,
      'user_id' => $this->user_id,
      'comment' => $this->comment,
      'created_at' => $this->created_at?->toISOString(),
      'item' => $this->whenLoaded('item', fn() => new OrderItemResource($this->item)),
      'status' => $this->whenLoaded('status', fn() => new StatusResource($this->status)),
      'zone' => $this->whenLoaded('zone', fn() => new ZoneResource($this->zone)),
      'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
      'creator' => $this->whenLoaded('creator', fn() => new UserResource($this->creator)),
    ];
  }
}
