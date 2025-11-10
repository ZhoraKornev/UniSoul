<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property int $branch_id
 * @property array<array-key, mixed> $name
 * @property array<array-key, mixed> $position
 * @property string $sex
 * @property int $age
 * @property bool $is_available
 * @property bool $active
 * @property string|null $phone
 * @property string|null $telegram_nickname
 * @property array<array-key, mixed> $address
 * @property array<array-key, mixed>|null $other_info
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Branch $branch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Employee> $colleagues
 * @property-read int|null $colleagues_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereOtherInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereTelegramNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereUpdatedAt($value)
 * @mixin \Eloquent
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
