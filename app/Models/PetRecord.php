<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableModel;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PetRecord extends Model
{
    use HasFactory;
    use HasTranslateableModel; // 翻译
    use LogsActivity;
    use SoftDeletes;

    protected static ?string $translateablePackageKey = ''; // 翻译

    protected $fillable = [
        'pet_id',
        'type',
        'title',
        'visit_date',
        'next_visit_date',
        'hospital_name',
        'vet_name',
        'hospital_phone',
        'weight',
        'temperature',
        'symptoms',
        'diagnosis',
        'treatment',
        'prescription',
        'notes',
        'cost',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'next_visit_date' => 'date',
        'weight' => 'decimal:2',
        'temperature' => 'decimal:1',
        'cost' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
