@extends('admin.layouts.admin')

@section('content')

<style>
.hs-wrap{
  max-width: 72rem;
  margin: 0 auto;
  padding: 1.5rem;
}
@media (min-width: 640px){ .hs-wrap{ padding: 2rem; } }
@media (min-width: 1024px){ .hs-wrap{ padding: 2.5rem; } }

.hs-head{
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
@media (min-width: 640px){
  .hs-head{
    flex-direction: row;
    align-items: flex-end;
    justify-content: space-between;
  }
}
.hs-title-row{
  display:flex;
  align-items:center;
  gap:.75rem;
  min-width: 0;
}
.hs-dot{
  width: .625rem;
  height: .625rem;
  border-radius: 999px;
  background: #0f172a;
}
.dark .hs-dot{ background:#f1f5f9; }

.hs-title{
  font-size: 1.25rem;
  line-height: 1.75rem;
  font-weight: 800;
  color: #0f172a;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.dark .hs-title{ color:#f1f5f9; }

.hs-subtitle{
  margin-top: .25rem;
  font-size: .875rem;
  line-height: 1.25rem;
  color: #475569;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.dark .hs-subtitle{ color:#cbd5e1; }

.hs-card{
  background: #ffffff;
  border: 1px solid rgba(15, 23, 42, .12);
  border-radius: 1rem;
  box-shadow: 0 1px 2px rgba(15, 23, 42, .06);
  overflow: hidden;
}
.dark .hs-card{
  background: #0f172a;
  border-color: rgba(148, 163, 184, .18);
}

.hs-table-wrap{
  overflow-x: auto;
}
.hs-table{
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}
.hs-table thead th{
  background: #f8fafc;
  border-bottom: 1px solid rgba(15, 23, 42, .10);
  color: #334155;
  font-size: .875rem;
  font-weight: 700;
  text-align: left;
  padding: .75rem 1.25rem;
  white-space: nowrap;
}
.dark .hs-table thead th{
  background: rgba(2, 6, 23, .35);
  border-bottom-color: rgba(148, 163, 184, .18);
  color: #cbd5e1;
}

.hs-table tbody td{
  padding: 1rem 1.25rem;
  font-size: .875rem;
  color: #0f172a;
  border-bottom: 1px solid rgba(15, 23, 42, .08);
  vertical-align: middle;
}
.dark .hs-table tbody td{
  color: #f1f5f9;
  border-bottom-color: rgba(148, 163, 184, .14);
}

.hs-table tbody tr:hover td{
  background: rgba(241, 245, 249, .7);
}
.dark .hs-table tbody tr:hover td{
  background: rgba(30, 41, 59, .35);
}

.hs-name{
  font-weight: 700;
  max-width: 260px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.hs-pill{
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  padding: .25rem .55rem;
  border-radius: 999px;
  font-size: .75rem;
  font-weight: 800;
  letter-spacing: .01em;
  border: 1px solid transparent;
}
.hs-pill--active{
  background: rgba(16,185,129,.12);
  color: #065f46;
  border-color: rgba(16,185,129,.25);
}
.dark .hs-pill--active{
  background: rgba(16,185,129,.18);
  color: #a7f3d0;
  border-color: rgba(16,185,129,.28);
}
.hs-muted{
  color: #64748b;
}
.dark .hs-muted{ color:#94a3b8; }

.hs-actions{
  display: inline-flex;
  flex-wrap: wrap;
  justify-content: flex-end;
  gap: .5rem;
}

.hs-btn-round{
  border-radius: 999px !important;
  padding: .5rem 1rem !important;
}
.hs-btn-sm-round{
  border-radius: 999px !important;
  padding: .35rem .85rem !important;
}

.hs-btn-danger{
  background: #dc2626;
  color: #fff;
  border-radius: 999px;
  padding: .35rem .85rem;
  transition: background .15s ease, transform .02s ease;
}
.hs-btn-danger:hover{ background:#b91c1c; }
.hs-btn-danger:active{ transform: translateY(1px); }
.dark .hs-btn-danger{ background:#ef4444; }
.dark .hs-btn-danger:hover{ background:#dc2626; }

.hs-footer{
  margin-top: 1.5rem;
}
</style>

<div class="hs-wrap">

  {{-- Header --}}
  <div class="hs-head">
    <div class="min-w-0">
      <div class="hs-title-row">
        <span class="hs-dot" aria-hidden="true"></span>
        <h1 class="hs-title">HostyStats • Catégories</h1>
      </div>
      <p class="hs-subtitle">Gestion des catégories de sondes.</p>
    </div>

    <a class="btn btn-primary hs-btn-round"
       href="{{ route('admin.hostystats.categories.create') }}">
      Créer une catégorie
    </a>
  </div>

  {{-- Card + Table --}}
  <div class="hs-card">
    <div class="hs-table-wrap">
      <table class="hs-table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Position</th>
            <th>Actif</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>

        <tbody>
          @forelse($categories as $c)
            @php $active = (bool) $c->is_active; @endphp

            <tr>
              <td>
                <div class="hs-name">{{ $c->name }}</div>
              </td>

              <td>{{ $c->position }}</td>

              <td>
                @if($active)
                  <span class="hs-pill hs-pill--active">Oui</span>
                @else
                  <span class="hs-muted">Non</span>
                @endif
              </td>

              <td style="text-align:right;">
                <div class="hs-actions">
                  <a class="btn btn-sm hs-btn-sm-round"
                     href="{{ route('admin.hostystats.categories.edit', $c) }}">
                    Modifier
                  </a>

                  <form class="inline" method="POST" action="{{ route('admin.hostystats.categories.destroy', $c) }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm hs-btn-danger" onclick="return confirm('Supprimer cette catégorie ?')">
                      Supprimer
                    </button>
                  </form>
                </div>
              </td>
            </tr>

          @empty
            <tr>
              <td colspan="4" style="padding:1.25rem;">
                <span class="hs-muted">Aucune catégorie configurée.</span>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Footer action --}}
  <div class="hs-footer">
    <a class="btn hs-btn-round" href="{{ route('admin.hostystats.monitors.index') }}">
      Gérer les sondes
    </a>
  </div>

</div>

@endsection
