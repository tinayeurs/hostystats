<?php

namespace App\Addons\HostyStats\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MaintenanceMessage extends Model
{
    protected $table = 'hostystats_maintenance_messages';

    protected $fillable = [
        'is_active',
        'show_on_client',
        'show_on_admin',
        'severity',
        'title',
        'description',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_client' => 'boolean',
        'show_on_admin' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function monitors(): BelongsToMany
    {
        return $this->belongsToMany(
            Monitor::class,
            'hostystats_maintenance_message_monitor',
            'maintenance_message_id',
            'monitor_id'
        );
    }

    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) return false;

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;

        return true;
    }
}
