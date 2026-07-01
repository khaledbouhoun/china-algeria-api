<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageItem extends Model
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
        return $this->belongsTo(PackageItemStep::class, "current_step_id");
    }

    public function steps(): HasMany
    {
        return $this->hasMany(PackageItemStep::class, "package_item_id")->orderByDesc("created_at");
    }

}
