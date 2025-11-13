<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \App\Models\Branch|null $branch
 * @property-read \App\Models\Employee|null $employee
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportManager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportManager query()
 *
 * @mixin \Eloquent
 */
class SupportManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'branch_id',
        'telegram_user_id',
        'telegram_chat_id',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'telegram_user_id' => 'integer',
        'telegram_chat_id' => 'integer',
    ];

    /**
     * Зв'язок з основним записом співробітника.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Зв'язок з філією.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
