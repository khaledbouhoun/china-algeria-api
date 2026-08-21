<?php

declare(strict_types=1);

namespace App\Http\Resources\Recuperation;

use App\Http\Resources\OrderItem\OrderItemResource;
use App\Http\Resources\Package\PackageResource;
use App\Http\Resources\PackageItemStep\PackageItemStepResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RecuperationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                     => $this->id,
            'package_id'             => $this->package_id,
            'order_item_id'          => $this->order_item_id,
            'is_bulk'                => is_null($this->quantity_allocated),
            'quantity_allocated'     => $this->quantity_allocated,
            'weight_total_allocated' => $this->weight_total_allocated,
            'amount_total_allocated' => $this->amount_total_allocated,
            'quantity_recupered'     => $this->quantity_recupered,
            'weight_total_recupered' => $this->weight_total_recupered,
            'amount_total_recupered' => $this->amount_total_recupered,
            'current_step_id'        => $this->current_step_id,
            'current_step'           => $this->whenLoaded('currentStep', fn () => new PackageItemStepResource($this->currentStep)),
            'order_item'             => $this->whenLoaded('orderItem', fn () => new OrderItemResource($this->orderItem)),
            'package'                => $this->whenLoaded('package', fn () => new PackageResource($this->package)),
            'updated_by'             => $this->updated_by,
            'updated_at'             => $this->updated_at?->toISOString(),
        ];
    }
}
