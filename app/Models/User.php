<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
  use HasFactory, Notifiable, SoftDeletes;

  protected $fillable = [
    "public_code",
    "full_name",
    "email",
    "phone",
    "address",
    "google_uid",
    "role_id",
    "zone_id",
    "status",
    "email_verified_at",
    "last_login_at",
  ];

  protected $casts = [
    'email_verified_at' => 'datetime',
    'last_login_at' => 'datetime',
    'status' => 'integer',
  ];

  public function role(): BelongsTo
  {
    return $this->belongsTo(Role::class, 'role_id');
  }

  public function zone(): BelongsTo
  {
    return $this->belongsTo(Zone::class, 'zone_id');
  }
}
