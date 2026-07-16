<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
  use HasFactory;

  protected $table = 'roles';

  protected $fillable = [
    'code',
    'name',
  ];

  protected $casts = [
    'id' => 'integer',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  public function users(): HasMany
  {
    return $this->hasMany(User::class, 'role_id');
  }

  public function wallets(): HasMany
  {
    return $this->hasMany(Wallet::class, 'role_id');
  }
}
