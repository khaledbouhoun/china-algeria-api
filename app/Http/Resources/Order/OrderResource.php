<?php

declare(strict_types=1);

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'client_id' => $this->client_id,
      'comment' => $this->comment,
      'created_at' => $this->created_at?->toISOString(),
      'updated_at' => $this->updated_at?->toISOString(),
      'deleted_at' => $this->deleted_at?->toISOString(),
      'client' => $this->whenLoaded('client', fn() => new \App\Http\Resources\User\UserResource($this->client)),
      'items' => $this->whenLoaded('items', fn() => \App\Http\Resources\OrderItem\OrderItemResource::collection($this->items)),
    ];
  }
}
