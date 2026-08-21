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

  /*
 |--------------------------------------------------------------------------
 | ITEM
 |--------------------------------------------------------------------------
 */

  public const ITEM_CL_CREATED = 1;
  public const ITEM_CL_CANCELED = 2;
  public const ITEM_A_RECEIVED = 3;
  public const ITEM_A_ERROR = 4;
  public const ITEM_CL_FULFILLED = 10;

  /*
  |--------------------------------------------------------------------------
  | PACKAGE ITEM
  |--------------------------------------------------------------------------
  */

  public const PACKAGE_ITEM_PACKAGED = 5;
  public const PACKAGE_ITEM_SHIPPED = 6;
  public const PACKAGE_ITEM_C_RECEIVED = 7;
  public const PACKAGE_ITEM_C_OPENED = 8;
  public const PACKAGE_ITEM_C_VERIFIED = 9;
  public const PACKAGE_ITEM_CL_RECEIVED = 10;
  public const PACKAGE_ITEM_CLIENT_DELIVERED = 10;
  public const PACKAGE_ITEM_CL_CONFIRMED = 11;
  public const PACKAGE_ITEM_CL_ERROR = 12;

  /*
  |--------------------------------------------------------------------------
  | PACKAGE
  |--------------------------------------------------------------------------
  */

  public const PACKAGE_A_CREATED = 13;
  public const PACKAGE_A_CANCELED = 14;
  public const PACKAGE_B_RECEIVED = 15;
  public const PACKAGE_G_RESERVED = 16;
  public const PACKAGE_G_RECEIVED = 17;
  public const PACKAGE_G_ERROR = 18;
  public const PACKAGE_D_RECEIVED = 19;
  public const PACKAGE_C_RECEIVED = 20;

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
