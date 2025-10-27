<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property int|null $parent_id Links to the parent button ID for nested menus. Null for top-level menu buttons.
 * @property array<array-key, mixed> $text The text displayed on the button in Telegram (e.g., "Male", "Settings").
 * @property string $callback_data The unique string sent back to the bot when the button is pressed (e.g., "GENDER_MALE").
 * @property int $order The display order of the button within its row or parent menu.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BotButton> $children
 * @property-read int|null $children_count
 * @property-read BotButton|null $parent
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereCallbackData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BotButton extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['text'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id', // Null for top-level menus
        'text',
        'callback_data', // Unique identifier for handler
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'parent_id' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the child buttons for the menu.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get the parent button.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
