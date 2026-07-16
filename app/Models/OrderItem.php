<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'order_items';

  protected $fillable = [
    'public_code',
    'order_id',
    'designation',
    'quantity_declared',
    'price_unit_declared',
    'weight_unit_declared',
    'weight_total',
    'current_step_id',
    'comment',
  ];

  protected $casts = [
    'id' => 'integer',
    'order_id' => 'integer',
    'quantity_declared' => 'integer',
    'price_unit_declared' => 'decimal:2',
    'weight_unit_declared' => 'decimal:3',
    'weight_total' => 'decimal:3',
    'current_step_id' => 'integer',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
  ];

  public function order(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'order_id');
  }

  public function currentStep(): BelongsTo
  {
    return $this->belongsTo(OrderItemStep::class, 'current_step_id');
  }

  public function steps(): HasMany
  {
    return $this->hasMany(OrderItemStep::class, 'item_id')->orderByDesc('created_at');
  }

  public function images(): HasMany
  {
    return $this->hasMany(OrderItemImage::class, 'order_item_id');
  }

  public function packageItems(): HasMany
  {
    return $this->hasMany(PackageItem::class, 'order_item_id');
  }
}
