@extends('layouts.front')

@section('title', 'Statut des services')

@section('content')
@php
  $slotClass = function(string $s) {
    return match($s) {
      'ok' => 'bg-emerald-500',
      'degraded' => 'bg-amber-400',
      'maintenance' => 'bg-sky-500',
      'down' => 'bg-rose-500',
      default => 'bg-slate-400',
    };
  };

  $pill = function(string $s) {
    return match($s) {
      'ok' => 'bg-emerald-500 text-white',
      'degraded' => 'bg-amber-400 text-slate-900',
      'maintenance' => 'bg-sky-500 text-white',
      'down' => 'bg-rose-500 text-white',
      default => 'bg-slate-500 text-white',
    };
  };

  $label = function(string $s) {
    return match($s) {
      'ok' => 'Opérationnel',
      'degraded' => 'Dégradé',
      'maintenance' => 'Maintenance',
      'down' => 'Incident',
      default => 'Inconnu',
    };
  };

  $maintenanceBox = function(?string $severity) {
    return match($severity) {
      'yellow' => 'border-amber-200 bg-amber-50 text-amber-950 dark:border-amber-900/60 dark:bg-amber-950/20 dark:text-amber-100',
      'orange' => 'border-orange-200 bg-orange-50 text-orange-950 dark:border-orange-900/60 dark:bg-orange-950/20 dark:text-orange-100',
      'red' => 'border-rose-200 bg-rose-50 text-rose-950 dark:border-rose-900/60 dark:bg-rose-950/20 dark:text-rose-100',
      default => 'border-amber-200 bg-amber-50 text-amber-950 dark:border-amber-900/60 dark:bg-amber-950/20 dark:text-amber-100',
    };
  };

  $maintenancePill = function(?string $severity) {
    return match($severity) {
      'yellow' => 'bg-amber-200 text-amber-950 dark:bg-amber-900/50 dark:text-amber-100',
      'orange' => 'bg-orange-200 text-orange-950 dark:bg-orange-900/50 dark:text-orange-100',
      'red' => 'bg-rose-200 text-rose-950 dark:bg-rose-900/50 dark:text-rose-100',
      default => 'bg-amber-200 text-amber-950 dark:bg-amber-900/50 dark:text-amber-100',
    };
  };

  $isMonitorAffected = function(int $monitorId) use ($activeMessage, $affectedMonitorIds) {
    if (empty($activeMessage)) return false;
    if (empty($affectedMonitorIds)) return true;
    return in_array($monitorId, $affectedMonitorIds, true);
  };

  $effectiveStatus = function($m) use ($isMonitorAffected) {
    if ($isMonitorAffected((int)$m->id)) {
      return 'maintenance';
    }
    return $m->forced_status ?: ($m->last_status ?: 'down');
  };

  $allMonitors = collect();
  foreach ($monitorsByCategory as $group) { $allMonitors = $allMonitors->merge($group); }

  $countTotal = $allMonitors->count();
  $countDown = $allMonitors->filter(fn($m) => ($effectiveStatus($m) === 'down'))->count();
  $countDegraded = $allMonitors->filter(fn($m) => ($effectiveStatus($m) === 'degraded'))->count();
  $countMaint = $allMonitors->filter(fn($m) => ($effectiveStatus($m) === 'maintenance'))->count();
  $countOk = $allMonitors->filter(fn($m) => ($effectiveStatus($m) === 'ok'))->count();

  $globalState = 'ok';
  if ($countDown > 0) $globalState = 'down';
  elseif ($countDegraded > 0) $globalState = 'degraded';
  elseif ($countMaint > 0) $globalState = 'maintenance';

  $stateText = match($globalState) {
    'ok' => 'Tous les systèmes sont opérationnels.',
    'degraded' => 'Certains services sont dégradés.',
    'maintenance' => 'Maintenance en cours sur certains services.',
    'down' => 'Incident en cours : certains services sont indisponibles.',
    default => 'Statut inconnu.',
  };
@endphp

<div class="bg-white dark:bg-slate-950">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- HERO (safe, no absolute positioning) --}}
    <div class="mb-7 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm
                dark:border-slate-800 dark:bg-slate-900">
      <div class="flex items-start justify-between gap-6">
        <div class="min-w-0">
          <div class="flex items-center gap-3">
            <div class="h-3 w-3 rounded-full {{ $slotClass($globalState) }}"></div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
              Statut des services
            </h1>
            <span class="hidden sm:inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $pill($globalState) }}">
              {{ $label($globalState) }}
            </span>
          </div>

          <p class="mt-2 text-sm sm:text-base text-slate-600 dark:text-slate-300">
            {{ $stateText }}
          </p>

          <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-600 dark:text-slate-300">
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1
                         dark:border-slate-800 dark:bg-slate-950/40">
              <span class="h-2 w-2 rounded-full bg-emerald-500"></span> OK: {{ $countOk }}
            </span>
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1
                         dark:border-slate-800 dark:bg-slate-950/40">
              <span class="h-2 w-2 rounded-full bg-amber-400"></span> Dégradé: {{ $countDegraded }}
            </span>
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1
                         dark:border-slate-800 dark:bg-slate-950/40">
              <span class="h-2 w-2 rounded-full bg-sky-500"></span> Maintenance: {{ $countMaint }}
            </span>
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1
                         dark:border-slate-800 dark:bg-slate-950/40">
              <span class="h-2 w-2 rounded-full bg-rose-500"></span> Incident: {{ $countDown }}
            </span>
          </div>
        </div>

        <div class="hidden md:block shrink-0">
          <div class="rounded-xl border border-slate-200 bg-slate-50 p-4
                      dark:border-slate-800 dark:bg-slate-950/40">
            <div class="text-xs text-slate-500 dark:text-slate-400">Dernière mise à jour</div>
            <div class="mt-1 font-semibold text-slate-900 dark:text-slate-100">
              {{ now()->format('d/m/Y H:i') }}
            </div>
            <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
              Rafraîchissez la page pour voir les changements.
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Maintenance banner --}}
    @if(!empty($activeMessage))
      @php $sev = $activeMessage->severity ?: 'yellow'; @endphp

      <div class="hs-maintenance hs-sev-{{ $sev }} rounded-2xl border p-5 mb-7 shadow-sm">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <div class="flex items-center gap-2">
              <div class="font-semibold text-base">
                {{ $activeMessage->title }}
              </div>

              <span class="hs-maintenance-pill text-xs px-2 py-1 rounded-full font-semibold">
                Maintenance
              </span>
            </div>

            @if($activeMessage->description)
              <div class="text-sm mt-2 whitespace-pre-line opacity-95">
                {{ $activeMessage->description }}
              </div>
            @endif

            <div class="text-xs mt-3 opacity-80">
              @if($activeMessage->starts_at)
                Début : {{ $activeMessage->starts_at }}
              @endif
              @if($activeMessage->ends_at)
                @if($activeMessage->starts_at) • @endif
                Fin : {{ $activeMessage->ends_at }}
              @endif

              @if(empty($affectedMonitorIds))
                • Portée : globale
              @else
                • Portée : {{ count($affectedMonitorIds) }} sonde(s)
              @endif
            </div>
          </div>
        </div>
      </div>
    @endif

    {{-- Categories --}}
    @forelse($categories as $cat)
      @php $monitors = $monitorsByCategory->get($cat->id, collect()); @endphp

      <section class="mb-7">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden
                    dark:border-slate-800 dark:bg-slate-900">
          <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between gap-4">
              <div class="min-w-0">
                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $cat->name }}</div>
                @if($cat->description)
                  <div class="text-sm text-slate-600 dark:text-slate-300 mt-1">{{ $cat->description }}</div>
                @endif
              </div>
              <div class="text-xs text-slate-500 dark:text-slate-400">
                {{ $monitors->count() }} sonde(s)
              </div>
            </div>
          </div>

          <div class="divide-y divide-slate-200 dark:divide-slate-800">
            @forelse($monitors as $m)
              @php
                $affected = $isMonitorAffected((int)$m->id);

                
                $status = $affected
                  ? 'maintenance'
                  : ($m->forced_status ?: ($m->last_status ?: 'down'));

                $slots = $slotsByMonitor[$m->id] ?? [];

                $meta = [];
                if ($m->last_response_time_ms) $meta[] = $m->last_response_time_ms.'ms';
                if ($m->last_http_code) $meta[] = 'HTTP '.$m->last_http_code;
              @endphp

              <details class="group">
                <summary class="list-none cursor-pointer px-5 py-4 flex items-center justify-between gap-4
                                hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                  <div class="min-w-0">
                    <div class="flex items-center gap-3 min-w-0">
                      <div class="h-2.5 w-2.5 rounded-full {{ $slotClass($status) }}"></div>

                      <div class="font-medium text-slate-900 dark:text-slate-100 truncate">
                        {{ $m->name }}
                      </div>

                      @if($affected)
                        <span class="text-xs px-2 py-1 rounded-full bg-sky-100 text-sky-800
                                     dark:bg-sky-900/40 dark:text-sky-200 shrink-0">
                          Maintenance
                        </span>
                      @endif
                    </div>

                    <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs">
                      <span class="text-slate-500 dark:text-slate-400">{{ strtoupper($m->type) }}</span>
                      @if(count($meta))
                        <span class="text-slate-500 dark:text-slate-400">• {{ implode(' • ', $meta) }}</span>
                      @endif
                    </div>

                    @if($m->description)
                      <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        {{ $m->description }}
                      </div>
                    @endif
                  </div>

                  <div class="flex items-center gap-3 shrink-0">
                    <div class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">
                      @if($m->last_checked_at) {{ $m->last_checked_at->diffForHumans() }} @else Jamais @endif
                    </div>

                    <div class="rounded-full px-4 py-2 text-sm font-semibold {{ $pill($status) }}">
                      {{ $label($status) }}
                    </div>

                    <div class="text-slate-400 dark:text-slate-500 group-open:rotate-180 transition">▾</div>
                  </div>
                </summary>

                <div class="px-5 pb-5 pt-4 bg-slate-50 dark:bg-slate-950/30">
                  <div class="rounded-2xl border border-slate-200 bg-slate-950 text-white p-4
                              dark:border-slate-800 dark:bg-slate-950 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                      <div class="flex-1">
                        <div class="flex items-center gap-1 overflow-hidden">
                          @foreach($slots as $slot)
                            <div
                              title="{{ $slot['minute']->format('H:i') }} • {{ strtoupper($slot['status']) }}"
                              class="h-4 w-2 rounded {{ $slotClass($slot['status']) }}">
                            </div>
                          @endforeach
                        </div>

                        <div class="flex items-center justify-between text-xs text-slate-300 mt-2">
                          <span>1h</span>
                          <span>Maintenant</span>
                        </div>

                        <div class="text-xs text-slate-300 mt-2">
                          Vérifier toutes les {{ (int)$m->interval_sec }} secondes
                          ({{ max(1, (int) round($m->interval_sec / 60)) }} minutes)
                        </div>
                      </div>

                      <div class="shrink-0">
                        <div class="rounded-full px-6 py-3 font-bold {{ $pill($status) }}">
                          {{ $label($status) }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm
                                dark:border-slate-800 dark:bg-slate-900">
                      <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Dernier check</div>
                      <div class="text-sm text-slate-700 dark:text-slate-200 mt-2">
                        @if($m->last_checked_at)
                          <div>{{ $m->last_checked_at->diffForHumans() }}</div>
                          <div class="text-xs text-slate-500 dark:text-slate-400">{{ $m->last_checked_at }}</div>
                        @else
                          <div class="text-slate-500 dark:text-slate-400">Jamais</div>
                        @endif
                      </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm
                                dark:border-slate-800 dark:bg-slate-900">
                      <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Mesures</div>
                      <div class="text-sm text-slate-700 dark:text-slate-200 mt-2 space-y-1">
                        <div>Réponse : <span class="font-medium">{{ $m->last_response_time_ms ? $m->last_response_time_ms.'ms' : '-' }}</span></div>
                        <div>HTTP : <span class="font-medium">{{ $m->last_http_code ?: '-' }}</span></div>
                        <div>Type : <span class="font-medium uppercase">{{ $m->type }}</span></div>
                      </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm
                                dark:border-slate-800 dark:bg-slate-900">
                      <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Informations</div>
                      <div class="text-sm text-slate-700 dark:text-slate-200 mt-2">
                        <div>Catégorie : <span class="font-medium">{{ $cat->name }}</span></div>

                        @if($activeMessage && $affected)
                          <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Maintenance en cours (message d’information)
                          </div>
                        @endif

                        @if($m->last_error && ($status === 'down'))
                          <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 break-words">
                            Erreur : {{ $m->last_error }}
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </details>

            @empty
              <div class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                Aucune sonde dans cette catégorie.
              </div>
            @endforelse
          </div>
        </div>
      </section>
    @empty
      <div class="rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-600 shadow-sm
                  dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
        Aucune catégorie/sonde active n’est configurée.
      </div>
    @endforelse

  </div>
</div>
@endsection

<style>
  .hs-maintenance{
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,.08);
    background: #fff;
    color: #0f172a;
  }

  .hs-maintenance .hs-maintenance-pill{
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .3rem .6rem;
    border-radius: 999px;
    font-weight: 700;
  }

  .hs-maintenance.hs-sev-yellow{
    border-color: #fcd34d;
    background: #fffbeb;
    color: #78350f;
  }
  .hs-maintenance.hs-sev-yellow .hs-maintenance-pill{
    background: #fde68a;
    color: #78350f;
  }

  .hs-maintenance.hs-sev-orange{
    border-color: #fdba74;
    background: #fff7ed;
    color: #7c2d12;
  }
  .hs-maintenance.hs-sev-orange .hs-maintenance-pill{
    background: #fed7aa;
    color: #7c2d12;
  }

  .hs-maintenance.hs-sev-red{
    border-color: #fda4af;
    background: #fff1f2;
    color: #881337;
  }
  .hs-maintenance.hs-sev-red .hs-maintenance-pill{
    background: #fecdd3;
    color: #881337;
  }

  .dark .hs-maintenance{
    border-color: rgba(148,163,184,.25);
    background: rgba(2,6,23,.55);
    color: #e2e8f0;
  }

  .dark .hs-maintenance.hs-sev-yellow{
    border-color: rgba(234,179,8,.35);
    background: rgba(234,179,8,.10);
    color: #fef3c7;
  }
  .dark .hs-maintenance.hs-sev-yellow .hs-maintenance-pill{
    background: rgba(234,179,8,.25);
    color: #fef3c7;
  }

  .dark .hs-maintenance.hs-sev-orange{
    border-color: rgba(249,115,22,.35);
    background: rgba(249,115,22,.10);
    color: #ffedd5;
  }
  .dark .hs-maintenance.hs-sev-orange .hs-maintenance-pill{
    background: rgba(249,115,22,.25);
    color: #ffedd5;
  }

  .dark .hs-maintenance.hs-sev-red{
    border-color: rgba(244,63,94,.35);
    background: rgba(244,63,94,.10);
    color: #ffe4e6;
  }
  .dark .hs-maintenance.hs-sev-red .hs-maintenance-pill{
    background: rgba(244,63,94,.25);
    color: #ffe4e6;
  }
</style>
