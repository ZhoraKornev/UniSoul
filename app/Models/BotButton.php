<?php

namespace App\Models;

use App\Enums\BotCallback;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property bool $need_donations
 * @property BotCallback $callback_data
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BotButton> $children
 * @property-read int|null $children_count
 * @property-read Model|\Eloquent $entity
 * @property-read BotButton|null $parent
 * @property-read mixed $translations
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton where(\Closure|string|array|\Illuminate\Database\Query\Expression $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereCallbackData(mixed $value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereNull(\Closure|string|array|\Illuminate\Database\Query\Expression $columns, string $boolean = 'and', bool $not = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BotButton whereLocales(string $column, array $locales)
 *
 * @mixin \Eloquent
 */
class BotButton extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['text'];

    protected $fillable = [
        'parent_id',
        'text',
        'callback_data',
        'order',
        'entity_type',
        'entity_id',
        'need_donations',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'order' => 'integer',
        'callback_data' => BotCallback::class,
        'text' => 'array',
        'need_donations' => 'boolean',
    ];

    // Polymorphic binding
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get enum object from callback_data
     */
    public function callbackEnum(): BotCallback
    {
        return $this->callback_data;
    }

    /**
     * Resolve translated label dynamically
     */
    public function label(): string
    {
        // If text translation exists, use it
        if (! empty($this->text)) {
            return $this->getTranslation('text', app()->getLocale());
        }

        // Otherwise return enum label
        return $this->callbackEnum()->label();
    }
}
