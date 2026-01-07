<?php

namespace App\Addons\HostyStats\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Check extends Model
{
    protected $table = 'hostystats_checks';

    protected $fillable = [
        'monitor_id','status','response_time_ms','http_code','error','checked_at'
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class, 'monitor_id');
    }
}
