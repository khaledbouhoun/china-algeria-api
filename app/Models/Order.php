<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @property int $id
     */

    protected $fillable = [
    ];

    protected $casts = [];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, "client_id");
    }

}
