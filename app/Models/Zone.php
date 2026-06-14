<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
  use HasFactory;

  protected $fillable = [
    'code',
    'name',
    'zone_type',
    'description',
  ];

  public $timestamps = false;

  /**
   * Get the users in this zone.
   */
  public function users(): HasMany
  {
    return $this->hasMany(User::class);
  }
}
