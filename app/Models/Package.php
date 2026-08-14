<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
  use HasFactory;

  // Define the maximum allowed weight per package
  public const MAX_WEIGHT_KG = 23.0;

  protected $table = 'packages';

  protected $fillable = [
    'qr_code',
    'items_count',
    'weight',
    'amount',
    'comment',
    'created_by',
    'updated_by',
    'gladiator_id',
    'current_step_id',
  ];

  protected $casts = [
    'id' => 'integer',
    'items_count' => 'integer',
    'weight' => 'decimal:3',
    'amount' => 'decimal:2',
    'created_by' => 'integer',
    'updated_by' => 'integer',
    'gladiator_id' => 'integer',
    'current_step_id' => 'integer',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  public function currentStep(): BelongsTo
  {
    return $this->belongsTo(PackageStep::class, 'current_step_id');
  }

  public function steps(): HasMany
  {
    return $this->hasMany(PackageStep::class, 'package_id')->orderByDesc('created_at');
  }

  public function items(): HasMany
  {
    return $this->hasMany(PackageItem::class, 'package_id');
  }

  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function updater(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }

  public function gladiator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'gladiator_id');
  }
}
