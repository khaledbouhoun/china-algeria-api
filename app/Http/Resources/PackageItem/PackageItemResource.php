<?php

declare(strict_types=1);

namespace App\Http\Resources\PackageItem;

use App\Http\Resources\OrderItem\OrderItemResource;
use App\Http\Resources\Package\PackageResource;
use App\Http\Resources\PackageItemReception\PackageItemReceptionResource;
use App\Http\Resources\PackageItemStep\PackageItemStepResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageItemResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'package_id' => $this->package_id,
      'order_item_id' => $this->order_item_id,
      'quantity_allocated' => $this->quantity_allocated,
      'weight_total_allocated' => $this->weight_total_allocated,
      'amount_total_allocated' => $this->amount_total_allocated,
      'current_step_id' => $this->current_step_id,
      'quantity_recupered' => $this->quantity_recupered,
      'weight_total_recupered' => $this->weight_total_recupered,
      'amount_total_recupered' => $this->amount_total_recupered,
      'created_by' => $this->created_by,
      'updated_by' => $this->updated_by,
      'created_at' => $this->created_at?->toISOString(),
      'updated_at' => $this->updated_at?->toISOString(),
      'package' => $this->whenLoaded('package', fn() => new PackageResource($this->package)),
      'order_item' => $this->whenLoaded('orderItem', fn() => new OrderItemResource($this->orderItem)),
      'current_step' => $this->whenLoaded('currentStep', fn() => new PackageItemStepResource($this->currentStep)),
      'steps' => $this->whenLoaded('steps', fn() => PackageItemStepResource::collection($this->steps)),
      'receptions' => $this->whenLoaded('receptions', fn() => PackageItemReceptionResource::collection($this->receptions)),
    ];
  }
}
