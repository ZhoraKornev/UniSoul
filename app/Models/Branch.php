<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string $phone
 * @property-read \App\Models\Confession|null $confession
 * @property-read mixed $translations
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branch whereLocales(string $column, array $locales)
 *
 * @mixin \Eloquent
 */
class Branch extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['name', 'address', 'description'];

    protected $fillable = [
        'confession_id',
        'name',
        'address',
        'description',
        'phone',
        'email',
        'schedule',
        'latitude',
        'longitude',
        'active',
    ];

    protected $casts = [
        'schedule' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'active' => 'boolean',
    ];

    public function confession(): BelongsTo
    {
        return $this->belongsTo(Confession::class);
    }
}
