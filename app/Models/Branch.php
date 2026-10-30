<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'email',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function villaTypes(): HasMany
    {
        return $this->hasMany(VillaType::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(Addon::class);
    }
}
