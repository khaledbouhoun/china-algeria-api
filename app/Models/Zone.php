<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
  use HasFactory;


  public const EVERYWHERE = 1;
  public const ZONE_A = 2;
  public const ZONE_B = 3;
  public const ZONE_C = 4;

  protected $table = 'zones';

  protected $fillable = [
    'name',
    'zone_type',
    'description',
  ];

  protected $casts = [
    'id' => 'integer',
  ];

  public $timestamps = false;

  public function users(): HasMany
  {
    return $this->hasMany(User::class, 'zone_id');
  }

  public function orderItemSteps(): HasMany
  {
    return $this->hasMany(OrderItemStep::class, 'zone_id');
  }

  public function packageItemSteps(): HasMany
  {
    return $this->hasMany(PackageItemStep::class, 'zone_id');
  }

  public function packageSteps(): HasMany
  {
    return $this->hasMany(PackageStep::class, 'zone_id');
  }
}
