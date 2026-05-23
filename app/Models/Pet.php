<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableModel;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Tags\HasTags;

class Pet extends Model
{
    use HasFactory;
    use HasTags;
    use HasTranslateableModel; // 翻译
    use LogsActivity;
    use SoftDeletes;

    protected static ?string $translateablePackageKey = ''; // 翻译

    protected $fillable = [
        'user_id',
        'name',
        'species',
        'breed',
        'gender',
        'birthday',
        'adoption_date',
        'avatar',
        'metadata',
        'is_default',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'birthday' => 'date',
        'adoption_date' => 'date',
        'metadata' => 'array',
        'is_default' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(PetRecord::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
