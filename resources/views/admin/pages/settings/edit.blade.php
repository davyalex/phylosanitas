@extends('admin.layout')
@section('title', 'Configuration')

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Configuration du site</h1>
        <p class="page-bar__sub">Identité, coordonnées et réseaux sociaux affichés sur tout le site (en-tête, pied de page, page Contact, Google). Les champs vides ne sont pas affichés.</p>
    </div>
</div>
@endsection

@section('content')
@php
    $v = fn($k, $d = '') => old($k, $s[$k] ?? $d);
    $logoUrl = !empty($s['logo']) ? Storage::disk('public')->url($s['logo']) : asset('assets_site/img/logo/logo_tp.png');
@endphp

<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" data-once>
    @csrf @method('PUT')

    <div class="form-shell">
        <div class="d-flex flex-column gap-3">

            {{-- Identité --}}
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-stars"></i>Identité</h2>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="s-nom" class="form-label">Nom du site <span class="req">*</span></label>
                        <input id="s-nom" name="site_nom" value="{{ $v('site_nom', config('app.name')) }}" class="form-control @error('site_nom') is-invalid @enderror" maxlength="60" required>
                        @error('site_nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-7">
                        <label for="s-slogan" class="form-label">Slogan <span class="field-count" data-count-for="s-slogan"></span></label>
                        <input id="s-slogan" name="slogan" value="{{ $v('slogan') }}" class="form-control" data-count="60" maxlength="120" placeholder="Votre santé, notre priorité">
                        <div class="form-text">Grand titre de l'accueil et titre de la page d'accueil dans Google.</div>
                    </div>
                    <div class="col-12">
                        <label for="s-desc" class="form-label">Présentation courte <span class="field-count" data-count-for="s-desc"></span></label>
                        <textarea id="s-desc" name="description" rows="2" class="form-control" data-count="200" maxlength="300">{{ $v('description') }}</textarea>
                        <div class="form-text">Affichée dans le pied de page et utilisée comme description par défaut pour Google.</div>
                    </div>
                </div>
            </section>

            {{-- Téléphones --}}
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-telephone"></i>Téléphones</h2>
                <p class="fsec__hint">Jusqu'à 3 numéros. Le premier est affiché dans la barre du haut.</p>
                @foreach([1, 2, 3] as $i)
                    <div class="row g-2 mb-2 align-items-end">
                        <div class="col-sm-4">
                            <label for="s-tl{{ $i }}" class="form-label">Libellé {{ $i }}</label>
                            <input id="s-tl{{ $i }}" name="telephone_{{ $i }}_label" value="{{ $v("telephone_{$i}_label") }}" class="form-control" maxlength="40" placeholder="{{ ['', 'Standard', 'Urgences', 'Rédaction'][$i] }}">
                        </div>
                        <div class="col-sm-8">
                            <label for="s-t{{ $i }}" class="form-label">Numéro {{ $i }}</label>
                            <input id="s-t{{ $i }}" type="tel" name="telephone_{{ $i }}" value="{{ $v("telephone_$i") }}" class="form-control @error("telephone_$i") is-invalid @enderror" placeholder="(+225) 01 02 03 04 05">
                            @error("telephone_$i")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                @endforeach
                <div class="row g-2 mt-1">
                    <div class="col-sm-8 offset-sm-4">
                        <label for="s-wa" class="form-label"><i class="bi bi-whatsapp text-success me-1"></i>WhatsApp</label>
                        <input id="s-wa" type="tel" name="whatsapp" value="{{ $v('whatsapp') }}" class="form-control @error('whatsapp') is-invalid @enderror" placeholder="+225 07 00 00 00 00">
                        <div class="form-text">Ajoute un bouton « Écrire sur WhatsApp » sur la page Contact.</div>
                        @error('whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </section>

            {{-- Emails --}}
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-envelope"></i>Adresses email</h2>
                <p class="fsec__hint">Jusqu'à 3 adresses (ex. contact, rédaction, partenariats).</p>
                @foreach([1, 2, 3] as $i)
                    <div class="row g-2 mb-2 align-items-end">
                        <div class="col-sm-4">
                            <label for="s-el{{ $i }}" class="form-label">Libellé {{ $i }}</label>
                            <input id="s-el{{ $i }}" name="email_{{ $i }}_label" value="{{ $v("email_{$i}_label") }}" class="form-control" maxlength="40" placeholder="{{ ['', 'Contact', 'Rédaction', 'Partenariats'][$i] }}">
                        </div>
                        <div class="col-sm-8">
                            <label for="s-e{{ $i }}" class="form-label">Email {{ $i }}</label>
                            <input id="s-e{{ $i }}" type="email" name="email_{{ $i }}" value="{{ $v("email_$i") }}" class="form-control @error("email_$i") is-invalid @enderror" placeholder="contact@exemple.com">
                            @error("email_$i")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                @endforeach
            </section>

            {{-- Adresse --}}
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-geo-alt"></i>Adresse & horaires</h2>
                <div class="row g-3">
                    <div class="col-12">
                        <label for="s-adr" class="form-label">Adresse affichée</label>
                        <input id="s-adr" name="adresse" value="{{ $v('adresse') }}" class="form-control" maxlength="200" placeholder="Cocody, Abidjan, Côte d'Ivoire">
                    </div>
                    <div class="col-sm-8">
                        <label for="s-ville" class="form-label">Ville</label>
                        <input id="s-ville" name="ville" value="{{ $v('ville') }}" class="form-control" maxlength="80">
                    </div>
                    <div class="col-sm-4">
                        <label for="s-pays" class="form-label">Code pays</label>
                        <input id="s-pays" name="pays_code" value="{{ $v('pays_code', 'CI') }}" class="form-control text-uppercase @error('pays_code') is-invalid @enderror" maxlength="2" placeholder="CI">
                        @error('pays_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="s-hor" class="form-label">Horaires</label>
                        <input id="s-hor" name="horaires" value="{{ $v('horaires') }}" class="form-control" maxlength="200" placeholder="Du lundi au vendredi, 8 h – 17 h">
                    </div>
                </div>
            </section>

            {{-- Réseaux --}}
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-share"></i>Réseaux sociaux</h2>
                <p class="fsec__hint">Seuls les réseaux renseignés apparaissent sur le site.</p>
                <div class="row g-3">
                    @foreach(\App\Models\Setting::SOCIALS as $key => $meta)
                        <div class="col-md-6">
                            <label for="s-{{ $key }}" class="form-label"><i class="bi bi-{{ $meta['icon'] }} me-1"></i>{{ $meta['label'] }}</label>
                            <input id="s-{{ $key }}" type="url" name="{{ $key }}" value="{{ $v($key) }}" class="form-control @error($key) is-invalid @enderror" placeholder="https://…">
                            @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Avertissement --}}
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-info-circle"></i>Avertissement médical</h2>
                <p class="fsec__hint">Affiché sous chaque article et dans le pied de page. Laissez vide pour le masquer.</p>
                <textarea name="avertissement" rows="3" class="form-control" maxlength="500" aria-label="Avertissement médical">{{ $v('avertissement') }}</textarea>

                <div class="mt-3" style="max-width:260px">
                    <label for="urgence" class="form-label">Numéro d'urgence</label>
                    <input id="urgence" type="text" name="urgence" class="form-control @error('urgence') is-invalid @enderror" value="{{ $v('urgence') }}" placeholder="185" inputmode="tel" maxlength="20">
                    <div class="form-text">Affiché dans « Quand consulter ? » (accueil et fin d'article). Vide = 185 (SAMU).</div>
                    @error('urgence')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </section>
        </div>

        {{-- Colonne latérale --}}
        <aside class="form-side">
            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-image"></i>Logo</h2>
                <div class="dropzone has-image" data-fit="contain" style="min-height:120px">
                    <img src="{{ $logoUrl }}" alt="Logo actuel" class="dropzone__preview dropzone__preview--contain" style="padding:12px">
                    <span class="dropzone__empty"></span>
                    <span class="dropzone__change">Changer le logo</span>
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" aria-label="Nouveau logo">
                </div>
                @error('logo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                <div class="form-text mt-2">PNG transparent ou SVG conseillé, format horizontal (environ 360 × 96 px), 2 Mo max.</div>
                @if(!empty($s['logo']))
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="logo_reset" value="1" id="logo-reset">
                        <label class="form-check-label small" for="logo-reset">Revenir au logo d'origine</label>
                    </div>
                @endif
            </section>

            <section class="fsec">
                <h2 class="fsec__title"><i class="bi bi-people"></i>Compteur de visites</h2>
                <div class="switch-row">
                    <div class="switch-row__text"><strong>Afficher sur le site</strong><span>Barre du haut et pied de page</span></div>
                    <input class="form-check-input" type="checkbox" role="switch" name="afficher_visites" value="1" {{ old('afficher_visites', $s['afficher_visites'] ?? 1) ? 'checked' : '' }} aria-label="Afficher le compteur de visites">
                </div>
                <div class="mt-2">
                    <label for="s-dep" class="form-label">Visites antérieures</label>
                    <input id="s-dep" type="number" min="0" name="visites_depart" value="{{ $v('visites_depart', 0) }}" class="form-control @error('visites_depart') is-invalid @enderror">
                    <div class="form-text">Ajoutées au total affiché, par exemple le chiffre de Google Analytics avant l'activation du compteur.</div>
                    @error('visites_depart')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-between mt-3 small">
                    <span class="text-muted">Mesurées par le site</span><strong>{{ number_format($visits['mesurees'] ?? 0, 0, ',', ' ') }}</strong>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Total affiché</span><strong>{{ number_format($visits['visites'], 0, ',', ' ') }}</strong>
                </div>
            </section>

        </aside>
    </div>

    <div class="form-footer">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Annuler</a>
        <div class="form-footer__end">
            <a href="{{ route('accueil') }}" target="_blank" rel="noopener" class="btn btn-link"><i class="bi bi-box-arrow-up-right me-1"></i>Voir le site</a>
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-1"></i>Enregistrer la configuration</button>
        </div>
    </div>
</form>
@endsection
