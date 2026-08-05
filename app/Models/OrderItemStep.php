<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemStep extends Model
{
  use HasFactory;
  public const UPDATED_AT = null;


  protected $table = 'order_item_steps';

  protected $fillable = [
    'item_id',
    'status_id',
    'zone_id',
    'user_id',
    'comment',
    'created_by',
  ];

  protected $casts = [
    'item_id' => 'integer',
    'status_id' => 'integer',
    'zone_id' => 'integer',
    'user_id' => 'integer',
    'created_by' => 'integer',
    'created_at' => 'datetime',
  ];

  public function item(): BelongsTo
  {
    return $this->belongsTo(OrderItem::class, 'item_id');
  }

  public function status(): BelongsTo
  {
    return $this->belongsTo(Status::class, 'status_id');
  }

  public function zone(): BelongsTo
  {
    return $this->belongsTo(Zone::class, 'zone_id');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }
}
