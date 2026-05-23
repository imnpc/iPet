<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableModel;

class PetSpecies extends Model
{
    use HasFactory;
    use HasTranslateableModel;

    protected static ?string $translateablePackageKey = '';

    protected $fillable = [
        'name',
        'icon',
        'sort_order',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, 'pet_species_id');
    }
}
