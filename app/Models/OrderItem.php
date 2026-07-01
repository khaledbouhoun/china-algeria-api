<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @property int $id
     */

    protected $fillable = [
    ];

    protected $casts = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, "order_id");
    }

    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(OrderItemStep::class, "current_step_id");
    }

    public function steps(): HasMany
    {
        return $this->hasMany(OrderItemStep::class, "item_id")->orderByDesc("created_at");
    }

}
