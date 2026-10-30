<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingUnit extends Model
{
    protected $fillable = [
        'booking_id',
        'villa_unit_id',
        'guest_count',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'guest_count' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function villaUnit(): BelongsTo
    {
        return $this->belongsTo(VillaUnit::class);
    }

    public function nights(): HasMany
    {
        return $this->hasMany(BookingUnitNight::class);
    }
}
