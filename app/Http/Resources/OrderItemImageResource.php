<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemImageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
