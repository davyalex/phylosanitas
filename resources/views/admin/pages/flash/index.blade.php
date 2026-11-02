@extends('admin.layout')
@section('title', 'Infos flash')

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Infos flash</h1>
        <p class="page-bar__sub">Messages courts affichés dans le bandeau défilant en haut de toutes les pages du site. Les infos urgentes passent en premier et colorent le bandeau en rouge.</p>
    </div>
</div>
@endsection

@section('content')
@php
    $statusLabels = ['live' => 'En ligne', 'scheduled' => 'Programmée', 'expired' => 'Expirée', 'off' => 'Désactivée'];
@endphp

<div class="kpis kpis--3">
    <div class="kpi"><div class="kpi__top"><span class="kpi__label">En ligne</span><span class="kpi__icon kpi__icon--green"><i class="bi bi-broadcast"></i></span></div><div class="kpi__value">{{ $stats['live'] }}</div></div>
    <div class="kpi"><div class="kpi__top"><span class="kpi__label">Programmées</span><span class="kpi__icon"><i class="bi bi-calendar-event"></i></span></div><div class="kpi__value">{{ $stats['scheduled'] }}</div></div>
    <div class="kpi"><div class="kpi__top"><span class="kpi__label">Expirées</span><span class="kpi__icon kpi__icon--amber"><i class="bi bi-hourglass-bottom"></i></span></div><div class="kpi__value">{{ $stats['expired'] }}</div></div>
</div>

<div class="row g-4">
    {{-- Formulaire --}}
    <div class="col-xl-5">
        <form action="{{ route('flash.store') }}" method="POST" class="fsec" data-once id="flash-form">
            @csrf
            <h2 class="fsec__title"><i class="bi bi-plus-circle"></i>Nouvelle info flash</h2>

            <div class="mb-3">
                <label for="flash-message" class="form-label">Message <span class="req">*</span> <span class="field-count" data-count-for="flash-message"></span></label>
                <textarea id="flash-message" name="message" rows="2" class="form-control @error('message') is-invalid @enderror" data-count="255" maxlength="255" required placeholder="Ex. : Campagne de vaccination gratuite contre la polio du 10 au 15 mai">{{ old('message') }}</textarea>
                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <span class="form-label d-block">Niveau</span>
                <div class="seg">
                    @foreach(\App\Models\FlashInfo::NIVEAUX as $key => $label)
                        <input type="radio" name="niveau" id="niv-{{ $key }}" value="{{ $key }}" {{ old('niveau', 'info') === $key ? 'checked' : '' }}>
                        <label for="niv-{{ $key }}"><strong><span class="level level--{{ $key }}">{{ $label }}</span></strong></label>
                    @endforeach
                </div>
            </div>

            <div class="mb-3">
                <label for="flash-lien" class="form-label">Lien (facultatif)</label>
                <input id="flash-lien" type="url" name="lien" value="{{ old('lien') }}" class="form-control @error('lien') is-invalid @enderror" placeholder="https://… ou lien vers un article du site">
                <div class="form-text">Le message devient cliquable.</div>
                @error('lien')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label for="flash-debut" class="form-label">Début de diffusion</label>
                    <input id="flash-debut" type="datetime-local" name="debut_at" value="{{ old('debut_at') }}" class="form-control">
                    <div class="form-text">Vide = immédiatement</div>
                </div>
                <div class="col-sm-6">
                    <label for="flash-fin" class="form-label">Fin de diffusion</label>
                    <input id="flash-fin" type="datetime-local" name="fin_at" value="{{ old('fin_at') }}" class="form-control @error('fin_at') is-invalid @enderror">
                    <div class="form-text">Vide = sans limite</div>
                    @error('fin_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <span class="form-label d-block">Aperçu</span>
                <div class="flash-preview" id="flash-preview">
                    <span class="flash-preview__label">Infos flash</span>
                    <span class="flash-preview__text" id="flash-preview-text">Votre message apparaîtra ici</span>
                </div>
            </div>

            <input type="hidden" name="actif" value="1">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send me-2"></i>Publier l'info flash</button>
        </form>
    </div>

    {{-- Liste --}}
    <div class="col-xl-7">
        <div class="panel-a">
            <div class="panel-a__head">
                <h2 class="panel-a__title"><i class="bi bi-list-ul"></i>Toutes les infos flash</h2>
                <span class="text-muted small">{{ $flashes->count() }} au total</span>
            </div>

            @if($flashes->isEmpty())
                <div class="empty-admin">
                    <i class="bi bi-lightning-charge"></i>
                    <h3>Aucune info flash</h3>
                    <p>Publiez votre première info : elle apparaîtra immédiatement en haut du site.</p>
                </div>
            @else
                <div class="flash-list">
                    @foreach($flashes as $f)
                        <div class="flash-row {{ $f->statut !== 'live' ? 'is-dim' : '' }}">
                            <div>
                                <p class="flash-row__msg">{{ $f->message }}</p>
                                <div class="flash-row__meta">
                                    <span class="status status--{{ $f->statut }}">{{ $statusLabels[$f->statut] }}</span>
                                    <span class="level level--{{ $f->niveau }}">{{ \App\Models\FlashInfo::NIVEAUX[$f->niveau] }}</span>
                                    @if($f->debut_at || $f->fin_at)
                                        <span><i class="bi bi-calendar3 me-1"></i>{{ $f->debut_at ? $f->debut_at->format('d/m H:i') : 'Maintenant' }} → {{ $f->fin_at ? $f->fin_at->format('d/m H:i') : 'sans fin' }}</span>
                                    @endif
                                    @if($f->lien)<a href="{{ $f->lien }}" target="_blank" rel="noopener"><i class="bi bi-link-45deg"></i>{{ $f->lien }}</a>@endif
                                </div>
                            </div>
                            <div class="row-actions">
                                <form action="{{ route('flash.toggle', $f) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $f->actif ? 'btn-outline-warning' : 'btn-outline-success' }} icon-action" title="{{ $f->actif ? 'Désactiver' : 'Activer' }}" aria-label="{{ $f->actif ? 'Désactiver' : 'Activer' }}">
                                        <i class="bi bi-{{ $f->actif ? 'pause-fill' : 'play-fill' }}"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-primary icon-action" data-bs-toggle="modal" data-bs-target="#editFlash{{ $f->id }}" title="Modifier" aria-label="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('flash.destroy', $f) }}" method="POST" data-confirm="Supprimer définitivement cette info flash ?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger icon-action" title="Supprimer" aria-label="Supprimer"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>

                        {{-- Modal modification --}}
                        <div class="modal fade" id="editFlash{{ $f->id }}" tabindex="-1" aria-labelledby="editFlashLabel{{ $f->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form class="modal-content" action="{{ route('flash.update', $f) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editFlashLabel{{ $f->id }}">Modifier l'info flash</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label" for="m-msg-{{ $f->id }}">Message <span class="req">*</span></label>
                                            <textarea id="m-msg-{{ $f->id }}" name="message" rows="2" class="form-control" maxlength="255" required>{{ $f->message }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <span class="form-label d-block">Niveau</span>
                                            <div class="seg">
                                                @foreach(\App\Models\FlashInfo::NIVEAUX as $key => $label)
                                                    <input type="radio" name="niveau" id="m-niv-{{ $f->id }}-{{ $key }}" value="{{ $key }}" {{ $f->niveau === $key ? 'checked' : '' }}>
                                                    <label for="m-niv-{{ $f->id }}-{{ $key }}"><strong><span class="level level--{{ $key }}">{{ $label }}</span></strong></label>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="m-lien-{{ $f->id }}">Lien</label>
                                            <input id="m-lien-{{ $f->id }}" type="url" name="lien" value="{{ $f->lien }}" class="form-control">
                                        </div>
                                        <div class="row g-3 mb-3">
                                            <div class="col-sm-6">
                                                <label class="form-label" for="m-debut-{{ $f->id }}">Début</label>
                                                <input id="m-debut-{{ $f->id }}" type="datetime-local" name="debut_at" value="{{ optional($f->debut_at)->format('Y-m-d\TH:i') }}" class="form-control">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label" for="m-fin-{{ $f->id }}">Fin</label>
                                                <input id="m-fin-{{ $f->id }}" type="datetime-local" name="fin_at" value="{{ optional($f->fin_at)->format('Y-m-d\TH:i') }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="switch-row">
                                            <div class="switch-row__text"><strong>Active</strong><span>Affichée sur le site pendant sa période</span></div>
                                            <input type="hidden" name="actif" value="0">
                                            <input class="form-check-input" type="checkbox" role="switch" name="actif" value="1" {{ $f->actif ? 'checked' : '' }} aria-label="Active">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        var msg = document.getElementById('flash-message');
        var text = document.getElementById('flash-preview-text');
        var box = document.getElementById('flash-preview');
        var form = document.getElementById('flash-form');
        if (!msg) return;
        var update = function () {
            text.textContent = msg.value.trim() || 'Votre message apparaîtra ici';
            var lvl = form.querySelector('[name=niveau]:checked');
            box.classList.toggle('flash-preview--urgent', lvl && lvl.value === 'urgent');
            box.querySelector('.flash-preview__label').textContent = lvl && lvl.value === 'urgent' ? 'Urgent' : 'Infos flash';
        };
        msg.addEventListener('input', update);
        form.addEventListener('change', update);
        update();
    })();
</script>
@endpush
