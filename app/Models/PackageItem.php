<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageItem extends Model
{
  use HasFactory;

  protected $table = 'package_items';

  protected $fillable = [
    'package_id',
    'order_item_id',
    'quantity_allocated',
    'weight_total_allocated',
    'amount_total_allocated',
    'current_step_id',
    'quantity_recupered',
    'weight_total_recupered',
    'amount_total_recupered',
    'created_by',
    'updated_by',
  ];

  protected $casts = [
    'id' => 'integer',
    'package_id' => 'integer',
    'order_item_id' => 'integer',
    'quantity_allocated' => 'integer',
    'weight_total_allocated' => 'decimal:3',
    'amount_total_allocated' => 'decimal:2',
    'current_step_id' => 'integer',
    'quantity_recupered' => 'integer',
    'weight_total_recupered' => 'decimal:3',
    'amount_total_recupered' => 'decimal:2',
    'created_by' => 'integer',
    'updated_by' => 'integer',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  public function package(): BelongsTo
  {
    return $this->belongsTo(Package::class, 'package_id');
  }

  public function orderItem(): BelongsTo
  {
    return $this->belongsTo(OrderItem::class, 'order_item_id');
  }

  public function currentStep(): BelongsTo
  {
    return $this->belongsTo(PackageItemStep::class, 'current_step_id');
  }

  public function steps(): HasMany
  {
    return $this->hasMany(PackageItemStep::class, 'package_item_id')->orderByDesc('created_at');
  }

  public function receptions(): HasMany
  {
    return $this->hasMany(PackageItemReception::class, 'package_item_id');
  }

  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function updater(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }
}
