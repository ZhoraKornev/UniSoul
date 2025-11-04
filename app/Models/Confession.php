<?php

namespace App\Models;

use App\Enums\BotCallback;
use App\Enums\ConfessionSubActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Webpatser\Countries\Countries;

/**
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property array<array-key, mixed> $full_name
 * @property array<array-key, mixed> $description
 * @property string $emoji
 * @property array<array-key, mixed> $country_ids
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $available_action_enums
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereAvailableActions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereCountryIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereEmoji($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereUpdatedAt($value)
 * @mixin \Illuminate\Database\Eloquent\Builder
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
