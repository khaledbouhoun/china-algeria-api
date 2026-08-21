<?php

declare(strict_types=1);

namespace App\Http\Resources\OrderItem;

use App\Http\Resources\OrderItemImage\OrderItemImageResource;
use App\Http\Resources\OrderItemStep\OrderItemStepResource;
use App\Http\Resources\Order\OrderResource;

use App\Http\Resources\PackageItem\PackageItemResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'item_code' => $this->item_code,
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
      'order' => $this->whenLoaded('order', fn() => new OrderResource($this->order)),
      'current_step' => $this->whenLoaded('currentStep', fn() => new OrderItemStepResource($this->currentStep)),
      'steps' => $this->whenLoaded('steps', fn() => OrderItemStepResource::collection($this->steps)),
      'images' => $this->whenLoaded('images', fn() => OrderItemImageResource::collection($this->images)),
      'packageItems' => $this->whenLoaded('packageItems', fn() => PackageItemResource::collection($this->packageItems)),
    ];
  }
}
