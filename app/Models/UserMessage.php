<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

