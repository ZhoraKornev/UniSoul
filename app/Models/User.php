<?php

namespace App\Models;

use App\Enums\State; // Using the updated Enum name
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string|null $name Full name for Filament/Admin users, nullable for Telegram users.
 * @property string|null $email Email address for Filament/Admin login. Unique check is handled by application logic to allow multiple NULLs for Telegram users.
 * @property \Illuminate\Support\Carbon|null $email_verified_at Timestamp when the email was verified.
 * @property string|null $password Hashed password for Filament/Admin login.
 * @property string|null $remember_token Remember me token for web sessions.
 * @property int|null $telegram_user_id Unique user ID provided by Telegram. Uniqueness should be enforced by application logic to allow multiple NULLs for Admin users.
 * @property string|null $first_name First name provided by Telegram.
 * @property string|null $last_name Last name provided by Telegram.
 * @property string|null $username Username provided by Telegram (e.g., @user_name).
 * @property State $current_state Integer status code for the bot conversation flow. Uses grouped statuses: 1x (Active), 2x (Non-Active), 3x (Flow). Default: 33
 * @property \Illuminate\Database\Eloquent\Casts\ArrayObject<array-key, mixed>|null $configuration JSON field to store user-specific configuration data (settings, preferences).
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereConfiguration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelegramUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Telegram User Fields
        'telegram_user_id',
        'first_name',
        'last_name',
        'username',
        'current_state',
        'configuration',

        // Standard Admin/Laravel Fields (used by Filament)
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * 'current_state' is cast to the State enum, and 'configuration' is cast
     * to AsArrayObject for easy access.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'configuration' => AsArrayObject::class,
        'current_state' => State::class, // Casting to the integer-backed Enum
    ];
}
