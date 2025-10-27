<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_config_id
 * @property int $telegram_user_id
 * @property string|null $username
 * @property string|null $message
 * @property array<array-key, mixed>|null $raw_update
 * @property bool $is_bot_reply
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UserConfig $userConfig
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereIsBotReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereRawUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereTelegramUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereUserConfigId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMessage whereUsername($value)
 * @mixin \Eloquent
 */
class UserMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_config_id',
        'telegram_user_id',
        'username',
        'message',
        'raw_update',
        'is_bot_reply',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'raw_update' => 'array',
        'is_bot_reply' => 'boolean',
    ];

    /**
     * Get the user config that owns the message.
     */
    public function userConfig(): BelongsTo
    {
        return $this->belongsTo(UserConfig::class, 'user_config_id');
    }
}

