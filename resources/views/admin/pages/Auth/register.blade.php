@extends('admin.layout')
@section('title', 'Utilisateurs')

@section('content')
<section class="section">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-people-fill me-2 text-primary"></i>Utilisateurs</h4>
            <p class="text-muted small mb-0">{{ $user->count() }} compte(s) enregistré(s)</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddUser">
            <i class="bi bi-person-plus-fill me-1"></i>Nouvel utilisateur
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover datatable align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Utilisateur</th>
                        <th>Contact</th>
                        <th>Rôle</th>
                        <th class="text-center">Articles</th>
                        <th class="text-center">Statut</th>
                        <th>Depuis</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user as $key => $item)
                        <tr class="{{ $item->active === 'no' ? 'table-secondary opacity-75' : '' }}">
                            <td class="ps-3 text-muted">{{ ++$key }}</td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar">{{ strtoupper(substr($item->name, 0, 2)) }}</div>
                                    <div>
                                        <span class="fw-semibold d-block">{{ $item->name }}</span>
                                        <small class="text-muted">{{ $item->email ?: 'Sans email' }}</small>
                                    </div>
                                </div>
                            </td>

                            <td class="text-muted">{{ $item->phone }}</td>

                            <td>
                                @if($item->roles->isNotEmpty())
                                    @php
                                        $roleName = $item->roles->first()->name;
                                        $roleColor = match(strtolower($roleName)) {
                                            'administrateur' => 'danger',
                                            'webmaster'      => 'warning',
                                            default          => 'primary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $roleColor }}">{{ $roleName }}</span>
                                @else
                                    <span class="badge bg-secondary">Aucun rôle</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $item->posts_count }}</span>
                            </td>

                            <td class="text-center">
                                @if($item->active === 'yes')
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Actif</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i>Bloqué</span>
                                @endif
                            </td>

                            <td class="text-muted small">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</td>

                            <td class="text-end pe-3">
                                <div class="d-flex gap-1 justify-content-end">
                                    {{-- Voir profil --}}
                                    <a href="{{ route('user.profil', $item->id) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Voir le profil">
                                        <i class="bi bi-person"></i>
                                    </a>

                                    @if($item->active === 'yes')
                                        {{-- Modifier --}}
                                        <button class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEdit{{ $item->id }}"
                                                title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        {{-- Bloquer --}}
                                        <a href="{{ route('user.lock', $item->id) }}"
                                           class="btn btn-sm btn-warning"
                                           title="Bloquer l'accès">
                                            <i class="bi bi-lock-fill"></i>
                                        </a>
                                    @else
                                        {{-- Débloquer --}}
                                        <a href="{{ route('user.unlock', $item->id) }}"
                                           class="btn btn-sm btn-success"
                                           title="Débloquer l'accès">
                                            <i class="bi bi-unlock-fill"></i>
                                        </a>
                                    @endif

                                    {{-- Supprimer --}}
                                    @if($item->id !== auth()->id())
                                        <form action="{{ route('user.delete', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmDelete{{ $item->id }}"
                                                    title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @include('admin.partials.deleteConfirm')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Modal édition --}}
                        <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Modifier — {{ $item->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('user.update', $item->id) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" value="{{ $item->name }}" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Téléphone <span class="text-danger">*</span></label>
                                                    <input type="tel" name="phone" value="{{ $item->phone }}" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Email</label>
                                                    <input type="email" name="email" value="{{ $item->email }}" class="form-control">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Rôle</label>
                                                    <select name="role" class="form-select">
                                                        <option value="">— Conserver le rôle actuel —</option>
                                                        @foreach($role as $r)
                                                            <option value="{{ $r->name }}"
                                                                    {{ $item->role === $r->name ? 'selected' : '' }}>
                                                                {{ $r->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Nouveau mot de passe</label>
                                                    <input type="password" name="password" class="form-control"
                                                           placeholder="Laisser vide pour ne pas modifier">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check-lg me-1"></i>Enregistrer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>
                                Aucun utilisateur.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</section>

{{-- Modal ajout utilisateur --}}
<div class="modal fade" id="modalAddUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Créer un utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('user.store') }}" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nom complet <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   class="form-control @error('phone') is-invalid @enderror" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Rôle <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="" disabled selected>— Sélectionner un rôle —</option>
                                @foreach($role as $r)
                                    <option value="{{ $r->name }}" {{ old('role') === $r->name ? 'selected' : '' }}>
                                        {{ $r->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mot de passe <span class="text-danger">*</span></label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus-fill me-1"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.user-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0066CC, #00A86B);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 700;
    flex-shrink: 0;
}
</style>

<script>
    @if($errors->any())
        new bootstrap.Modal(document.getElementById('modalAddUser')).show();
    @endif
</script>
@endsection
