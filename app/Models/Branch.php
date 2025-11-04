<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

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