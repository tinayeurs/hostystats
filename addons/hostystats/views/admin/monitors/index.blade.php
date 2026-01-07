@extends('admin.layouts.admin')

@section('navbar', '')

@section('content')
<style>
  .hs-btn-round{
    border-radius:999px;
    padding:8px 16px;
  }
</style>

<div class="p-6 max-w-5xl mx-auto">

  {{-- HEADER + bouton à droite --}}
  <div class="flex items-center justify-between mb-5">
    <div class="min-w-0">
      <div class="flex items-center gap-3">
        <div class="h-2.5 w-2.5 rounded-full bg-slate-900 dark:bg-slate-100"></div>
        <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 truncate">
          HostyStats • Sondes
        </h1>
      </div>
      <p class="text-sm text-slate-600 dark:text-slate-300 mt-1 truncate">
        Gestion des sondes, cibles, types et activation.
      </p>
    </div>

    <a class="btn btn-primary hs-btn-round" href="{{ route('admin.hostystats.monitors.create') }}">
      Créer une sonde
    </a>
  </div>

  {{-- TABLE CARD --}}
  <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full border-collapse">
        <thead class="bg-slate-50 dark:bg-slate-950/40 border-b border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-sm uppercase font-bold">
          <tr>
            <th class="px-5 py-3 text-left">Nom</th>
            <th class="px-5 py-3 text-left">Catégorie</th>
            <th class="px-5 py-3 text-left">Type</th>
            <th class="px-5 py-3 text-left">Cible</th>
            <th class="px-5 py-3 text-left">Actif</th>
            <th class="px-5 py-3 text-right">Actions</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
          @forelse($monitors as $m)
            @php
              $status = $m->forced_status ?: ($m->last_status ?: 'down');
            @endphp
            <tr class="text-sm text-slate-900 dark:text-slate-100 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
              <td class="px-5 py-4 font-medium truncate max-w-[260px]">{{ $m->name }}</td>
              <td class="px-5 py-4">{{ $m->category?->name ?? '-' }}</td>
              <td class="px-5 py-4">
                <span class="inline-flex items-center gap-1 px-2 py-1 border rounded-full bg-slate-100 dark:bg-slate-800 text-xs font-semibold">
                  {{ strtoupper($m->type) }}
                </span>
              </td>
              <td class="px-5 py-4 font-mono text-xs">{{ $m->target }}</td>
              <td class="px-5 py-4">
                @if($m->is_active)
                  <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">
                    Oui
                  </span>
                @else
                  <span class="text-slate-500 dark:text-slate-400">Non</span>
                @endif
              </td>

              {{-- ACTIONS --}}
              <td class="px-5 py-4 text-right">
                <div class="inline-flex gap-2 justify-end flex-wrap">
                  <a class="btn btn-sm rounded-full px-3 py-1.5" href="{{ route('admin.hostystats.monitors.edit', $m) }}">
                    Modifier
                  </a>
                  <form class="inline" method="POST" action="{{ route('admin.hostystats.monitors.destroy', $m) }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm bg-red-600 text-white hover:bg-red-700 transition rounded-full px-3 py-1.5"
                            onclick="return confirm('Supprimer cette sonde ?')">
                      Supprimer
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-4 text-sm text-slate-600 dark:text-slate-300">
                Aucune sonde configurée.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- FOOTER --}}
  <div class="mt-6">
    <a class="btn rounded-full px-4 py-2" href="{{ route('admin.hostystats.categories.index') }}">
      Gérer les catégories
    </a>
  </div>

</div>
@endsection
