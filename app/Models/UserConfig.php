<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $telegram_user_id
 * @property string|null $username
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $language
 * @property bool $notifications_enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserMessage> $messages
 * @property-read int|null $messages_count
 * @method static \Database\Factories\UserConfigFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereNotificationsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereTelegramUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereUsername($value)
 * @mixin \Eloquent
 */
class UserConfig extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'telegram_user_id',
        'username',
        'first_name',
        'last_name',
        'language',
        'notifications_enabled',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'notifications_enabled' => 'boolean',
    ];

    /**
     * Get the messages for the user.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(UserMessage::class, 'user_config_id');
    }
}

