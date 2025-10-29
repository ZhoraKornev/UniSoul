<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property array<array-key, mixed> $text
 * @property string|null $callback_data
 * @property int $order
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BotButton> $children
 * @property-read int|null $children_count
 * @property-read BotButton|null $parent
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereActive($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|static where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|static whereNull($column)
 * @method static \Illuminate\Database\Eloquent\Builder|static first($columns = ['*'])
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class BotButton extends Model
{
    use HasFactory;
    use HasTranslations;

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

    public function children(): HasMany
    {
        // To resolve PHPStan's difficulty in tracing the return type after orderBy(),
        // we explicitly create the HasMany relation object and apply the ordering.
        // This slight separation helps static analysis confirm the HasMany type.
        $relation = $this->hasMany(self::class, 'parent_id');

        /** @phpstan-ignore-next-line  */
        return $relation->orderBy('order');
    }

    /**
     * Get the parent button.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
