<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageStep extends Model
{
  use HasFactory;

  protected $table = 'package_steps';

  public const UPDATED_AT = null;


  protected $fillable = [
    'package_id',
    'status_id',
    'zone_id',
    'user_id',
    'comment',
  ];

  protected $casts = [
    'id' => 'integer',
    'package_id' => 'integer',
    'status_id' => 'integer',
    'zone_id' => 'integer',
    'user_id' => 'integer',
    'created_at' => 'datetime',
  ];

  public function package(): BelongsTo
  {
    return $this->belongsTo(Package::class, 'package_id');
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
}
