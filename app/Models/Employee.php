<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property int|null $branch_id
 * @property array $name
 * @property array|null $position
 * @property string|null $sex
 * @property int|null $age
 * @property bool $is_available
 * @property bool $active
 * @property string|null $phone
 * @property string|null $telegram_nickname
 * @property array|null $address
 * @property array|null $other_info
 * @property-read \App\Models\Branch|null $branch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Employee> $colleagues
 * @property-read int|null $colleagues_count
 * @property-read mixed $translations
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereId(mixed $value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereBranchId(mixed $value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee wherePhone(mixed $value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereLocales(string $column, array $locales)
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Employee extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['name', 'position', 'address'];

    protected $fillable = [
        'branch_id',
        'name',
        'position',
        'sex',
        'age',
        'is_available',
        'active',
        'phone',
        'telegram_nickname',
        'address',
        'other_info',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'active' => 'boolean',
        'other_info' => 'array',
        'age' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function colleagues(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_colleagues', 'employee_id', 'colleague_id');
    }
}
