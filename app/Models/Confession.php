<?php

namespace App\Models;

use App\Enums\ConfessionSubActions;
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
        'available_actions', // Додано нове поле
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
        'available_actions' => 'array', // Зберігаємо як JSON-масив у БД, PHP перетворює його на звичайний масив
    ];


    /**
     * Отримати об'єкти ConfessionSubActions для доступних дій.
     * Це дозволяє отримати доступ до енумів, а не просто рядків.
     */
    public function getAvailableActionEnumsAttribute(): array
    {
        // Перетворюємо масив рядків (що зберігається в available_actions) на масив об'єктів Enum
        return array_map(function ($actionValue) {
            try {
                // Використовуємо ConfessionSubActions::tryFrom для безпечного перетворення
                return ConfessionSubActions::tryFrom($actionValue);
            } catch (\ValueError $e) {
                // Ігноруємо або логуємо невідомі значення
                return null;
            }
        }, $this->available_actions ?? []);
    }
}

