@extends('admin.layouts.admin')

@section('content')
@php
  /**
   * =========================
   * Helpers UI
   * =========================
   */
  $statusLabel = function(string $status) {
    return match($status) {
      'ok' => 'UP',
      'degraded' => 'DÉGRADÉ',
      'maintenance' => 'MAINTENANCE',
      'down' => 'DOWN',
      default => strtoupper($status),
    };
  };

  $statusText = function(string $status) {
    return match($status) {
      'ok' => 'Opérationnel',
      'degraded' => 'Dégradé',
      'maintenance' => 'Maintenance',
      'down' => 'Incident',
      default => 'Inconnu',
    };
  };

  $statusPill = function(string $status) {
    return match($status) {
      'ok' => 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200',
      'degraded' => 'bg-amber-50 text-amber-800 ring-1 ring-amber-200',
      'maintenance' => 'bg-sky-50 text-sky-800 ring-1 ring-sky-200',
      'down' => 'bg-rose-50 text-rose-800 ring-1 ring-rose-200',
      default => 'bg-slate-50 text-slate-700 ring-1 ring-slate-200',
    };
  };

  $dot = function(string $status) {
    return match($status) {
      'ok' => 'bg-emerald-500',
      'degraded' => 'bg-amber-400',
      'maintenance' => 'bg-sky-500',
      'down' => 'bg-rose-500',
      default => 'bg-slate-400',
    };
  };

  $kpiCard = function(string $tone) {
    return match($tone) {
      'down' => 'border-rose-200 bg-rose-50/40',
      'up' => 'border-emerald-200 bg-emerald-50/40',
      'maintenance' => 'border-sky-200 bg-sky-50/40',
      'degraded' => 'border-amber-200 bg-amber-50/40',
      default => 'border-slate-200 bg-white',
    };
  };

  $kpiDot = function(string $tone) {
    return match($tone) {
      'down' => 'bg-rose-500',
      'up' => 'bg-emerald-500',
      'maintenance' => 'bg-sky-500',
      'degraded' => 'bg-amber-400',
      default => 'bg-slate-400',
    };
  };

  /**
   * =========================
   * Maintenance message logic (Option B like client)
   * =========================
   */
  $activeMessage = $activeMessage ?? null;
  $affectedMonitorIds = $affectedMonitorIds ?? [];

  $isMonitorAffected = function(int $monitorId) use ($activeMessage, $affectedMonitorIds) {
    if (empty($activeMessage) || empty($activeMessage->is_active)) return false;
    if (empty($affectedMonitorIds)) return true; // message global => toutes
    return in_array($monitorId, $affectedMonitorIds, true);
  };

  // OPTION B: message maintenance actif + sonde ciblée => statut effectif = maintenance
  $effectiveStatus = function($m) use ($isMonitorAffected) {
    if ($isMonitorAffected((int)$m->id)) {
      return 'maintenance';
    }
    return $m->forced_status ?: ($m->last_status ?: 'down');
  };

  /**
   * =========================
   * KPIs basés sur le statut effectif
   * =========================
   */
  $all = collect($monitors ?? []);

  $kpiDown = $all->filter(fn($m) => $effectiveStatus($m) === 'down')->count();
  $kpiDegraded = $all->filter(fn($m) => $effectiveStatus($m) === 'degraded')->count();
  $kpiMaint = $all->filter(fn($m) => $effectiveStatus($m) === 'maintenance')->count();
  $kpiUp = $all->filter(fn($m) => $effectiveStatus($m) === 'ok')->count(); // UP = ok uniquement (maintenance exclue + degraded séparé)

  // Tu peux garder ton ancien $kpis si tu veux, mais on force celui-ci pour être cohérent.
  $kpis = [
    'down' => $kpiDown,
    'up' => $kpiUp,
    'degraded' => $kpiDegraded,
    'maintenance' => $kpiMaint,
  ];

  $maintBox = function(?string $sev) {
    return match($sev) {
      'yellow' => 'border-amber-200 bg-amber-50 text-amber-950',
      'orange' => 'border-orange-200 bg-orange-50 text-orange-950',
      'red' => 'border-rose-200 bg-rose-50 text-rose-950',
      default => 'border-slate-200 bg-slate-50 text-slate-900',
    };
  };

  $maintPill = function(?string $sev) {
    return match($sev) {
      'yellow' => 'bg-amber-200 text-amber-950',
      'orange' => 'bg-orange-200 text-orange-950',
      'red' => 'bg-rose-200 text-rose-950',
      default => 'bg-slate-200 text-slate-900',
    };
  };
@endphp

<div class="p-6">
  {{-- Header --}}
  <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-6">
    <div class="min-w-0">
      <div class="flex items-center gap-3">
        <div class="h-2.5 w-2.5 rounded-full bg-slate-900"></div>
        <h1 class="text-xl font-bold text-slate-900">HostyStats • Dashboard</h1>
      </div>
      <p class="text-sm text-slate-600 mt-1">Vue globale du statut de tes sondes.</p>
    </div>

    <div class="flex flex-wrap gap-2">
      <a class="btn" href="{{ route('admin.hostystats.categories.index') }}">Catégories</a>
      <a class="btn" href="{{ route('admin.hostystats.monitors.index') }}">Sondes</a>
      <a class="btn" href="{{ route('admin.hostystats.maintenance') }}">Maintenance</a>
      <a class="btn btn-primary" href="{{ route('admin.hostystats.monitors.create') }}">Créer une sonde</a>
    </div>
  </div>

  {{-- Maintenance banner (si message actif) --}}
  @if($activeMessage && $activeMessage->is_active)
    <div class="rounded-2xl border p-5 mb-6 {{ $maintBox($activeMessage->severity ?? null) }}">
      <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
          <div class="flex items-center gap-2">
            <div class="font-semibold text-base">{{ $activeMessage->title }}</div>
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $maintPill($activeMessage->severity ?? null) }}">
              Maintenance active
            </span>
          </div>

          @if($activeMessage->description)
            <div class="text-sm mt-2 whitespace-pre-line opacity-90">{{ $activeMessage->description }}</div>
          @endif

          <div class="text-xs mt-3 opacity-75">
            @if($activeMessage->starts_at) Début : {{ $activeMessage->starts_at }} @endif
            @if($activeMessage->ends_at)
              @if($activeMessage->starts_at) • @endif
              Fin : {{ $activeMessage->ends_at }}
            @endif
            @if(!empty($affectedMonitorIds))
              • Portée : {{ count($affectedMonitorIds) }} sonde(s)
            @else
              • Portée : globale
            @endif
          </div>
        </div>

        <div class="shrink-0">
          <a class="btn" href="{{ route('admin.hostystats.maintenance') }}">Modifier</a>
        </div>
      </div>
    </div>
  @endif

  {{-- KPI --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="rounded-2xl border p-5 {{ $kpiCard('down') }}">
      <div class="flex items-center justify-between">
        <div class="text-sm font-semibold text-slate-700">Incidents</div>
        <div class="h-2.5 w-2.5 rounded-full {{ $kpiDot('down') }}"></div>
      </div>
      <div class="text-3xl font-bold mt-2 text-slate-900">{{ $kpis['down'] }}</div>
      <div class="text-xs text-slate-600 mt-2">Sondes DOWN</div>
    </div>

    <div class="rounded-2xl border p-5 {{ $kpiCard('up') }}">
      <div class="flex items-center justify-between">
        <div class="text-sm font-semibold text-slate-700">UP</div>
        <div class="h-2.5 w-2.5 rounded-full {{ $kpiDot('up') }}"></div>
      </div>
      <div class="text-3xl font-bold mt-2 text-slate-900">{{ $kpis['up'] }}</div>
      <div class="text-xs text-slate-600 mt-2">Opérationnelles</div>
    </div>

    <div class="rounded-2xl border p-5 {{ $kpiCard('degraded') }}">
      <div class="flex items-center justify-between">
        <div class="text-sm font-semibold text-slate-700">Dégradé</div>
        <div class="h-2.5 w-2.5 rounded-full {{ $kpiDot('degraded') }}"></div>
      </div>
      <div class="text-3xl font-bold mt-2 text-slate-900">{{ $kpis['degraded'] }}</div>
      <div class="text-xs text-slate-600 mt-2">Réponse lente / dégradée</div>
    </div>

    <div class="rounded-2xl border p-5 {{ $kpiCard('maintenance') }}">
      <div class="flex items-center justify-between">
        <div class="text-sm font-semibold text-slate-700">Maintenance</div>
        <div class="h-2.5 w-2.5 rounded-full {{ $kpiDot('maintenance') }}"></div>
      </div>
      <div class="text-3xl font-bold mt-2 text-slate-900">{{ $kpis['maintenance'] }}</div>
      <div class="text-xs text-slate-600 mt-2">Message ou statut forcé</div>
    </div>

  </div>

  {{-- Table --}}
  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="p-5 border-b flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="font-semibold text-slate-900">Toutes les sondes</div>
        <div class="text-sm text-slate-600">Statut effectif (maintenance message incluse) + dernières infos.</div>
      </div>

      <div class="flex flex-wrap gap-2">
        <a class="btn btn-primary" href="{{ route('admin.hostystats.monitors.create') }}">Créer une sonde</a>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="table w-full">
        <thead>
          <tr class="text-left">
            <th class="px-5 py-3 w-[360px]">Sonde</th>
            <th class="px-5 py-3">Catégorie</th>
            <th class="px-5 py-3">Type</th>
            <th class="px-5 py-3">Cible</th>
            <th class="px-5 py-3">État</th>
            <th class="px-5 py-3">Dernier check</th>
            <th class="px-5 py-3 text-right">Actions</th>
          </tr>
        </thead>

        <tbody>
          @forelse($monitors as $m)
            @php
              $affected = $isMonitorAffected((int)$m->id);
              $status = $effectiveStatus($m);

              $meta = [];
              if ($m->last_response_time_ms) $meta[] = $m->last_response_time_ms.'ms';
              if ($m->last_http_code) $meta[] = 'HTTP '.$m->last_http_code;
            @endphp

            <tr class="align-top border-t">
              {{-- Sonde --}}
              <td class="px-5 py-4">
                <div class="flex items-start gap-3">
                  <span class="mt-1 h-2.5 w-2.5 rounded-full {{ $dot($status) }}"></span>

                  <div class="min-w-0">
                    <div class="flex items-center gap-2">
                      <div class="font-medium text-slate-900 leading-5">{{ $m->name }}</div>

                      @if($affected)
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold bg-sky-50 text-sky-800 ring-1 ring-sky-200">
                          Maintenance (message)
                        </span>
                      @endif

                      @if(!empty($m->forced_status))
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold bg-slate-50 text-slate-700 ring-1 ring-slate-200">
                          Statut forcé
                        </span>
                      @endif
                    </div>

                    @if($m->description)
                      <div class="text-xs text-slate-600 mt-1 leading-5">{{ $m->description }}</div>
                    @endif

                    <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-500">
                      <span class="font-mono text-slate-600">{{ $m->target }}</span>
                      <span>•</span>
                      <span class="uppercase">{{ $m->type }}</span>
                      @if(count($meta))
                        <span>•</span>
                        <span>{{ implode(' • ', $meta) }}</span>
                      @endif
                    </div>

                    @if($m->last_error && $status === 'down')
                      <div class="mt-2 text-xs text-slate-500 line-clamp-1">{{ $m->last_error }}</div>
                    @endif
                  </div>
                </div>
              </td>

              <td class="px-5 py-4 text-sm text-slate-700">{{ $m->category?->name ?? '-' }}</td>
              <td class="px-5 py-4 text-sm text-slate-700">{{ strtoupper($m->type) }}</td>
              <td class="px-5 py-4 font-mono text-xs text-slate-700">{{ $m->target }}</td>

              {{-- État --}}
              <td class="px-5 py-4">
                <div class="flex flex-col gap-1">
                  <span class="inline-flex w-fit items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusPill($status) }}">
                    {{ $statusLabel($status) }}
                  </span>
                  <div class="text-xs text-slate-500">{{ $statusText($status) }}</div>
                </div>
              </td>

              {{-- Dernier check --}}
              <td class="px-5 py-4 text-sm text-slate-700">
                @if($m->last_checked_at)
                  <div>{{ $m->last_checked_at->diffForHumans() }}</div>
                  <div class="text-xs text-slate-500">{{ $m->last_checked_at }}</div>
                @else
                  <span class="text-slate-500">Jamais</span>
                @endif
              </td>

              {{-- Actions --}}
              <td class="px-5 py-4 text-right">
                <div class="inline-flex flex-wrap justify-end gap-2">
                  <a class="btn btn-sm" href="{{ route('admin.hostystats.monitors.show', $m) }}">Voir plus</a>
                  <a class="btn btn-sm" href="{{ route('admin.hostystats.monitors.edit', $m) }}">Modifier</a>
                  <form class="inline" method="POST" action="{{ route('admin.hostystats.monitors.destroy', $m) }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette sonde ?')">Supprimer</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-5 py-6 text-sm text-slate-600">Aucune sonde configurée.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
