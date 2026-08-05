<?php

declare(strict_types=1);

namespace App\Http\Resources\Order;

use App\Http\Resources\OrderItem\OrderItemResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'client_id' => $this->client_id,
      'comment' => $this->comment,
      'items_count' => $this->items_count,
      'created_at' => $this->created_at?->toISOString(),
      'updated_at' => $this->updated_at?->toISOString(),
      'deleted_at' => $this->deleted_at?->toISOString(),
      'items' => $this->whenLoaded('items', fn() => OrderItemResource::collection($this->items)),
    ];
  }
}
