<?php

namespace App\Models;

use App\Enums\BotCallback;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Translatable\HasTranslations;
use Webpatser\Countries\Countries;

/**
 * @property int $id
 * @property string $emoji
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BotButton> $botButtons
 * @property-read int|null $bot_buttons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Branch> $branches
 * @property-read int|null $branches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Countries> $countries
 * @property-read int|null $countries_count
 * @property-read array $available_action_enums
 * @property-read mixed $translations
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession where(\Closure|string|array|\Illuminate\Database\Query\Expression $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereId(mixed $value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereLocales(string $column, array $locales)
 *
 * @mixin \Eloquent
 */
class Confession extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'name',
        'full_name',
        'description',
        'emoji',
        'active',
    ];

    public array $translatable = [
        'name',
        'full_name',
        'description',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(
            Countries::class,
            'confession_country',
            'confession_id',
            'country_iso_3166_2',
            'id',
            'iso_3166_2'
        );
    }

    /**
     * Get all BotCallback enum actions available for this confession
     */
    public function getAvailableActionEnumsAttribute(): array
    {
        return BotButton::query()
            ->where('entity_type', self::class)
            ->where('entity_id', $this->id)
            ->whereNotNull('callback_data')
            ->pluck('callback_data')
            ->map(fn ($value) => BotCallback::tryFrom($value))
            ->filter()
            ->values()
            ->toArray();
    }

    public function botButtons(): MorphMany
    {
        return $this->morphMany(BotButton::class, 'entity');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
