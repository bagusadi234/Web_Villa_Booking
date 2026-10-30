<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VillaType extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'slug',
        'description',
        'bedrooms',
        'bathrooms',
        'standard_capacity',
        'max_capacity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'standard_capacity' => 'integer',
            'max_capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(VillaUnit::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(VillaImage::class);
    }

    public function pricingRules(): HasMany
    {
        return $this->hasMany(PricingRule::class);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(
            Facility::class,
            'villa_type_facility'
        )->withTimestamps();
    }
}