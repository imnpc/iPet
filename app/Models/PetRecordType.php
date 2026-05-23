<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableModel;

class PetRecordType extends Model
{
    use HasFactory;
    use HasTranslateableModel;

    protected static ?string $translateablePackageKey = '';

    protected $fillable = [
        'name',
        'slug',
        'color',
        'sort_order',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function records(): HasMany
    {
        return $this->hasMany(PetRecord::class, 'pet_record_type_id');
    }
}
