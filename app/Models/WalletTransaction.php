<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
  use HasFactory;

  protected $table = 'wallet_transactions';

  protected $fillable = [
    'wallet_id',
    'direction',
    'amount',
    'user_id',
    'balance_before',
    'balance_after',
    'created_by',
    'comment',
    'status',
  ];

  protected $casts = [
    'id' => 'integer',
    'wallet_id' => 'integer',
    'direction' => 'integer',
    'amount' => 'decimal:2',
    'user_id' => 'integer',
    'balance_before' => 'decimal:2',
    'balance_after' => 'decimal:2',
    'created_by' => 'integer',
    'created_at' => 'datetime',
  ];

  public function wallet(): BelongsTo
  {
    return $this->belongsTo(Wallet::class, 'wallet_id');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }
}
