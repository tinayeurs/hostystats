@extends('admin.layouts.admin')

@section('content')
<style>
  .hs-wrap{
    max-width: 980px;
    margin: 0 auto;
    padding: 24px;
  }

  .hs-title{
    display:flex;
    align-items:center;
    gap:12px;
  }
  .hs-title-dot{
    width:10px;height:10px;border-radius:999px;background:#0f172a;
  }
  .dark .hs-title-dot{ background:#e2e8f0; }

  .hs-h1{ font-size:20px; font-weight:800; color:#0f172a; }
  .dark .hs-h1{ color:#e2e8f0; }

  .hs-sub{ margin-top:4px; font-size:13px; color:#64748b; }
  .dark .hs-sub{ color:#94a3b8; }

  .hs-success{
    border:1px solid rgba(16,185,129,.25);
    background: rgba(16,185,129,.10);
    color:#065f46;
    padding:12px 14px;
    border-radius:14px;
    font-size:13px;
    margin-bottom:14px;
  }
  .dark .hs-success{
    border-color: rgba(16,185,129,.25);
    background: rgba(16,185,129,.08);
    color:#a7f3d0;
  }

  .hs-card{
    border:1px solid #e2e8f0;
    background:#fff;
    border-radius:16px;
    box-shadow: 0 1px 2px rgba(15,23,42,.06);
    overflow:hidden;
  }
  .dark .hs-card{
    border-color:#1e293b;
    background:#0f172a;
    box-shadow:none;
  }

  .hs-card-head{
    padding:16px 18px;
    border-bottom:1px solid #e2e8f0;
    background:#f8fafc;
  }
  .dark .hs-card-head{
    border-color:#1e293b;
    background: rgba(2,6,23,.35);
  }

  .hs-card-body{ padding:18px; }

  .hs-grid{
    display:grid;
    grid-template-columns: 1fr;
    gap:14px;
  }
  @media (min-width: 768px){
    .hs-grid{ grid-template-columns: 1fr 1fr; }
    .hs-span-2{ grid-column: span 2; }
  }

  .hs-label{
    display:block;
    font-size:13px;
    font-weight:700;
    color:#0f172a;
    margin-bottom:6px;
  }
  .dark .hs-label{ color:#e2e8f0; }

  .hs-input{
    width:100%;
    border:1px solid #e2e8f0;
    background:#fff;
    color:#0f172a;
    border-radius:12px;
    padding:10px 12px;
    font-size:14px;
    outline:none;
    transition: box-shadow .15s, border-color .15s;
  }
  .hs-input:focus{
    border-color: rgba(56,189,248,.9);
    box-shadow: 0 0 0 4px rgba(56,189,248,.15);
  }
  .dark .hs-input{
    border-color:#1e293b;
    background: rgba(2,6,23,.35);
    color:#e2e8f0;
  }
  .dark .hs-input:focus{
    border-color: rgba(56,189,248,.65);
    box-shadow: 0 0 0 4px rgba(56,189,248,.18);
  }

  .hs-help{
    margin-top:6px;
    font-size:12px;
    color:#64748b;
  }
  .dark .hs-help{ color:#94a3b8; }

  .hs-error{
    margin-top:6px;
    font-size:12px;
    color:#e11d48;
  }

  .hs-row{
    display:flex;
    gap:16px;
    flex-wrap:wrap;
    align-items:center;
  }

  .hs-switch{
    display:flex;
    align-items:center;
    gap:10px;
    user-select:none;
  }
  .hs-toggle{
    appearance:none;
    width:46px;
    height:26px;
    border-radius:999px;
    background: rgba(148,163,184,.25);
    border:1px solid #e2e8f0;
    position:relative;
    cursor:pointer;
    transition: background .15s, border-color .15s;
  }
  .hs-toggle::after{
    content:"";
    position:absolute;
    top:3px;
    left:3px;
    width:18px;
    height:18px;
    border-radius:999px;
    background:#fff;
    box-shadow: 0 1px 2px rgba(15,23,42,.18);
    transition: transform .15s;
  }
  .hs-toggle:checked{
    background:#10b981;
    border-color:#10b981;
  }
  .hs-toggle:checked::after{ transform: translateX(20px); }
  .dark .hs-toggle{
    border-color:#1e293b;
    background: rgba(148,163,184,.18);
  }

  .hs-pill{
    display:inline-flex;
    align-items:center;
    padding:4px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:800;
    border:1px solid transparent;
  }
  .hs-pill-yellow{ background:#fef3c7; color:#78350f; border-color:#fde68a; }
  .hs-pill-orange{ background:#ffedd5; color:#7c2d12; border-color:#fed7aa; }
  .hs-pill-red{ background:#ffe4e6; color:#881337; border-color:#fecdd3; }
  .dark .hs-pill-yellow{ background: rgba(234,179,8,.18); color:#fef3c7; border-color: rgba(234,179,8,.30); }
  .dark .hs-pill-orange{ background: rgba(249,115,22,.16); color:#ffedd5; border-color: rgba(249,115,22,.30); }
  .dark .hs-pill-red{ background: rgba(244,63,94,.16); color:#ffe4e6; border-color: rgba(244,63,94,.30); }

  .hs-box{
    border:1px solid #e2e8f0;
    border-radius:14px;
    background:#fff;
  }
  .dark .hs-box{
    border-color:#1e293b;
    background:#0f172a;
  }

  .hs-box-inner{
    padding:14px;
  }

  .hs-monitors{
    border:1px solid #e2e8f0;
    border-radius:14px;
    padding:12px;
    max-height: 280px;
    overflow:auto;
    background:#f8fafc;
  }
  .dark .hs-monitors{
    border-color:#1e293b;
    background: rgba(2,6,23,.35);
  }

  .hs-monitor-row{
    display:flex;
    align-items:flex-start;
    gap:10px;
    padding:8px 8px;
    border-radius:12px;
    font-size:13px;
    color:#0f172a;
  }
  .hs-monitor-row:hover{ background: rgba(148,163,184,.18); }
  .dark .hs-monitor-row{
    color:#e2e8f0;
  }
  .dark .hs-monitor-row:hover{ background: rgba(148,163,184,.12); }

  .hs-monitor-sub{
    font-size:12px;
    color:#64748b;
    margin-top:2px;
  }
  .dark .hs-monitor-sub{ color:#94a3b8; }

  .hs-actions{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top: 14px;
  }
</style>

<div class="hs-wrap">
  <div class="mb-5">
    <div class="hs-title">
      <span class="hs-title-dot"></span>
      <h1 class="hs-h1">HostyStats • Message de maintenance</h1>
    </div>
    <p class="hs-sub">Affiché sur la page Statut (et éventuellement en admin).</p>
  </div>

  @if(session('success'))
    <div class="hs-success">
      {{ session('success') }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.hostystats.maintenance.update') }}" class="space-y-4">
    @csrf

    {{-- Settings --}}
    <div class="hs-card">
      <div class="hs-card-head">
        <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Configuration</div>
        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
          Active/désactive le message, choisis sa couleur et son affichage.
        </div>
      </div>

      <div class="hs-card-body">
        <div class="hs-grid">

          {{-- Active --}}
          <div>
            <label class="hs-label">Activation</label>
            <div class="hs-switch">
              <input class="hs-toggle" type="checkbox" name="is_active" value="1" @checked(old('is_active', $message->is_active))>
              <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Activer le message</span>
            </div>
            <div class="hs-help">Si désactivé, aucun bandeau n’est affiché.</div>
          </div>

          {{-- Visibility --}}
          <div>
            <label class="hs-label">Visibilité</label>
            <div class="hs-row">
              <label class="hs-switch">
                <input class="hs-toggle" style="width:42px;height:24px" type="checkbox" name="show_on_client" value="1" @checked(old('show_on_client', $message->show_on_client))>
                <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Côté client</span>
              </label>

              <label class="hs-switch">
                <input class="hs-toggle" style="width:42px;height:24px" type="checkbox" name="show_on_admin" value="1" @checked(old('show_on_admin', $message->show_on_admin))>
                <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">En admin</span>
              </label>
            </div>
            <div class="hs-help">Tu peux l’afficher sur la page Statut, et aussi dans l’admin.</div>
          </div>

          {{-- Severity --}}
          <div>
            <label class="hs-label">Couleur</label>
            <select name="severity" class="hs-input">
              @foreach(['yellow' => 'Jaune', 'orange' => 'Orange', 'red' => 'Rouge'] as $k => $v)
                <option value="{{ $k }}" @selected(old('severity', $message->severity) === $k)>{{ $v }}</option>
              @endforeach
            </select>
            <div class="hs-help">
              Indique le niveau d’alerte visuel.
            </div>
          </div>

          {{-- Preview pill --}}
          <div>
            <label class="hs-label">Aperçu</label>
            @php
              $sev = old('severity', $message->severity) ?: 'yellow';
              $previewClass = match($sev){
                'yellow' => 'hs-pill hs-pill-yellow',
                'orange' => 'hs-pill hs-pill-orange',
                'red' => 'hs-pill hs-pill-red',
                default => 'hs-pill hs-pill-yellow',
              };
            @endphp
            <div class="{{ $previewClass }}">Maintenance</div>
            <div class="hs-help">Aperçu du badge “Maintenance”.</div>
          </div>

          {{-- Title --}}
          <div class="hs-span-2">
            <label class="hs-label">Titre</label>
            <input name="title" class="hs-input" value="{{ old('title', $message->title) }}" placeholder="Ex : Maintenance planifiée" />
            @error('title') <div class="hs-error">{{ $message }}</div> @enderror
            <div class="hs-help">Court et clair. Visible en haut du bandeau.</div>
          </div>

          {{-- Description --}}
          <div class="hs-span-2">
            <label class="hs-label">Description</label>
            <textarea name="description" rows="4" class="hs-input" placeholder="Détaille l’intervention, impact, durée, etc.">{{ old('description', $message->description) }}</textarea>
            <div class="hs-help">Optionnel. Tu peux mettre plusieurs lignes.</div>
          </div>

          {{-- Dates --}}
          <div>
            <label class="hs-label">Début (optionnel)</label>
            <input
              type="datetime-local"
              name="starts_at"
              class="hs-input"
              value="{{ old('starts_at', optional($message->starts_at)->format('Y-m-d\TH:i')) }}"
            >
            <div class="hs-help">Affiché à titre informatif.</div>
          </div>

          <div>
            <label class="hs-label">Fin (optionnel)</label>
            <input
              type="datetime-local"
              name="ends_at"
              class="hs-input"
              value="{{ old('ends_at', optional($message->ends_at)->format('Y-m-d\TH:i')) }}"
            >
            <div class="hs-help">Laisse vide si inconnue.</div>
          </div>

        </div>
      </div>
    </div>

    {{-- Affected monitors --}}
    <div class="hs-card">
      <div class="hs-card-head">
        <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Sondes concernées (facultatif)</div>
        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
          Si aucune sonde n’est sélectionnée, le message est considéré comme <b>global</b>.
        </div>
      </div>

      <div class="hs-card-body">
        <div class="hs-monitors">
          @foreach($monitors as $m)
            <label class="hs-monitor-row">
              <input
                type="checkbox"
                name="monitor_ids[]"
                value="{{ $m->id }}"
                @checked(in_array($m->id, old('monitor_ids', $selectedMonitorIds)))
              >
              <div class="min-w-0">
                <div class="font-semibold truncate">{{ $m->name }}</div>
                <div class="hs-monitor-sub truncate">
                  {{ $m->category?->name ?? '-' }} — {{ $m->target }}
                </div>
              </div>
            </label>
          @endforeach
        </div>

        
      </div>
    </div>

    {{-- Actions --}}
    <div class="hs-actions">
      <button class="btn btn-primary rounded-full px-5 py-2" type="submit">Enregistrer</button>
      <a class="btn rounded-full px-5 py-2" href="{{ route('admin.hostystats.dashboard') }}">Retour dashboard</a>
    </div>
  </form>
</div>
@endsection
