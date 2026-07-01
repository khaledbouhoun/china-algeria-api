<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    /**
     * @property int $id
     */

    protected $fillable = [
    ];

    protected $casts = [];

    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(PackageStep::class, "current_step_id");
    }

    public function steps(): HasMany
    {
        return $this->hasMany(PackageStep::class, "package_id")->orderByDesc("created_at");
    }

}
