<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
  use HasFactory;

  protected $table = 'wallets';

  protected $fillable = [
    'user_id',
    'role_id',
    'balance',
  ];

  protected $casts = [
    'id' => 'integer',
    'user_id' => 'integer',
    'role_id' => 'integer',
    'balance' => 'decimal:2',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function role(): BelongsTo
  {
    return $this->belongsTo(Role::class, 'role_id');
  }

  public function transactions(): HasMany
  {
    return $this->hasMany(WalletTransaction::class, 'wallet_id');
  }
}
