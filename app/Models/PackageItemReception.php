<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageItemReception extends Model
{
  use HasFactory;

  protected $table = 'package_item_receptions';

  protected $fillable = [
    'package_item_id',
    'inspected_by',
    'expected_quantity',
    'expected_weight',
    'received_quantity',
    'received_weight',
    'count_reception',
    'comment',
  ];

  protected $casts = [
    'id' => 'integer',
    'package_item_id' => 'integer',
    'inspected_by' => 'integer',
    'expected_quantity' => 'integer',
    'expected_weight' => 'decimal:3',
    'received_quantity' => 'integer',
    'received_weight' => 'decimal:3',
    'difference_quantity' => 'integer',
    'difference_weight' => 'decimal:3',
    'count_reception' => 'integer',
    'created_at' => 'datetime',
  ];

  public function packageItem(): BelongsTo
  {
    return $this->belongsTo(PackageItem::class, 'package_item_id');
  }

  public function inspector(): BelongsTo
  {
    return $this->belongsTo(User::class, 'inspected_by');
  }
}
