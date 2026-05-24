<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableModel;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Tags\HasTags;

class Post extends Model
{
    use HasFactory;
    use HasTags;
    use HasTranslateableModel; // 翻译
    use LogsActivity;
    use SoftDeletes;

    protected static ?string $translateablePackageKey = ''; // 翻译

    protected $fillable = [
        'user_id',
        'pet_id',
        'content',
        'location',
        'visibility',
        'is_pinned',
        'allow_comment',
        'published_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'allow_comment' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeOrderByPublishedAtDesc(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        return $query->where(function (Builder $query) use ($user): void {
            $query->where('visibility', 'public');

            if ($user === null) {
                return;
            }

            $query->orWhere('user_id', $user->id)
                ->orWhere(function (Builder $query) use ($user): void {
                    $query->where('visibility', 'followers')
                        ->whereExists(function ($followQuery) use ($user): void {
                            $followQuery->selectRaw('1')
                                ->from('follows')
                                ->whereColumn('follows.following_id', 'posts.user_id')
                                ->where('follows.follower_id', $user->id);
                        });
                });
        });
    }

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

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
