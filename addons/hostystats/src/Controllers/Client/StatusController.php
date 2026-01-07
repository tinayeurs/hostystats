<?php

namespace App\Addons\HostyStats\Controllers\Client;

use App\Addons\HostyStats\Models\Category;
use App\Addons\HostyStats\Models\Monitor;
use App\Addons\HostyStats\Models\Check;
use App\Addons\HostyStats\Models\MaintenanceMessage;
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class StatusController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $since = $now->copy()->subHour();

        $activeMessage = null;
        $affectedMonitorIds = [];

        $message = MaintenanceMessage::query()
            ->with('monitors:id')
            ->first();

        if ($message && $message->show_on_client && $message->isCurrentlyActive()) {
            $activeMessage = $message;
            
            $affectedMonitorIds = $message->monitors->pluck('id')->all();
        }

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $monitors = Monitor::query()
            ->where('is_active', true)
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        
        $monitorsByCategory = $monitors->groupBy('category_id');

        $slotsByMonitor = [];

        if ($monitors->isNotEmpty()) {
            $checks = Check::query()
                ->whereIn('monitor_id', $monitors->pluck('id'))
                ->where('checked_at', '>=', $since)
                ->orderBy('checked_at', 'asc')
                ->get(['monitor_id', 'status', 'checked_at']);

            
            $byMonitorMinute = [];
            foreach ($checks as $c) {
                $minuteKey = $c->checked_at->format('Y-m-d H:i');
                $byMonitorMinute[$c->monitor_id][$minuteKey] = $c->status;
            }

            
            foreach ($monitors as $m) {
                $lastKnown = $m->forced_status ?: ($m->last_status ?: 'down');
                $cursor = $now->copy()->subMinutes(59)->startOfMinute();

                $slots = [];
                for ($i = 0; $i < 60; $i++) {
                    $key = $cursor->format('Y-m-d H:i');

                    if (isset($byMonitorMinute[$m->id][$key])) {
                        $lastKnown = $byMonitorMinute[$m->id][$key] ?: $lastKnown;
                    }

                    $slots[] = [
                        'minute' => $cursor->copy(),
                        'status' => $lastKnown,
                    ];

                    $cursor->addMinute();
                }

                $slotsByMonitor[$m->id] = $slots;
            }
        }

        return view('hostystats::default.status', [
            'categories' => $categories,
            'monitorsByCategory' => $monitorsByCategory,
            'slotsByMonitor' => $slotsByMonitor,

            
            'activeMessage' => $activeMessage,
            'affectedMonitorIds' => $affectedMonitorIds,
        ]);
    }
}
