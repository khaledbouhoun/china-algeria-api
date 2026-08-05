<?php

declare(strict_types=1);

namespace App\Http\Resources\OrderItemImage;

use App\Http\Resources\OrderItem\OrderItemResource;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemImageResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'order_item_id' => $this->order_item_id,
      'image_path' => $this->image_path,
      'created_at' => $this->created_at?->toISOString(),
      'updated_at' => $this->updated_at?->toISOString(),
      'order_item' => $this->whenLoaded('orderItem', fn() => new OrderItemResource($this->orderItem)),
    ];
  }
}
