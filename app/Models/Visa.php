<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visa extends Model
{
  use HasFactory;

  protected $table = 'visas';

  protected $fillable = [
    'user_id',
    'visa_status',
    'date_from',
    'date_to',
    'number',
    'created_by',
  ];

  protected $casts = [
    'id' => 'integer',
    'user_id' => 'integer',
    'date_from' => 'datetime',
    'date_to' => 'datetime',
    'created_by' => 'integer',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }
}
