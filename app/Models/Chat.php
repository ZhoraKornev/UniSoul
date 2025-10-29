<?php

namespace App\Models;

use App\Enums\Gender;
use Glorand\Model\Settings\Traits\HasSettingsTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use LaracraftTech\LaravelDateScopes\DateScopes;

/**
 * @property int $chat_id
 * @property string $type
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $username
 * @property string|null $language_code
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $blocked_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Glorand\Model\Settings\Models\ModelSettings|null $modelSettings
 * @method static Builder<static>|Chat centuryToDate(?string $column = null)
 * @method static Builder<static>|Chat dayToNow(?string $column = null)
 * @method static Builder<static>|Chat decadeToDate(?string $column = null)
 * @method static Builder<static>|Chat hourToNow(?string $column = null)
 * @method static Builder<static>|Chat millenniumToDate(?string $column = null)
 * @method static Builder<static>|Chat minuteToNow(?string $column = null)
 * @method static Builder<static>|Chat monthToDate(?string $column = null)
 * @method static Builder<static>|Chat newModelQuery()
 * @method static Builder<static>|Chat newQuery()
 * @method static Builder<static>|Chat ofJustNow($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast12Hours($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast12Months($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast14Days($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast15Minutes($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast15Seconds($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast18Hours($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast21Days($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast24Hours($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast2Quarters($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast2Weeks($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast30Days($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast30Minutes($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast30Seconds($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast3Months($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast3Quarters($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast3Weeks($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast45Minutes($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast45Seconds($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast4Quarters($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast4Weeks($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast60Minutes($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast60Seconds($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast6Hours($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast6Months($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast7Days($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLast9Months($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastCenturies(int $centuries, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastCentury($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastDays(int $days, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastDecade($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastDecades(int $decades, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastHour($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastHours(int $hours, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastMillennium($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastMillenniums(int $millennium, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastMinute($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastMinutes(int $minutes, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastMonth($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastMonths(int $months, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastQuarter($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastQuarters(int $quarters, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastSecond($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastSeconds(int $seconds, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastUnit(string $dateUnit, int $value, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastWeek($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastWeeks(int $weeks, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastYear($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Chat ofLastYears(int $years, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Chat ofThisYear()
 * @method static Builder<static>|Chat ofToday($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Chat ofYesterday($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Chat quarterToDate(?string $column = null)
 * @method static Builder<static>|Chat query()
 * @method static Builder<static>|Chat secondToNow(?string $column = null)
 * @method static Builder<static>|Chat weekToDate(?string $column = null)
 * @method static Builder<static>|Chat whereBlockedAt($value)
 * @method static Builder<static>|Chat whereChatId($value)
 * @method static Builder<static>|Chat whereCreatedAt($value)
 * @method static Builder<static>|Chat whereFirstName($value)
 * @method static Builder<static>|Chat whereLanguageCode($value)
 * @method static Builder<static>|Chat whereLastName($value)
 * @method static Builder<static>|Chat whereSettings(string $setting, string $operator, $value, ?bool $filterOnMissing = null)
 * @method static Builder<static>|Chat whereStartedAt($value)
 * @method static Builder<static>|Chat whereType($value)
 * @method static Builder<static>|Chat whereUpdatedAt($value)
 * @method static Builder<static>|Chat whereUsername($value)
 * @method static Builder<static>|Chat yearToDate(?string $column = null)
 * @mixin \Eloquent
 */
class Chat extends Model
{
    use HasFactory;
    use HasSettingsTable;
    use DateScopes;

    protected $primaryKey = 'chat_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected static $unguarded = true;

    protected bool $persistSetting = true;

    public $defaultSettings =[
        'gender' => Gender::OTHER->value,
        'language' => 'en',
        'confession' => null,
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'blocked_at' => 'datetime',
        ];
    }


    public function getSettingsRules(): array
    {
        return [
            'language' => 'string|max:2',
        ];
    }

    public static function findFromUser(?User $user): ?Chat
    {
        if ($user === null) {
            return null;
        }

        $chat = self::find($user->id);

        return $chat ?? null;
    }

    public function scopeWhereSettings(
        Builder $query,
        string $setting,
        string $operator,
                $value,
        bool $filterOnMissing = null
    ): Builder {
        return $query->where(function (Builder $query) use ($value, $operator, $setting, $filterOnMissing) {
            return $query->when(
                $filterOnMissing,
                function (Builder $query) use ($value, $operator, $setting) {
                    return $query
                        ->whereDoesntHave('modelSettings')
                        ->orWhereHas(
                            'modelSettings',
                            fn (Builder $query) => $query->where("settings->$setting", $operator, $value)
                        );
                },
                function (Builder $query) use ($value, $operator, $setting) {
                    $query->whereHas(
                        'modelSettings',
                        fn (Builder $query) => $query->where("settings->$setting", $operator, $value)
                    );
                }

            );
        });
    }

    public function scopeOfThisYear(Builder $query): Builder
    {
        $createdColumnName = self::CREATED_AT !== 'created_at' ? self::CREATED_AT : config('date-scopes.created_column');
        $now = CarbonImmutable::now();

        return $query->whereBetween($createdColumnName, [$now->startOfYear(), $now->endOfYear()]);
    }
}
