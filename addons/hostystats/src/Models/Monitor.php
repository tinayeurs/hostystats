<?php

namespace App\Addons\HostyStats\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Monitor extends Model
{
    protected $table = 'hostystats_monitors';

    protected $fillable = [
        'category_id',
        'name','description',
        'type','target',
        'expected_http_code',
        'degraded_threshold_ms',
        'timeout_ms',
        'interval_sec',
        'is_active',
        'position',
        'forced_status',
        'last_status',
        'last_response_time_ms',
        'last_http_code',
        'last_error',
        'last_checked_at',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'last_checked_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function checks(): HasMany
    {
        return $this->hasMany(Check::class, 'monitor_id');
    }
}
