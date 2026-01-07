@extends('admin.layouts.admin')

@section('content')
<style>
  .hs-wrap{
    max-width: 960px;
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

  .hs-head{
    padding:16px 18px;
    border-bottom:1px solid #e2e8f0;
    background:#f8fafc;
  }
  .dark .hs-head{
    border-color:#1e293b;
    background: rgba(2,6,23,.35);
  }

  .hs-body{ padding:18px; }

  .hs-label{
    display:block;
    font-size:13px;
    font-weight:800;
    color:#0f172a;
    margin-bottom:6px;
  }
  .dark .hs-label{ color:#e2e8f0; }

  .hs-help{
    margin-top:6px;
    font-size:12px;
    color:#64748b;
  }
  .dark .hs-help{ color:#94a3b8; }

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

  .hs-error{
    margin-top:6px;
    font-size:12px;
    color:#e11d48;
  }

  .hs-grid{
    display:grid;
    grid-template-columns: 1fr;
    gap:14px;
  }
  @media (min-width: 768px){
    .hs-grid-2{ grid-template-columns: 1fr 1fr; }
    .hs-grid-3{ grid-template-columns: 1fr 1fr 1fr; }
    .hs-span-2{ grid-column: span 2; }
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

  .hs-chip{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:6px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:800;
    border:1px solid rgba(148,163,184,.35);
    background: rgba(148,163,184,.10);
    color:#334155;
  }
  .dark .hs-chip{
    border-color: rgba(148,163,184,.20);
    background: rgba(148,163,184,.10);
    color:#cbd5e1;
  }

  .hs-actions{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    padding:16px 18px;
    border-top:1px solid #e2e8f0;
    background:#f8fafc;
  }
  .dark .hs-actions{
    border-color:#1e293b;
    background: rgba(2,6,23,.35);
  }
</style>

<div class="hs-wrap">
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-5">
    <div class="min-w-0">
      <div class="hs-title">
        <span class="hs-title-dot"></span>
        <h1 class="hs-h1">
          {{ $monitor->exists ? 'Modifier une sonde' : 'Créer une sonde' }}
        </h1>
      </div>
      <p class="hs-sub">
        Configure la cible, le type de check, l’intervalle et les seuils de performance.
      </p>
    </div>

    <a class="btn rounded-full px-4 py-2" href="{{ route('admin.hostystats.monitors.index') }}">Retour</a>
  </div>

  <div class="hs-card">
    <div class="hs-head">
      <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Paramètres de la sonde</div>
      <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
        Astuce : TCP attend <span class="hs-chip">host:port</span> • HTTP attend une URL • PING attend une IP ou un hostname.
      </div>
    </div>

    <form method="POST" action="{{ $monitor->exists ? route('admin.hostystats.monitors.update', $monitor) : route('admin.hostystats.monitors.store') }}">
      @csrf
      @if($monitor->exists) @method('PUT') @endif

      <div class="hs-body">
        {{-- Category --}}
        <div class="mb-4">
          <label class="hs-label">Catégorie <span class="text-rose-600">*</span></label>
          <select class="hs-input input" name="category_id" required>
            @foreach($categories as $c)
              <option value="{{ $c->id }}" {{ (int)old('category_id', $monitor->category_id) === $c->id ? 'selected' : '' }}>
                {{ $c->name }}
              </option>
            @endforeach
          </select>
          @error('category_id') <div class="hs-error">{{ $message }}</div> @enderror
        </div>

        {{-- Name --}}
        <div class="mb-4">
          <label class="hs-label">Nom <span class="text-rose-600">*</span></label>
          <input class="hs-input input" name="name" value="{{ old('name', $monitor->name) }}" required>
          @error('name') <div class="hs-error">{{ $message }}</div> @enderror
        </div>

        {{-- Description --}}
        <div class="mb-4">
          <label class="hs-label">Description</label>
          <textarea class="hs-input input" name="description" rows="3">{{ old('description', $monitor->description) }}</textarea>
          @error('description') <div class="hs-error">{{ $message }}</div> @enderror
        </div>

        {{-- Type + target --}}
        <div class="hs-grid hs-grid-2 mb-4">
          <div>
            <label class="hs-label">Type <span class="text-rose-600">*</span></label>
            <select class="hs-input input" name="type" required>
              @php($t = old('type', $monitor->type ?: 'http'))
              <option value="http" {{ $t==='http'?'selected':'' }}>HTTP</option>
              <option value="ping" {{ $t==='ping'?'selected':'' }}>PING</option>
              <option value="tcp"  {{ $t==='tcp'?'selected':'' }}>TCP</option>
            </select>
            @error('type') <div class="hs-error">{{ $message }}</div> @enderror
            <div class="hs-help">Choisis la méthode de vérification.</div>
          </div>

          <div>
            <label class="hs-label">Cible <span class="text-rose-600">*</span></label>
            <input class="hs-input input" name="target" value="{{ old('target', $monitor->target) }}" required placeholder="https://site.tld | 1.2.3.4 | host:port">
            @error('target') <div class="hs-error">{{ $message }}</div> @enderror
            <div class="hs-help">Ex : https://demo.hostalis.fr • 1.2.3.4 • node1:25565</div>
          </div>
        </div>

        {{-- Expected + degraded --}}
        <div class="hs-grid hs-grid-2 mb-4">
          <div>
            <label class="hs-label">HTTP attendu (optionnel)</label>
            <input class="hs-input input" type="number" name="expected_http_code" value="{{ old('expected_http_code', $monitor->expected_http_code) }}" placeholder="200">
            @error('expected_http_code') <div class="hs-error">{{ $message }}</div> @enderror
            <div class="hs-help">Si vide, OK = HTTP 2xx/3xx.</div>
          </div>

          <div>
            <label class="hs-label">Seuil dégradé (ms)</label>
            <input class="hs-input input" type="number" name="degraded_threshold_ms" value="{{ old('degraded_threshold_ms', $monitor->degraded_threshold_ms ?? 800) }}">
            @error('degraded_threshold_ms') <div class="hs-error">{{ $message }}</div> @enderror
            <div class="hs-help">Au-dessus de ce seuil : “Dégradé”.</div>
          </div>
        </div>

        {{-- Timeout + interval + position --}}
        <div class="hs-grid hs-grid-3 mb-4">
          <div>
            <label class="hs-label">Timeout (ms)</label>
            <input class="hs-input input" type="number" name="timeout_ms" value="{{ old('timeout_ms', $monitor->timeout_ms ?? 3000) }}">
            @error('timeout_ms') <div class="hs-error">{{ $message }}</div> @enderror
            <div class="hs-help">Ex : 3000 = 3s.</div>
          </div>

          <div>
            <label class="hs-label">Intervalle (s)</label>
            <input class="hs-input input" type="number" name="interval_sec" value="{{ old('interval_sec', $monitor->interval_sec ?? 60) }}">
            @error('interval_sec') <div class="hs-error">{{ $message }}</div> @enderror
            <div class="hs-help">Respecté par la commande cron.</div>
          </div>

          <div>
            <label class="hs-label">Position</label>
            <input class="hs-input input" type="number" name="position" value="{{ old('position', $monitor->position ?? 0) }}">
            @error('position') <div class="hs-error">{{ $message }}</div> @enderror
            <div class="hs-help">Ordre d’affichage dans la catégorie.</div>
          </div>
        </div>

        {{-- Active + forced status --}}
        <div class="hs-grid hs-grid-2">
          <div>
            <label class="hs-label">Activation</label>
            <input type="hidden" name="is_active" value="0">
            <label class="hs-switch">
              <input class="hs-toggle" type="checkbox" name="is_active" value="1"
                     {{ old('is_active', $monitor->exists ? $monitor->is_active : true) ? 'checked' : '' }}>
              <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Sonde active</span>
            </label>
            @error('is_active') <div class="hs-error">{{ $message }}</div> @enderror
            <div class="hs-help">Si désactivée, elle n’est pas checkée.</div>
          </div>

          <div>
            <label class="hs-label">Forcer le statut (optionnel)</label>
            @php($fs = old('forced_status', $monitor->forced_status))
            <select class="hs-input input" name="forced_status">
              <option value="" {{ empty($fs) ? 'selected' : '' }}>Aucun</option>
              <option value="ok" {{ $fs==='ok'?'selected':'' }}>OK</option>
              <option value="degraded" {{ $fs==='degraded'?'selected':'' }}>Dégradé</option>
              <option value="down" {{ $fs==='down'?'selected':'' }}>Down</option>
              <option value="maintenance" {{ $fs==='maintenance'?'selected':'' }}>Maintenance</option>
            </select>
            @error('forced_status') <div class="hs-error">{{ $message }}</div> @enderror
            <div class="hs-help">Prioritaire sur le statut réel (utile pour incident/maintenance).</div>
          </div>
        </div>
      </div>

      <div class="hs-actions">
        <button class="btn btn-primary rounded-full px-5 py-2">Enregistrer</button>
        <a class="btn rounded-full px-5 py-2" href="{{ route('admin.hostystats.monitors.index') }}">Annuler</a>
      </div>
    </form>
  </div>
</div>
@endsection
