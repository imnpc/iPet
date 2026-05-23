<?php

namespace App\Models;

use App\Traits\DateTrait;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Interfaces\WalletFloat;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Traits\HasWalletFloat;
use Bavix\Wallet\Traits\HasWallets;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Cog\Laravel\Ban\Traits\Bannable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableModel;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediableInterface;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;
use Spatie\Tags\HasTags;

class User extends Authenticatable implements BannableContract, HasPasskeys, MediableInterface, Wallet, WalletFloat
{
    use Bannable; // 封禁
    use DateTrait; // 日期重写
    use HasApiTokens;
    use HasFactory, Notifiable;
    use HasTags; // 标签
    use HasTranslateableModel; // 翻译
    use HasWalletFloat; // 钱包
    use HasWallet, HasWallets; // 钱包
    use InteractsWithPasskeys; // Passkeys 认证
    use LogsActivity; // 记录日志
    use Mediable; // 媒体库
    use SoftDeletes;

    public function getPasskeyId(): string
    {
        return (string) $this->id;
    }

    protected static ?string $translateablePackageKey = ''; // 翻译

    /**
     * 日志
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'status',
        'parent_id',
        'avatar',
        'nickname',
        'bio',
        'gender',
        'birthday',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'tags',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'birthday' => 'date',
            'app_authentication_secret' => 'encrypted',
            'app_authentication_recovery_codes' => 'encrypted:array',
        ];
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    public function defaultPet(): HasOne
    {
        return $this->hasOne(Pet::class)->where('is_default', true);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function follows(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function followers(): HasMany
    {
        return $this->hasMany(Follow::class, 'following_id');
    }

    public function following(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function routeNotificationForEasySms($notification): string
    {
        return $this->mobile;
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'avatar_url',
    ];

    public function getAvatarUrlAttribute(): string
    {
        if ($this->hasMedia('avatar')) {
            $media = $this->firstMedia('avatar');

            return $media->getUrl();
        }

        return '';
    }

    /**
     * 获取上级
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * 获取下级
     */
    public function children(): HasMany
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /**
     * 关联用户钱包日志
     */
    public function userWalletLog(): HasMany
    {
        return $this->hasMany(UserWalletLog::class);
    }

    /**
     * 判断当前管理员是否允许模拟其他用户登录。
     * 默认允许所有后台用户使用模拟功能，可根据业务需求加入角色/权限校验。
     */
    public function canImpersonate(): bool
    {
        return true;
    }
}
