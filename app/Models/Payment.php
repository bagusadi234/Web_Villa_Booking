<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'transaction_type',
        'payment_method',
        'amount',
        'status',
        'external_transaction_id',
        'related_payment_id',
        'paid_at',
        'gateway_response',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_at' => 'datetime',
            'gateway_response' => 'array',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function relatedPayment(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'related_payment_id'
        );
    }

    public function relatedPayments(): HasMany
    {
        return $this->hasMany(
            self::class,
            'related_payment_id'
        );
    }
}
