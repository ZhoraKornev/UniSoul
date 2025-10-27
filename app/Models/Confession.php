<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @property-read mixed $countries
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Confession whereLocales(string $column, array $locales)
 * @mixin \Eloquent
 */
class Confession extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'full_name',
        'description',
        'emoji',
        'country_ids',
        'active',
    ];

    /**
     * Поля, які будуть перекладатися.
     */
    public array $translatable = [
        'name',
        'full_name',
        'description'
    ];

    /**
     * Кастування для JSON полів.
     */
    protected $casts = [
        'country_ids' => 'array',
        'active' => 'boolean',
    ];

    /**
     * Отримати країни, пов'язані з цією конфесією.
     * Хоча country_ids зберігається як масив, ми можемо створити метод доступу для зручності.
     */
    public function getCountriesAttribute(): Collection
    {
        return Country::whereIn('id', $this->country_ids)->get();
    }
}
