@extends('admin.layout')
@section('title', 'Mon profil')

@section('content')
<section class="section">
<div class="row g-4">

    {{-- Carte profil --}}
    <div class="col-xl-4">
        <div class="card text-center">
            <div class="card-body pt-5 pb-4">
                <div class="profile-avatar mx-auto mb-3">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-2">
                    @if($user->roles->isNotEmpty())
                        @php
                            $roleName  = $user->roles->first()->name;
                            $roleColor = match(strtolower($roleName)) {
                                'administrateur' => 'danger',
                                'webmaster'      => 'warning',
                                default          => 'primary',
                            };
                        @endphp
                        <span class="badge bg-{{ $roleColor }} px-3">{{ $roleName }}</span>
                    @else
                        <span class="badge bg-secondary">Aucun rôle</span>
                    @endif
                </p>
                <p class="text-muted small mb-0">
                    <i class="bi bi-telephone me-1"></i>{{ $user->phone }}
                </p>
                @if($user->email)
                    <p class="text-muted small mb-0">
                        <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                    </p>
                @endif

                <div class="row g-0 mt-4 pt-3 border-top text-center">
                    <div class="col-6 border-end">
                        <p class="mb-0 fw-bold fs-5">{{ $user->posts_count }}</p>
                        <small class="text-muted">Articles</small>
                    </div>
                    <div class="col-6">
                        <p class="mb-0 fw-bold fs-5">{{ $user->roles->count() }}</p>
                        <small class="text-muted">Rôle(s)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulaires --}}
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body pt-3">
                <ul class="nav nav-tabs nav-tabs-bordered mb-3">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-info">
                            <i class="bi bi-person me-1"></i>Informations
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-edit">
                            <i class="bi bi-pencil me-1"></i>Modifier
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-password">
                            <i class="bi bi-key me-1"></i>Mot de passe
                        </button>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- Onglet infos --}}
                    <div class="tab-pane fade show active" id="tab-info">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th class="text-muted fw-normal" style="width:35%">Nom complet</th>
                                    <td class="fw-semibold">{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Téléphone</th>
                                    <td>{{ $user->phone }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Email</th>
                                    <td>{{ $user->email ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Rôle</th>
                                    <td>{{ $user->roles->first()?->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Statut</th>
                                    <td>
                                        @if($user->active === 'yes')
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-danger">Bloqué</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">Membre depuis</th>
                                    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Onglet modifier --}}
                    <div class="tab-pane fade" id="tab-edit">
                        <form method="POST" action="{{ route('user.update', $user->id) }}" novalidate>
                            @csrf
                            {{-- Champ rôle caché pour conserver le rôle actuel --}}
                            <input type="hidden" name="role" value="{{ $user->roles->first()?->name }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                           class="form-control @error('name') is-invalid @enderror" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Téléphone <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                                           class="form-control @error('phone') is-invalid @enderror" required>
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                           class="form-control @error('email') is-invalid @enderror">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-check-lg me-1"></i>Mettre à jour
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Onglet mot de passe --}}
                    <div class="tab-pane fade" id="tab-password">
                        <form method="POST" action="{{ route('user.newpassword', $user->id) }}" novalidate>
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Ancien mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror" required>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Nouveau mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" name="newpassword"
                                           class="form-control @error('newpassword') is-invalid @enderror"
                                           minlength="6" required>
                                    @error('newpassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-key me-1"></i>Changer le mot de passe
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
</section>

<style>
.profile-avatar {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0066CC, #00A86B);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; font-weight: 700;
}
</style>
@endsection
