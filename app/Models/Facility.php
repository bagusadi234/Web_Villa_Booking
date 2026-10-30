<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Facility extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function villaTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            VillaType::class,
            'villa_type_facility'
        )->withTimestamps();
    }
}