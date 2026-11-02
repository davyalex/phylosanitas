<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#004A99">
    <meta name="robots" content="noindex">
    <title>Hors connexion — {{ $site['nom'] }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets_site/img/favicon-32x32.png') }}">
    <style>
        :root { --ink: #12233a; --muted: #5a6a80; --brand: #0066cc; --brand-strong: #004a99; --bg: #f4f7fa; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px 16px; background: var(--bg); color: var(--ink); font-family: "Source Sans 3", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        main { width: 100%; max-width: 440px; text-align: center; background: #fff; border: 1px solid #dde5ee; border-radius: 16px; padding: 32px 24px; }
        img { height: 44px; width: auto; margin-bottom: 24px; }
        .icon { width: 64px; height: 64px; margin: 0 auto 16px; display: grid; place-items: center; border-radius: 50%; background: #e8f1fb; color: var(--brand); }
        h1 { font-family: "Source Serif 4", Georgia, serif; font-size: 1.6rem; line-height: 1.2; margin: 0 0 8px; }
        p { color: var(--muted); line-height: 1.55; margin: 0 0 24px; }
        button { min-height: 46px; padding: 0 22px; border: 0; border-radius: 8px; background: var(--brand); color: #fff; font: 600 1rem/1 inherit; cursor: pointer; }
        button:hover { background: var(--brand-strong); }
        button:focus-visible { outline: 3px solid rgba(0, 102, 204, .35); outline-offset: 2px; }
    </style>
</head>
<body>
    <main>
        <img src="{{ $site['logo'] }}" alt="{{ $site['nom'] }}">
        <div class="icon" aria-hidden="true">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 1l22 22"/><path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/><path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/><path d="M10.71 5.05A16 16 0 0 1 22.58 9"/><path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><path d="M12 20h.01"/></svg>
        </div>
        <h1>Vous êtes hors connexion</h1>
        <p>Cette page n'est pas encore disponible sans internet. Les articles que vous avez déjà consultés restent accessibles. Vérifiez votre connexion puis réessayez.</p>
        <button type="button" onclick="location.reload()">Réessayer</button>
    </main>
</body>
</html>
