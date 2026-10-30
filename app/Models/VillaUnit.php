<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VillaUnit extends Model
{
    protected $fillable = [
        'villa_type_id',
        'unit_code',
        'unit_name',
        'condition_status',
        'notes',
    ];

    public function villaType(): BelongsTo
    {
        return $this->belongsTo(VillaType::class);
    }

    public function housekeepingTasks(): HasMany
    {
        return $this->hasMany(HousekeepingTask::class);
    }

    public function unitBlocks(): HasMany
    {
        return $this->hasMany(UnitBlock::class);
    }

    public function bookingUnits(): HasMany
    {
        return $this->hasMany(BookingUnit::class);
    }
}
