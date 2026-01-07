<?php

namespace App\Addons\HostyStats\Controllers;

use Illuminate\Routing\Controller;
use App\Addons\HostyStats\Models\Monitor;
use App\Addons\HostyStats\Models\MaintenanceMessage;

class DashboardController extends Controller
{
    public function index()
    {
        $monitors = Monitor::query()
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $activeMessage = MaintenanceMessage::query()
            ->where('is_active', true)
            ->orderByDesc('id')
            ->with('monitors:id')
            ->first();

        $affectedMonitorIds = $activeMessage
            ? $activeMessage->monitors->pluck('id')->map(fn ($v) => (int) $v)->all()
            : [];

        $isMonitorAffected = function (int $monitorId) use ($activeMessage, $affectedMonitorIds): bool {
            if (!$activeMessage || !$activeMessage->is_active) {
                return false;
            }

            if (empty($affectedMonitorIds)) {
                return true;
            }

            return in_array($monitorId, $affectedMonitorIds, true);
        };

        $effectiveStatus = function (Monitor $m) use ($isMonitorAffected): string {
            if ($isMonitorAffected((int) $m->id)) {
                return 'maintenance';
            }

            if (!empty($m->forced_status)) {
                return $m->forced_status;
            }

            return $m->last_status ?: 'down';
        };

        $down = 0;
        $maintenance = 0;
        $degraded = 0;
        $ok = 0;

        foreach ($monitors as $m) {
            $s = $effectiveStatus($m);

            if ($s === 'maintenance') {
                $maintenance++;
                continue;
            }

            if ($s === 'down') {
                $down++;
                continue;
            }

            if ($s === 'degraded') {
                $degraded++;
                continue;
            }

            $ok++;
        }

        $up = $ok + $degraded;

        return view('hostystats::admin.dashboard', [
            'monitors' => $monitors,
            'activeMessage' => $activeMessage,
            'affectedMonitorIds' => $affectedMonitorIds,
            'kpis' => [
                'down' => $down,
                'up' => $up,
                'degraded' => $degraded,
                'maintenance' => $maintenance,
                'total' => $monitors->count(),
            ],
        ]);
    }
}
