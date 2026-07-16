<?php

declare(strict_types=1);

namespace App\Http\Resources\Package;

use App\Http\Resources\PackageItem\PackageItemResource;
use App\Http\Resources\PackageStep\PackageStepResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'qr_code' => $this->qr_code,
      'items_count' => $this->items_count,
      'weight' => $this->weight,
      'amount' => $this->amount,
      'comment' => $this->comment,
      'created_by' => $this->created_by,
      'updated_by' => $this->updated_by,
      'gladiator_id' => $this->gladiator_id,
      'current_step_id' => $this->current_step_id,
      'created_at' => $this->created_at?->toISOString(),
      'updated_at' => $this->updated_at?->toISOString(),
      'current_step' => $this->whenLoaded('currentStep', fn() => new PackageStepResource($this->currentStep)),
      'steps' => $this->whenLoaded('steps', fn() => PackageStepResource::collection($this->steps)),
      'items' => $this->whenLoaded('items', fn() => PackageItemResource::collection($this->items)),
    ];
  }
}
