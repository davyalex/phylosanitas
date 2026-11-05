@extends('admin.layout')
@section('title', 'Notifications')

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Notifications</h1>
        <p class="page-bar__sub">Les lecteurs qui ont cliqué sur « Recevoir les nouveaux articles » reçoivent une notification sur leur téléphone ou leur ordinateur, même site fermé.</p>
    </div>
</div>
@endsection

@section('content')
@unless($actif)
    <div class="alert alert-danger d-flex gap-2"><i class="bi bi-exclamation-triangle mt-1"></i>
        <div>Les clés de sécurité des notifications n'ont pas pu être créées sur ce serveur (extension OpenSSL). Contactez le développeur.</div>
    </div>
@endunless

<div class="kpis kpis--3">
    <div class="kpi"><div class="kpi__top"><span class="kpi__label">Appareils abonnés</span><span class="kpi__icon kpi__icon--green"><i class="bi bi-phone"></i></span></div><div class="kpi__value">{{ number_format($abonnes, 0, ',', ' ') }}</div></div>
    <div class="kpi"><div class="kpi__top"><span class="kpi__label">Notifications envoyées</span><span class="kpi__icon"><i class="bi bi-bell"></i></span></div><div class="kpi__value">{{ $messages->count() }}</div><div class="kpi__meta">30 dernières</div></div>
    <div class="kpi"><div class="kpi__top"><span class="kpi__label">Envoi automatique</span><span class="kpi__icon kpi__icon--amber"><i class="bi bi-lightning-charge"></i></span></div><div class="kpi__value">{{ $auto ? 'Activé' : 'Désactivé' }}</div></div>
</div>

<div class="row g-4">
    <div class="col-xl-5">
        <form action="{{ route('push.envoyer') }}" method="POST" class="fsec mb-3" data-once data-confirm="Envoyer cette notification à {{ $abonnes }} appareil(s) ?" data-confirm-ok="Envoyer" data-confirm-tone="warning">
            @csrf
            <h2 class="fsec__title"><i class="bi bi-send"></i>Envoyer une notification</h2>
            <div class="mb-3">
                <label for="pn-titre" class="form-label">Titre <span class="req">*</span> <span class="field-count" data-count-for="pn-titre"></span></label>
                <input id="pn-titre" name="titre" value="{{ old('titre') }}" class="form-control @error('titre') is-invalid @enderror" maxlength="110" data-count="60" required placeholder="Ex. : Campagne de dépistage gratuite ce samedi">
            </div>
            <div class="mb-3">
                <label for="pn-corps" class="form-label">Texte</label>
                <textarea id="pn-corps" name="corps" rows="2" maxlength="200" class="form-control" placeholder="Une phrase. Elle s'affiche sous le titre.">{{ old('corps') }}</textarea>
            </div>
            <div class="mb-3">
                <label for="pn-url" class="form-label">Page ouverte au clic <span class="req">*</span></label>
                <input id="pn-url" name="url" value="{{ old('url', '/') }}" class="form-control @error('url') is-invalid @enderror" maxlength="500" list="pn-articles" required>
                <datalist id="pn-articles">
                    @foreach($derniers as $p)
                        <option value="/article/{{ $p->slug }}">{{ Str::limit($p->title, 70) }}</option>
                    @endforeach
                </datalist>
                <div class="form-text">/ pour l'accueil, ou choisissez un article récent dans la liste.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100" {{ $abonnes && $actif ? '' : 'disabled' }}><i class="bi bi-send me-1"></i>Envoyer maintenant</button>
            @unless($abonnes)<p class="small text-muted mt-2 mb-0">Aucun appareil abonné pour l'instant.</p>@endunless
        </form>

        <form action="{{ route('push.reglages') }}" method="POST" class="fsec">
            @csrf @method('PUT')
            <h2 class="fsec__title"><i class="bi bi-gear"></i>Réglage</h2>
            <div class="switch-row">
                <div class="switch-row__text"><strong>Notifier chaque nouvel article</strong><span>Envoi automatique à la première publication d'un article (pas pour les sondages)</span></div>
                <input class="form-check-input" type="checkbox" role="switch" name="push_auto" value="1" {{ $auto ? 'checked' : '' }} aria-label="Notifier chaque nouvel article" onchange="this.form.submit()">
            </div>
        </form>
    </div>

    <div class="col-xl-7">
        <div class="panel-a">
            <div class="panel-a__head"><h2 class="panel-a__title"><i class="bi bi-clock-history"></i>Historique</h2></div>
            @if($messages->isEmpty())
                <div class="empty-admin"><i class="bi bi-bell"></i><h3>Aucune notification envoyée</h3><p>Elles apparaîtront ici avec le nombre d'appareils atteints.</p></div>
            @else
                <ul class="mini-list">
                    @foreach($messages as $m)
                        <li>
                            <div class="mini-list__main">
                                <span class="mini-list__title">{{ $m->titre }}</span>
                                <span class="mini-list__sub">{{ $m->created_at->translatedFormat('d M Y à H:i') }} · {{ $m->post_id ? 'automatique' : 'manuelle' }} · <a href="{{ url($m->url) }}" target="_blank" rel="noopener">{{ Str::limit($m->url, 50) }}</a></span>
                            </div>
                            <span class="small text-nowrap"><strong>{{ $m->envoyes }}</strong> reçue{{ $m->envoyes > 1 ? 's' : '' }}@if($m->echecs)<span class="text-muted"> · {{ $m->echecs }} échec{{ $m->echecs > 1 ? 's' : '' }}</span>@endif</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
