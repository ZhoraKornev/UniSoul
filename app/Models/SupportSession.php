<?php

namespace App\Models;

use App\Enums\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property State $status
 * @property-read \App\Models\Employee|null $manager
 * @property-read \App\Models\SupportManager|null $supportManagerProfile
 *
 * @method static Builder<static>|SupportSession active()
 * @method static Builder<static>|SupportSession newModelQuery()
 * @method static Builder<static>|SupportSession newQuery()
 * @method static Builder<static>|SupportSession query()
 *
 * @mixin \Eloquent
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
    public function scopeActive(Builder $query): Builder
    {
        // An active session is one where the status is State::Ready (10) or State::ActiveConversation (11),
        // which corresponds to the 1x group in the Enum.
        return $query->whereIn('status', [State::Ready->value, State::ActiveConversation->value]);
    }
}
