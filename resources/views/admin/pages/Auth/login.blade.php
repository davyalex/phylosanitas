@php
    $site = $site ?? \App\Models\Setting::forViews();
    $nom  = $site['nom'] ?? config('app.name');
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0a2c52">
    <title>Connexion — {{ $nom }} administration</title>

    <link rel="icon" href="{{ asset('assets_admin/img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets_admin/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets_site/css/site-motion.css') }}?v={{ @filemtime(public_path('assets_site/css/site-motion.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets_admin/css/login.css') }}?v={{ @filemtime(public_path('assets_admin/css/login.css')) }}" rel="stylesheet">
</head>
<body>
<main class="auth">

    {{-- Panneau de marque (ordinateur) --}}
    <aside class="auth__aside" aria-hidden="true">
        <div class="auth__aside-inner">
            <span class="auth__eyebrow"><i class="bi bi-shield-lock"></i> Espace d'administration</span>
            <h2 class="auth__headline">Gérez {{ $nom }} en toute simplicité.</h2>
            <p class="auth__lead">Articles, sondages, popups, publicités et configuration du site, au même endroit.</p>

            <div class="auth__art"><x-health-art name="hospital" /></div>

            <ul class="auth__features">
                <li><i class="bi bi-journal-medical"></i> Publiez vos conseils santé</li>
                <li><i class="bi bi-graph-up-arrow"></i> Suivez visites et lectures</li>
                <li><i class="bi bi-megaphone"></i> Diffusez vos campagnes</li>
            </ul>
        </div>
        <p class="auth__copy">© {{ date('Y') }} {{ $nom }}</p>
    </aside>

    {{-- Formulaire --}}
    <section class="auth__main">
        <div class="auth__card">
            <a href="{{ route('accueil') }}" class="auth__logo" title="Retour au site">
                <img src="{{ $site['logo'] }}" alt="{{ $nom }}" onerror="this.onerror=null;this.src='{{ asset('assets_site/img/logo/logo_tp.png') }}'">
            </a>

            <h1 class="auth__title">Connexion</h1>
            <p class="auth__subtitle">Accédez au tableau de bord de {{ $nom }}.</p>

            @if(session('status'))
                <div class="auth__alert auth__alert--ok" role="status">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="auth__alert" role="alert">
                    <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                    <span>{{ $errors->first('login') ?: $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="auth__form" id="login-form" novalidate>
                @csrf

                <div class="auth__field">
                    <label for="phone">Numéro de téléphone</label>
                    <div class="auth__input @error('phone') is-invalid @enderror">
                        <i class="bi bi-telephone" aria-hidden="true"></i>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" inputmode="tel"
                               autocomplete="username" placeholder="Ex. 0700000000" required autofocus>
                    </div>
                    @error('phone')<p class="auth__error">{{ $message }}</p>@enderror
                </div>

                <div class="auth__field">
                    <label for="password">Mot de passe</label>
                    <div class="auth__input @error('password') is-invalid @enderror">
                        <i class="bi bi-lock" aria-hidden="true"></i>
                        <input id="password" type="password" name="password" autocomplete="current-password" placeholder="Votre mot de passe" required>
                        <button type="button" class="auth__eye" id="toggle-pwd" aria-label="Afficher le mot de passe" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <p class="auth__caps" id="caps" hidden><i class="bi bi-capslock-fill" aria-hidden="true"></i> Majuscules activées</p>
                    @error('password')<p class="auth__error">{{ $message }}</p>@enderror
                </div>

                <label class="auth__remember">
                    <input type="checkbox" name="remember" value="1" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                    <span>Rester connecté sur cet appareil</span>
                </label>

                <button type="submit" class="auth__submit" id="login-submit">
                    <span class="auth__submit-label">Se connecter</span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </button>
            </form>

            <p class="auth__help">Mot de passe oublié ? Contactez l'administrateur du site.</p>

            <a href="{{ route('accueil') }}" class="auth__back"><i class="bi bi-arrow-left" aria-hidden="true"></i> Retour au site</a>
        </div>
    </section>
</main>

<script>
(function () {
    var pwd = document.getElementById('password');
    var eye = document.getElementById('toggle-pwd');
    eye.addEventListener('click', function () {
        var show = pwd.type === 'password';
        pwd.type = show ? 'text' : 'password';
        eye.setAttribute('aria-pressed', show);
        eye.setAttribute('aria-label', show ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
        eye.querySelector('i').className = 'bi ' + (show ? 'bi-eye-slash' : 'bi-eye');
        pwd.focus();
    });

    // Avertit si la touche Majuscule est verrouillée
    var caps = document.getElementById('caps');
    ['keydown', 'keyup'].forEach(function (ev) {
        pwd.addEventListener(ev, function (e) {
            if (e.getModifierState) caps.hidden = !e.getModifierState('CapsLock');
        });
    });

    // Champ vide : message clair ; sinon bouton en attente pour éviter le double envoi
    var form = document.getElementById('login-form');
    form.addEventListener('submit', function (e) {
        var empty = Array.prototype.filter.call(form.querySelectorAll('input[required]'), function (i) { return !i.value.trim(); });
        if (empty.length) {
            e.preventDefault();
            empty.forEach(function (i) { i.closest('.auth__input').classList.add('is-invalid'); });
            empty[0].focus();
            return;
        }
        var btn = document.getElementById('login-submit');
        btn.disabled = true;
        btn.querySelector('.auth__submit-label').textContent = 'Connexion…';
    });
    form.querySelectorAll('input').forEach(function (i) {
        i.addEventListener('input', function () { var w = i.closest('.auth__input'); if (w) w.classList.remove('is-invalid'); });
    });

    @if($errors->has('login'))
        pwd.focus();
    @endif
})();
</script>
</body>
</html>
