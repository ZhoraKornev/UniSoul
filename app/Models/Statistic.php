<?php

namespace App\Models;

use App\Support\ExtraDateScopes;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use LaracraftTech\LaravelDateScopes\DateScopes;

/**
 * App\Models\Statistic
 *
 * @property int $id
 * @property int|null $chat_id
 * @property string $action
 * @property array<array-key, mixed>|null $value
 * @property string|null $category
 * @property Carbon $collected_at
 * @property-read \App\Models\Chat|null $chat
 * @method static Builder<static>|Statistic centuryToDate(?string $column = null)
 * @method static Builder<static>|Statistic dayToNow(?string $column = null)
 * @method static Builder<static>|Statistic decadeToDate(?string $column = null)
 * @method static Builder<static>|Statistic hourToNow(?string $column = null)
 * @method static Builder<static>|Statistic millenniumToDate(?string $column = null)
 * @method static Builder<static>|Statistic minuteToNow(?string $column = null)
 * @method static Builder<static>|Statistic monthToDate(?string $column = null)
 * @method static Builder<static>|Statistic newModelQuery()
 * @method static Builder<static>|Statistic newQuery()
 * @method static Builder<static>|Statistic ofJustNow($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast12Hours($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast12Months($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast14Days($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast15Minutes($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast15Seconds($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast18Hours($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast21Days($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast24Hours($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast2Quarters($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast2Weeks($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast30Days($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast30Minutes($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast30Seconds($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast3Months($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast3Quarters($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast3Weeks($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast45Minutes($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast45Seconds($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast4Quarters($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast4Weeks($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast60Minutes($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast60Seconds($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast6Hours($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast6Months($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast7Days($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLast9Months($startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastCenturies(int $centuries, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastCentury($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastDays(int $days, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastDecade($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastDecades(int $decades, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastHour($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastHours(int $hours, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastMillennium($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastMillenniums(int $millennium, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastMinute($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastMinutes(int $minutes, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastMonth($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastMonths(int $months, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastQuarter($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastQuarters(int $quarters, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastSecond($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastSeconds(int $seconds, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastUnit(string $dateUnit, int $value, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastWeek($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastWeeks(int $weeks, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastYear($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofLastYears(int $years, $startFrom = null, ?\LaracraftTech\LaravelDateScopes\DateRange $customRange = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofToday($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Statistic ofYesterday($startFrom = null, ?string $column = null)
 * @method static Builder<static>|Statistic quarterToDate(?string $column = null)
 * @method static Builder<static>|Statistic query()
 * @method static Builder<static>|Statistic secondToNow(?string $column = null)
 * @method static Builder<static>|Statistic weekToDate(?string $column = null)
 * @method static Builder<static>|Statistic whereAction($value)
 * @method static Builder<static>|Statistic whereCategory($value)
 * @method static Builder<static>|Statistic whereChatId($value)
 * @method static Builder<static>|Statistic whereCollectedAt($value)
 * @method static Builder<static>|Statistic whereId($value)
 * @method static Builder<static>|Statistic whereValue($value)
 * @method static Builder<static>|Statistic yearToDate(?string $column = null)
 * @mixin Eloquent
 */
class Statistic extends Model
{
    use DateScopes;
    public $timestamps = false;
    protected static $unguarded = true;

    protected function casts(): array
    {
        return [
            'value' => 'array',
            'collected_at' => 'datetime',
        ];
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chat_id', 'chat_id');
    }

    public static function getStatsForBot(): array
    {
        $date = now();

        $diagramsToday = self::query()
            ->where('category', 'diagram')
            ->whereDate('collected_at', $date->toDateString())
            ->count();

        $diagramsTotal = self::query()
            ->where('category', 'diagram')
            ->count();

        $usersNewToday = Chat::query()
            ->whereDate('created_at', $date->toDateString())
            ->count();

        $usersActiveToday = self::query()
            ->distinct()
            ->whereDate('collected_at', $date->toDateString())
            ->whereNotNull('chat_id')
            ->count('chat_id');

        $usersTotal = Chat::count();

        return [
            'diagramsToday' => number_format($diagramsToday, thousands_separator: '˙'),
            'diagramsTotal' => number_format($diagramsTotal, thousands_separator: '˙'),

            'usersNewToday' => number_format($usersNewToday, thousands_separator: '˙'),
            'usersActiveToday' => number_format($usersActiveToday, thousands_separator: '˙'),
            'usersTotal' => number_format($usersTotal, thousands_separator: '˙'),
            'lastUpdate' => now()->format('Y-m-d H:i:s e'),
        ];
    }
}
