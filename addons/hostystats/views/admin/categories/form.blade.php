@extends('admin.layouts.admin')

@section('content')
<style>
  .hs-card {
    background: #fff;
    border: 1px solid rgb(226 232 240);
    border-radius: 16px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .06);
    overflow: hidden;
  }
  .dark .hs-card {
    background: rgb(15 23 42);
    border-color: rgb(30 41 59);
    box-shadow: none;
  }

  .hs-head {
    padding: 18px 20px;
    border-bottom: 1px solid rgb(226 232 240);
    background: rgb(248 250 252);
  }
  .dark .hs-head {
    border-color: rgb(30 41 59);
    background: rgba(2, 6, 23, .35);
  }

  .hs-body { padding: 20px; }

  .hs-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: rgb(15 23 42);
    margin-bottom: 6px;
  }
  .dark .hs-label { color: rgb(226 232 240); }

  .hs-help {
    font-size: 12px;
    color: rgb(100 116 139);
    margin-top: 6px;
  }
  .dark .hs-help { color: rgb(148 163 184); }

  .hs-input {
    width: 100%;
    border: 1px solid rgb(226 232 240);
    border-radius: 12px;
    padding: 10px 12px;
    font-size: 14px;
    background: #fff;
    color: rgb(15 23 42);
    outline: none;
    transition: box-shadow .15s, border-color .15s;
  }
  .hs-input:focus {
    border-color: rgb(56 189 248);
    box-shadow: 0 0 0 4px rgba(56, 189, 248, .15);
  }
  .dark .hs-input {
    background: rgba(2, 6, 23, .35);
    border-color: rgb(30 41 59);
    color: rgb(226 232 240);
  }
  .dark .hs-input:focus {
    border-color: rgba(56, 189, 248, .7);
    box-shadow: 0 0 0 4px rgba(56, 189, 248, .18);
  }

  .hs-error {
    margin-top: 6px;
    font-size: 12px;
    color: rgb(225 29 72);
  }

  .hs-switch {
    display: flex;
    align-items: center;
    gap: 10px;
    user-select: none;
  }
  .hs-toggle {
    appearance: none;
    width: 44px;
    height: 26px;
    border-radius: 999px;
    background: rgb(226 232 240);
    border: 1px solid rgb(226 232 240);
    position: relative;
    outline: none;
    cursor: pointer;
    transition: background .15s, border-color .15s;
  }
  .hs-toggle::after {
    content: "";
    position: absolute;
    top: 3px;
    left: 3px;
    width: 18px;
    height: 18px;
    border-radius: 999px;
    background: #fff;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .18);
    transition: transform .15s;
  }
  .hs-toggle:checked {
    background: rgb(16 185 129);
    border-color: rgb(16 185 129);
  }
  .hs-toggle:checked::after {
    transform: translateX(18px);
  }
  .dark .hs-toggle {
    background: rgba(148, 163, 184, .18);
    border-color: rgb(30 41 59);
  }

  .hs-switch-label {
    font-size: 14px;
    font-weight: 600;
    color: rgb(15 23 42);
  }
  .dark .hs-switch-label { color: rgb(226 232 240); }

  .hs-actions {
    display: flex;
    gap: 10px;
    padding: 16px 20px;
    border-top: 1px solid rgb(226 232 240);
    background: rgb(248 250 252);
  }
  .dark .hs-actions {
    border-color: rgb(30 41 59);
    background: rgba(2, 6, 23, .35);
  }
</style>

<div class="p-6 sm:p-8 lg:p-10 max-w-3xl mx-auto">
  <div class="flex items-end justify-between gap-4 mb-5">
    <div class="min-w-0">
      <div class="flex items-center gap-3">
        <div class="h-2.5 w-2.5 rounded-full bg-slate-900 dark:bg-slate-100"></div>
        <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">
          {{ $category->exists ? 'Modifier une catégorie' : 'Créer une catégorie' }}
        </h1>
      </div>
      <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
        Définis le nom, la description et l’ordre d’affichage.
      </p>
    </div>

    <a class="btn rounded-full px-4 py-2" href="{{ route('admin.hostystats.categories.index') }}">
      Retour
    </a>
  </div>

  <div class="hs-card">
    <div class="hs-head">
      <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
        Informations de la catégorie
      </div>
      <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
        Les champs marqués requis doivent être remplis.
      </div>
    </div>

    <form method="POST" action="{{ $category->exists ? route('admin.hostystats.categories.update', $category) : route('admin.hostystats.categories.store') }}">
      @csrf
      @if($category->exists) @method('PUT') @endif

      <div class="hs-body">
        <div class="mb-4">
          <label class="hs-label">Nom <span class="text-rose-600">*</span></label>
          <input class="hs-input input" name="name" value="{{ old('name', $category->name) }}" required>
          @error('name') <div class="hs-error">{{ $message }}</div> @enderror
          <div class="hs-help">Ex : “Infrastructure”, “Jeux”, “Réseau”, “Sites web”.</div>
        </div>

        <div class="mb-4">
          <label class="hs-label">Description</label>
          <textarea class="hs-input input" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
          @error('description') <div class="hs-error">{{ $message }}</div> @enderror
          <div class="hs-help">Optionnel. Affiché sous le titre côté client.</div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="hs-label">Position</label>
            <input class="hs-input input" type="number" name="position" value="{{ old('position', $category->position ?? 0) }}">
            @error('position') <div class="hs-error">{{ $message }}</div> @enderror
            <div class="hs-help">Plus petit = plus haut dans la liste.</div>
          </div>

          <div class="flex items-end">
            <div class="w-full">
              <label class="hs-label">Activation</label>
              <input type="hidden" name="is_active" value="0">
              <div class="hs-switch">
                <input
                  class="hs-toggle"
                  type="checkbox"
                  name="is_active"
                  value="1"
                  {{ old('is_active', $category->exists ? $category->is_active : true) ? 'checked' : '' }}
                >
                <span class="hs-switch-label">Catégorie active</span>
              </div>
              @error('is_active') <div class="hs-error">{{ $message }}</div> @enderror
              <div class="hs-help">Si désactivée, elle n’apparaît pas côté client.</div>
            </div>
          </div>
        </div>
      </div>

      <div class="hs-actions">
        <button class="btn btn-primary rounded-full px-5 py-2">
          Enregistrer
        </button>
        <a class="btn rounded-full px-5 py-2" href="{{ route('admin.hostystats.categories.index') }}">
          Annuler
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
