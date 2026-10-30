<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingUnitNight extends Model
{
    protected $fillable = [
        'booking_unit_id',
        'stay_date',
        'nightly_price',
        'price_type',
    ];

    protected function casts(): array
    {
        return [
            'stay_date' => 'date',
            'nightly_price' => 'integer',
        ];
    }

    public function bookingUnit(): BelongsTo
    {
        return $this->belongsTo(BookingUnit::class);
    }
}
