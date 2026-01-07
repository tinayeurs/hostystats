<?php

namespace App\Addons\HostyStats\Controllers;

use App\Addons\HostyStats\Models\MaintenanceMessage;
use App\Addons\HostyStats\Models\Monitor;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MaintenanceController extends Controller
{
    public function edit()
    {
        $message = MaintenanceMessage::query()->first();

        if (!$message) {
            $message = MaintenanceMessage::create([
                'is_active' => false,
                'show_on_client' => true,
                'show_on_admin' => true,
                'severity' => 'yellow',
                'title' => 'Maintenance planifiée',
                'description' => null,
            ]);
        }

        $monitors = Monitor::query()
            ->with('category')
            ->orderBy('category_id')->orderBy('position')->orderBy('id')
            ->get();

        $selectedMonitorIds = $message->monitors()->pluck('hostystats_monitors.id')->all();

        return view('hostystats::admin.maintenance.edit', compact('message', 'monitors', 'selectedMonitorIds'));
    }

    public function update(Request $request)
    {
        $message = MaintenanceMessage::query()->firstOrFail();

        $data = $request->validate([
            'is_active' => ['nullable', 'boolean'],
            'show_on_client' => ['nullable', 'boolean'],
            'show_on_admin' => ['nullable', 'boolean'],
            'severity' => ['required', 'in:yellow,orange,red'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'monitor_ids' => ['nullable', 'array'],
            'monitor_ids.*' => ['integer', 'exists:hostystats_monitors,id'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['show_on_client'] = (bool) ($data['show_on_client'] ?? false);
        $data['show_on_admin'] = (bool) ($data['show_on_admin'] ?? false);

        $message->update([
            'is_active' => $data['is_active'],
            'show_on_client' => $data['show_on_client'],
            'show_on_admin' => $data['show_on_admin'],
            'severity' => $data['severity'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
        ]);

        $message->monitors()->sync($data['monitor_ids'] ?? []);

        return redirect()->route('admin.hostystats.maintenance')
    ->with('success', 'Message de maintenance enregistré.');

    }
}
