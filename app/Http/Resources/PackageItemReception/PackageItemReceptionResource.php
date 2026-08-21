<?php

declare(strict_types=1);

namespace App\Http\Resources\PackageItemReception;

use App\Http\Resources\PackageItem\PackageItemResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageItemReceptionResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id'                  => $this->id,
      'package_item_id'     => $this->package_item_id,
      'inspected_by'        => $this->inspected_by,
      'expected_quantity'    => $this->expected_quantity,
      'expected_weight'     => $this->expected_weight,
      'received_quantity'   => $this->received_quantity,
      'received_weight'     => $this->received_weight,
      'difference_quantity' => $this->difference_quantity,
      'difference_weight'   => $this->difference_weight,
      'count_reception'     => $this->count_reception,
      'comment'             => $this->comment,
      'created_at'          => $this->created_at?->toISOString(),
      'package_item'        => $this->whenLoaded('packageItem', fn () => new PackageItemResource($this->packageItem)),
      'inspector'           => $this->whenLoaded('inspector', fn () => new UserResource($this->inspector)),
    ];
  }
}
