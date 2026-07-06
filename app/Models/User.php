<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
  use HasFactory, Notifiable, SoftDeletes;

  // -------------------------------------------------------------------------
  // Status constants — single source of truth for valid status strings.
  // -------------------------------------------------------------------------

  public const STATUS_PENDING = 'PENDING';
  public const STATUS_ENABLED = 'ENABLED';
  public const STATUS_DISABLED = 'DISABLED';
  public const STATUS_REJECTED = 'REJECTED';
  public const STATUS_DELETED = 'DELETED';

  /** @var list<string> */
  public const STATUSES = [
    self::STATUS_PENDING,
    self::STATUS_ENABLED,
    self::STATUS_DISABLED,
    self::STATUS_REJECTED,
    self::STATUS_DELETED,
  ];

  // -------------------------------------------------------------------------
  // Role ID constants — mirrors the roles table.
  // -------------------------------------------------------------------------

  public const ROLE_ADMIN = 1;
  public const ROLE_CLIENT = 2;
  public const ROLE_CASHIER = 3;
  public const ROLE_AGENT_A = 4;
  public const ROLE_AGENT_C = 5;
  public const ROLE_RESPONSABLE_A = 6;
  public const ROLE_RESPONSABLE_C = 7;
  public const ROLE_GLADIATOR = 8;
  public const ROLE_DELIVERY = 9;
  public const ROLE_VERIFIER = 10;

  // -------------------------------------------------------------------------
  // Eloquent configuration
  // -------------------------------------------------------------------------

  protected $fillable = [
    'public_code',
    'full_name',
    'email',
    'phone',
    'address',
    'firebase_uid',
    'role_id',
    'zone_id',
    'status',
    'verified_at',
    'last_login_at',
  ];

  protected $hidden = [
    'firebase_uid',
  ];

  protected $casts = [
    'email_verified_at' => 'datetime',
    'last_login_at' => 'datetime',
    // status is stored as a string enum — do NOT cast to integer.
    'status' => 'string',
  ];

  // -------------------------------------------------------------------------
  // Relationships
  // -------------------------------------------------------------------------

  public function role(): BelongsTo
  {
    return $this->belongsTo(Role::class, 'role_id');
  }

  public function zone(): BelongsTo
  {
    return $this->belongsTo(Zone::class, 'zone_id');
  }

  public function userSessions(): HasMany
  {
    return $this->hasMany(UserSession::class, 'user_id');
  }

  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  public function isEnabled(): bool
  {
    return $this->status === self::STATUS_ENABLED;
  }

  public function isPending(): bool
  {
    return $this->status === self::STATUS_PENDING;
  }

  
}
