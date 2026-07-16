<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
  use HasFactory;

  protected $table = 'statuses';

  protected $fillable = [
    'code',
    'name',
    'type',
  ];

  protected $casts = [
    'id' => 'integer',
    'type' => StatusType::class,
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  public function orderItemSteps(): HasMany
  {
    return $this->hasMany(OrderItemStep::class, 'status_id');
  }

  public function packageItemSteps(): HasMany
  {
    return $this->hasMany(PackageItemStep::class, 'status_id');
  }

  public function packageSteps(): HasMany
  {
    return $this->hasMany(PackageStep::class, 'status_id');
  }
}
