@extends('site.layout')
@section('title', 'Contactez-nous')
@section('description', 'Contactez l\'équipe PhyloSanitas — blog médical et santé.')

@section('content')
<section class="section section--tight">
    <div class="container-xl">

        <nav aria-label="Fil d'Ariane" class="crumbs">
            <a href="{{ route('accueil') }}">Accueil</a>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <span aria-current="page">Contact</span>
        </nav>

        <div class="contact">
            <div class="contact__intro">
                <h1 class="page-title">Contactez-nous</h1>
                <p class="lede">Notre équipe répond à toutes vos questions dans les plus brefs délais.</p>

                <ul class="contact__list">
                    @if($site['adresse'])
                        <li>
                            <span class="contact__icon" aria-hidden="true"><i class="bi bi-geo-alt"></i></span>
                            <span><strong>Adresse</strong><address>{{ $site['adresse'] }}</address></span>
                        </li>
                    @endif
                    @if(count($site['phones']))
                        <li>
                            <span class="contact__icon" aria-hidden="true"><i class="bi bi-telephone"></i></span>
                            <span>
                                <strong>Téléphone{{ count($site['phones']) > 1 ? 's' : '' }}</strong>
                                @foreach($site['phones'] as $p)
                                    <span class="contact__line"><a href="{{ $p['href'] }}">{{ $p['value'] }}</a>@if($p['label'])<small>{{ $p['label'] }}</small>@endif</span>
                                @endforeach
                            </span>
                        </li>
                    @endif
                    @if(count($site['emails']))
                        <li>
                            <span class="contact__icon" aria-hidden="true"><i class="bi bi-envelope"></i></span>
                            <span>
                                <strong>Email{{ count($site['emails']) > 1 ? 's' : '' }}</strong>
                                @foreach($site['emails'] as $e)
                                    <span class="contact__line"><a href="{{ $e['href'] }}">{{ $e['value'] }}</a>@if($e['label'])<small>{{ $e['label'] }}</small>@endif</span>
                                @endforeach
                            </span>
                        </li>
                    @endif
                    @if($site['horaires'])
                        <li>
                            <span class="contact__icon" aria-hidden="true"><i class="bi bi-clock"></i></span>
                            <span><strong>Horaires</strong><span class="contact__line">{{ $site['horaires'] }}</span></span>
                        </li>
                    @endif
                </ul>

                @if($site['whatsapp_url'] || count($site['socials']))
                    <div class="contact__actions">
                        @if($site['whatsapp_url'])
                            <a href="{{ $site['whatsapp_url'] }}" target="_blank" rel="noopener" class="btn btn-whatsapp"><i class="bi bi-whatsapp" aria-hidden="true"></i>Écrire sur WhatsApp</a>
                        @endif
                        @if(count($site['socials']))
                            <div class="contact__social">@include('site.partials.socials')</div>
                        @endif
                    </div>
                @endif

                <aside class="notice notice--compact" aria-label="Urgence médicale">
                    <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
                    <p>Ce formulaire ne permet pas de traiter les urgences. En cas d'urgence médicale, contactez immédiatement les services d'urgence.</p>
                </aside>
            </div>

            <div class="contact__form panel">
                <h2 class="panel__title">Envoyer un message</h2>

                @if (session('success_contact'))
                    <div class="success-box" role="status">
                        <i class="bi bi-check-circle" aria-hidden="true"></i>
                        <span>{{ session('success_contact') }}</span>
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="field-row">
                        <div class="field">
                            <label for="contact-name">Votre nom <span class="req" aria-hidden="true">*</span></label>
                            <input type="text" name="name" id="contact-name" class="@error('name') is-invalid @enderror" value="{{ old('name') }}" required autocomplete="name">
                            @error('name')<p class="field__error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field">
                            <label for="contact-email">Votre email <span class="req" aria-hidden="true">*</span></label>
                            <input type="email" name="email" id="contact-email" class="@error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="exemple@email.com" required autocomplete="email">
                            @error('email')<p class="field__error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="field">
                        <label for="contact-subject">Sujet <span class="req" aria-hidden="true">*</span></label>
                        <input type="text" name="subject" id="contact-subject" class="@error('subject') is-invalid @enderror" value="{{ old('subject') }}" placeholder="Question sur un article, partenariat…" required>
                        @error('subject')<p class="field__error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="contact-message">Message <span class="req" aria-hidden="true">*</span></label>
                        <textarea name="message" id="contact-message" class="@error('message') is-invalid @enderror" rows="6" placeholder="Décrivez votre demande…" required>{{ old('message') }}</textarea>
                        @error('message')<p class="field__error">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn btn-medical">Envoyer le message</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
