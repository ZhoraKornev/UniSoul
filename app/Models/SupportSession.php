<?php

namespace App\Models;

use App\Enums\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $branch_id
 * @property int|null $user_id
 * @property int|null $manager_id
 * @property string|null $user_chat_id
 * @property string|null $manager_chat_id
 * @property \App\Enums\State $status
 * @property string|null $mode
 * @property string|null $ai_thread_id
 * @property \Illuminate\Support\Carbon|null $ai_handoff_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Employee|null $manager
 * @property-read \App\Models\SupportManager|null $supportManagerProfile
 *
 * @method static Builder|SupportSession active()
 * @method static Builder|SupportSession whereId(mixed $value)
 * @method static Builder|SupportSession whereManagerId(mixed $value)
 * @method static Builder|SupportSession whereUserId(mixed $value)
 * @method static Builder|SupportSession whereBranchId(mixed $value)
 * @method static Builder|SupportSession whereStatus(\App\Enums\State|string|int $value)
 * @method static Builder|SupportSession whereMode(mixed $value)
 * @method static Builder|SupportSession whereAiThreadId(mixed $value)
 * @method static Builder|SupportSession whereAiHandoffAt(mixed $value)
 * @method static Builder|SupportSession newModelQuery()
 * @method static Builder|SupportSession newQuery()
 * @method static Builder|SupportSession query()
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class SupportSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'manager_id',
        'user_chat_id',
        'manager_chat_id',
        'status',
        'mode',
        'ai_thread_id',
        'ai_handoff_at',
    ];

    protected $casts = [
        'ai_handoff_at' => 'datetime',
        'manager_id' => 'integer',
        // MODIFIED: Cast status to the integer-backed State Enum
        'status' => State::class,
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function supportManagerProfile(): BelongsTo
    {
        return $this->belongsTo(SupportManager::class, 'manager_id', 'employee_id');
    }

    /**
     * Scope a query to include only sessions in an active state (Ready or ActiveConversation).
     */
    public function scopeActive(Builder $query): \Illuminate\Database\Query\Builder
    {
        // An active session is one where the status is State::Ready (10) or State::ActiveConversation (11),
        // which corresponds to the 1x group in the Enum.
        return $query->whereIn('status', [State::Ready->value, State::ActiveConversation->value]);
    }
}
