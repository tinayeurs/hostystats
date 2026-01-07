<?php

namespace App\Addons\HostyStats\Controllers;

use App\Addons\HostyStats\Models\Category;
use App\Addons\HostyStats\Models\Monitor;
use App\Addons\HostyStats\Models\Check;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MonitorController extends Controller
{
    public function index()
    {
        $monitors = Monitor::query()
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        return view('hostystats::admin.monitors.index', compact('monitors'));
    }

    public function show(Monitor $monitor)
    {
        $monitor->load('category');

        $now = Carbon::now();
        $since = $now->copy()->subHour();

        $checks = Check::query()
            ->where('monitor_id', $monitor->id)
            ->where('checked_at', '>=', $since)
            ->orderBy('checked_at', 'asc')
            ->get(['status', 'checked_at']);

        $lastKnown = $monitor->forced_status ?: ($monitor->last_status ?: 'down');

        $byMinute = $checks->groupBy(fn ($c) => $c->checked_at->format('Y-m-d H:i'));

        $slots = [];
        $cursor = $now->copy()->subMinutes(59)->startOfMinute();

        for ($i = 0; $i < 60; $i++) {
            $key = $cursor->format('Y-m-d H:i');
            $entry = $byMinute->get($key)?->last();

            if ($entry) {
                $lastKnown = $entry->status ?: $lastKnown;
            }

            $slots[] = [
                'minute' => $cursor->copy(),
                'status' => $lastKnown,
            ];

            $cursor->addMinute();
        }

        return view('hostystats::admin.monitors.show', [
            'monitor' => $monitor,
            'slots' => $slots,
        ]);
    }

    public function create()
    {
        return view('hostystats::admin.monitors.form', [
            'monitor' => new Monitor(),
            'categories' => Category::query()->orderBy('position')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request, true);
        Monitor::create($data);

        return redirect()->route('admin.hostystats.monitors.index');
    }

    public function edit(Monitor $monitor)
    {
        return view('hostystats::admin.monitors.form', [
            'monitor' => $monitor,
            'categories' => Category::query()->orderBy('position')->orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, Monitor $monitor)
    {
        $data = $this->validateData($request, false);
        $monitor->update($data);

        return redirect()->route('admin.hostystats.monitors.index');
    }

    public function destroy(Monitor $monitor)
    {
        $monitor->delete();
        return redirect()->route('admin.hostystats.monitors.index');
    }

    private function validateData(Request $request, bool $creating = true): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:hostystats_categories,id'],
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:http,ping,tcp'],
            'target' => ['required', 'string', 'max:255'],

            'expected_http_code' => ['nullable', 'integer', 'min:100', 'max:599'],

            'degraded_threshold_ms' => ['nullable', 'integer', 'min:1', 'max:600000'],
            'timeout_ms' => ['nullable', 'integer', 'min:250', 'max:600000'],
            'interval_sec' => ['nullable', 'integer', 'min:10', 'max:86400'],

            'position' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'forced_status' => ['nullable', 'in:ok,degraded,down,maintenance'],
        ]);

        $data['position'] = (int) ($data['position'] ?? 0);
        $data['timeout_ms'] = (int) ($data['timeout_ms'] ?? 3000);
        $data['interval_sec'] = (int) ($data['interval_sec'] ?? 60);
        $data['degraded_threshold_ms'] = (int) ($data['degraded_threshold_ms'] ?? 800);

        $data['is_active'] = $creating
            ? (bool) ($data['is_active'] ?? true)
            : (bool) ($data['is_active'] ?? false);

        if (($data['type'] ?? '') !== 'http') {
            $data['expected_http_code'] = null;
        }

        return $data;
    }
}
