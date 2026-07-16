<?php

declare(strict_types=1);

namespace App\Http\Resources\OrderItem;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'public_code' => $this->public_code,
      'order_id' => $this->order_id,
      'designation' => $this->designation,
      'quantity_declared' => $this->quantity_declared,
      'price_unit_declared' => $this->price_unit_declared,
      'weight_unit_declared' => $this->weight_unit_declared,
      'weight_total' => $this->weight_total,
      'current_step_id' => $this->current_step_id,
      'comment' => $this->comment,
      'created_at' => $this->created_at?->toISOString(),
      'updated_at' => $this->updated_at?->toISOString(),
      'deleted_at' => $this->deleted_at?->toISOString(),
      'order' => $this->whenLoaded('order', fn() => new \App\Http\Resources\Order\OrderResource($this->order)),
      'current_step' => $this->whenLoaded('currentStep', fn() => new \App\Http\Resources\OrderItemStep\OrderItemStepResource($this->currentStep)),
      'steps' => $this->whenLoaded('steps', fn() => \App\Http\Resources\OrderItemStep\OrderItemStepResource::collection($this->steps)),
      'images' => $this->whenLoaded('images', fn() => \App\Http\Resources\OrderItemImage\OrderItemImageResource::collection($this->images)),
    ];
  }
}
