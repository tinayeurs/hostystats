@extends('admin.layouts.admin')

@section('content')
<style>
  .hs-wrap{ max-width:1100px; margin:0 auto; padding:24px; }

  .hs-head{
    border:1px solid #e2e8f0;
    background:#fff;
    border-radius:16px;
    box-shadow: 0 1px 2px rgba(15,23,42,.06);
    padding:18px;
    margin-bottom:16px;
  }
  .dark .hs-head{ border-color:#1e293b; background:#0f172a; box-shadow:none; }

  .hs-title{ font-size:20px; font-weight:900; color:#0f172a; line-height:1.15; }
  .dark .hs-title{ color:#e2e8f0; }

  .hs-meta{ margin-top:8px; font-size:13px; color:#64748b; }
  .dark .hs-meta{ color:#94a3b8; }

  .hs-row{ display:flex; align-items:flex-start; justify-content:space-between; gap:16px; }
  .hs-left{ min-width:0; }
  .hs-right{ flex-shrink:0; display:flex; gap:10px; align-items:center; }

  .hs-target{
    margin-top:8px;
    font-size:13px;
    color:#64748b;
  }
  .dark .hs-target{ color:#94a3b8; }
  .hs-mono{
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size:12px;
    color:#334155;
    word-break: break-all;
    background: rgba(148,163,184,.12);
    border:1px solid rgba(148,163,184,.25);
    padding:4px 8px;
    border-radius:10px;
    display:inline-block;
  }
  .dark .hs-mono{
    color:#cbd5e1;
    background: rgba(148,163,184,.10);
    border-color: rgba(148,163,184,.18);
  }

  .hs-pill{
    border-radius:999px;
    padding:10px 14px;
    font-weight:900;
    font-size:12px;
    letter-spacing:.02em;
    white-space:nowrap;
    display:inline-flex;
    align-items:center;
    gap:10px;
    box-shadow: 0 1px 2px rgba(15,23,42,.10);
  }
  .dark .hs-pill{ box-shadow:none; }
  .hs-pill-dot{ width:10px;height:10px;border-radius:999px; background: rgba(255,255,255,.9); }

  .hs-actions-top .btn{
    border-radius:999px;
    padding:8px 12px;
  }

  .hs-bar{
    border-radius:16px;
    border:1px solid rgba(148,163,184,.18);
    background: linear-gradient(180deg, #0b1220 0%, #0f172a 100%);
    color:#fff;
    padding:16px;
    box-shadow: 0 10px 20px rgba(2,6,23,.18);
    margin-bottom:16px;
  }

  .hs-bar-top{
    display:flex; align-items:flex-start; justify-content:space-between; gap:16px;
  }
  .hs-slots{
    display:flex;
    align-items:center;
    gap:2px;
    overflow:hidden;
    padding:6px 8px;
    border-radius:14px;
    background: rgba(255,255,255,.06);
    border:1px solid rgba(255,255,255,.10);
  }
  .hs-slot{ width:4px; height:16px; border-radius:4px; }
  @media (min-width: 768px){ .hs-slot{ width:5px; height:18px; } }

  .hs-bar-foot{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-top:10px;
    font-size:12px;
    color: rgba(226,232,240,.85);
  }

  .hs-bar-note{
    margin-top:8px;
    font-size:12px;
    color: rgba(226,232,240,.78);
  }

  .hs-grid{ display:grid; grid-template-columns:1fr; gap:12px; }
  @media (min-width: 1024px){ .hs-grid{ grid-template-columns:repeat(3, 1fr); } }

  .hs-card{
    border:1px solid #e2e8f0;
    background:#fff;
    border-radius:16px;
    padding:16px;
    box-shadow: 0 1px 2px rgba(15,23,42,.06);
  }
  .dark .hs-card{ border-color:#1e293b; background:#0f172a; box-shadow:none; }

  .hs-card-h{ font-weight:900; color:#0f172a; margin-bottom:10px; }
  .dark .hs-card-h{ color:#e2e8f0; }

  .hs-card-b{ font-size:13px; color:#334155; }
  .dark .hs-card-b{ color:#cbd5e1; }

  .hs-card-b .muted{ font-size:12px; color:#64748b; }
  .dark .hs-card-b .muted{ color:#94a3b8; }

  .hs-errorbox{
    margin-top:10px;
    border-radius:14px;
    border:1px solid rgba(244,63,94,.22);
    background: rgba(244,63,94,.08);
    padding:10px 12px;
    font-size:12px;
    color:#e11d48;
    word-break: break-word;
  }
  .dark .hs-errorbox{
    border-color: rgba(244,63,94,.28);
    background: rgba(244,63,94,.12);
    color:#fecdd3;
  }

  .hs-bottom-actions{ margin-top:16px; display:flex; gap:10px; flex-wrap:wrap; }
  .hs-bottom-actions .btn{ border-radius:999px; padding:10px 14px; }
</style>

@php
  $status = $monitor->forced_status ?: ($monitor->last_status ?: 'down');

  $label = match($status) {
    'ok' => 'En ligne',
    'degraded' => 'Dégradé',
    'maintenance' => 'Maintenance',
    default => 'Hors ligne',
  };

  $pill = match($status) {
    'ok' => 'background:#10b981;color:#fff;',
    'degraded' => 'background:#fbbf24;color:#0f172a;',
    'maintenance' => 'background:#0ea5e9;color:#fff;',
    default => 'background:#f43f5e;color:#fff;',
  };

  $slotClass = function(string $s) {
    return match($s) {
      'ok' => 'background:#10b981;',
      'degraded' => 'background:#fbbf24;',
      'maintenance' => 'background:#0ea5e9;',
      'down' => 'background:#f43f5e;',
      default => 'background:rgba(148,163,184,.28);',
    };
  };
@endphp

<div class="hs-wrap">
  {{-- Header card --}}
  <div class="hs-head">
    <div class="hs-row">
      <div class="hs-left">
        <div class="hs-title">{{ $monitor->name }}</div>

        <div class="hs-meta">
          Catégorie : <span class="font-semibold">{{ $monitor->category?->name ?? '-' }}</span>
          • Type : <span class="font-semibold uppercase">{{ $monitor->type }}</span>
          • Interval : <span class="font-semibold">{{ $monitor->interval_sec }}s</span>
        </div>

        <div class="hs-target">
          Cible : <span class="hs-mono">{{ $monitor->target }}</span>
        </div>
      </div>

      <div class="hs-right">
        <div class="hs-pill" style="{{ $pill }}">
          <span class="hs-pill-dot"></span>
          {{ $label }}
        </div>

        <div class="hs-actions-top hidden sm:flex">
          <a class="btn" href="{{ route('admin.hostystats.monitors.index') }}">Retour</a>
          <a class="btn btn-primary" href="{{ route('admin.hostystats.monitors.edit', $monitor) }}">Modifier</a>
        </div>
      </div>
    </div>
  </div>

  {{-- 1H bar --}}
  <div class="hs-bar">
    <div class="hs-bar-top">
      <div class="flex-1 min-w-0">
        <div class="hs-slots" aria-label="Historique 1 heure">
          @foreach($slots as $slot)
            <div
              class="hs-slot"
              style="{{ $slotClass($slot['status']) }}"
              title="{{ $slot['minute']->format('H:i') }} • {{ strtoupper($slot['status']) }}">
            </div>
          @endforeach
        </div>

        <div class="hs-bar-foot">
          <span>1h</span>
          <span>Maintenant</span>
        </div>

        <div class="hs-bar-note">
          Vérification toutes les <strong>{{ (int)$monitor->interval_sec }}</strong> secondes
          ({{ max(1, (int) round($monitor->interval_sec / 60)) }} minutes)
        </div>
      </div>

      <div class="shrink-0">
        <div class="hs-pill" style="{{ $pill }}">
          <span class="hs-pill-dot"></span>
          {{ $label }}
        </div>
      </div>
    </div>
  </div>

  {{-- Details --}}
  <div class="hs-grid">
    <div class="hs-card">
      <div class="hs-card-h">Dernier check</div>
      <div class="hs-card-b">
        @if($monitor->last_checked_at)
          <div class="font-semibold">{{ $monitor->last_checked_at->diffForHumans() }}</div>
          <div class="muted mt-1">{{ $monitor->last_checked_at }}</div>
        @else
          <div class="muted">Jamais</div>
        @endif
      </div>
    </div>

    <div class="hs-card">
      <div class="hs-card-h">Mesures</div>
      <div class="hs-card-b space-y-1">
        <div>Réponse : <span class="font-semibold">{{ $monitor->last_response_time_ms ? $monitor->last_response_time_ms.'ms' : '-' }}</span></div>
        <div>HTTP : <span class="font-semibold">{{ $monitor->last_http_code ?: '-' }}</span></div>
        <div>Timeout : <span class="font-semibold">{{ $monitor->timeout_ms }}ms</span></div>
        <div>Seuil dégradé : <span class="font-semibold">{{ $monitor->degraded_threshold_ms }}ms</span></div>
      </div>
    </div>

    <div class="hs-card">
      <div class="hs-card-h">État</div>
      <div class="hs-card-b">
        <div>Actif : <span class="font-semibold">{{ $monitor->is_active ? 'Oui' : 'Non' }}</span></div>
        <div class="mt-1">Statut forcé : <span class="font-semibold">{{ $monitor->forced_status ?: 'Aucun' }}</span></div>

        @if($monitor->last_error)
          <div class="hs-errorbox">
            <strong>Erreur :</strong> {{ $monitor->last_error }}
          </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Bottom actions (mobile + general) --}}
  <div class="hs-bottom-actions sm:hidden">
    <a class="btn" href="{{ route('admin.hostystats.monitors.index') }}">Retour</a>
    <a class="btn btn-primary" href="{{ route('admin.hostystats.monitors.edit', $monitor) }}">Modifier</a>
  </div>
</div>
@endsection
