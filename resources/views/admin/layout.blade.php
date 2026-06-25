<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name') }} · @yield('title', 'Dashboard')</title>

  <link href="{{ asset('assets_admin/img/favicon.png') }}" rel="icon">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

  <link href="{{ asset('assets_admin/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_admin/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_admin/vendor/simple-datatables/style.css') }}" rel="stylesheet">
  <link href="{{ asset('assets_admin/css/style.css') }}" rel="stylesheet">

  <script src="{{ asset('assets_admin/js/jquery.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

  <style>
    /* ── Variables médicales ─────────────────────── */
    :root {
      --sb-bg:          #0d1f3c;
      --sb-bg-dark:     #081526;
      --sb-accent:      #0066CC;
      --sb-accent-glow: rgba(0,102,204,.35);
      --sb-text:        rgba(255,255,255,.78);
      --sb-text-dim:    rgba(255,255,255,.42);
      --sb-hover-bg:    rgba(255,255,255,.08);
      --sb-active-bg:   rgba(255,255,255,.14);
      --sb-width:       260px;
      --header-h:       64px;
    }

    /* ── Reset body / font ───────────────────────── */
    body { font-family: 'Inter', sans-serif; background: #f4f6fb; }

    /* ══════════════════════════════════════════════
       HEADER
    ══════════════════════════════════════════════ */
    #header {
      height: var(--header-h);
      background: #fff;
      border-bottom: 1px solid #e8ecf1;
      box-shadow: 0 1px 6px rgba(0,0,0,.06);
      padding: 0 20px;
      display: flex;
      align-items: center;
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 997;
      gap: 16px;
    }

    .header-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      width: var(--sb-width);
      flex-shrink: 0;
    }

    .header-logo-icon {
      width: 36px; height: 36px;
      background: linear-gradient(135deg, #0066CC, #00A86B);
      border-radius: 9px;
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: 1.1rem; font-weight: 700;
      flex-shrink: 0;
    }

    .header-logo-text {
      line-height: 1.15;
    }

    .header-logo-name {
      font-size: .9rem; font-weight: 700; color: #0d1f3c;
      display: block;
    }

    .header-logo-sub {
      font-size: .68rem; color: #8a99b2; text-transform: uppercase;
      letter-spacing: .05em;
    }

    .header-toggle {
      background: none; border: none; cursor: pointer;
      color: #6b7c93; font-size: 1.3rem;
      padding: 4px 8px; border-radius: 6px;
      transition: background .2s, color .2s;
    }
    .header-toggle:hover { background: #f0f4ff; color: #0066CC; }

    .header-spacer { flex: 1; }

    /* Lien "Voir le site" */
    .header-site-link {
      display: flex; align-items: center; gap: 6px;
      font-size: .82rem; font-weight: 600;
      color: #6b7c93; text-decoration: none;
      padding: 6px 12px; border-radius: 8px;
      border: 1px solid #e2e8f0;
      transition: all .2s;
    }
    .header-site-link:hover { color: #0066CC; border-color: #0066CC; background: #f0f6ff; }

    /* Dropdown utilisateur */
    .header-user-btn {
      display: flex; align-items: center; gap: 10px;
      background: none; border: none; cursor: pointer;
      padding: 5px 8px; border-radius: 10px;
      transition: background .2s;
    }
    .header-user-btn:hover { background: #f4f6fb; }

    .header-avatar {
      width: 34px; height: 34px; border-radius: 50%;
      background: linear-gradient(135deg, #0066CC, #00A86B);
      color: #fff; font-size: .72rem; font-weight: 700;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }

    .header-user-name {
      font-size: .85rem; font-weight: 600; color: #2d3748;
      max-width: 130px; white-space: nowrap; overflow: hidden;
      text-overflow: ellipsis;
    }

    .header-user-role {
      font-size: .72rem; color: #8a99b2;
    }

    /* ══════════════════════════════════════════════
       SIDEBAR
    ══════════════════════════════════════════════ */
    #sidebar {
      position: fixed;
      top: var(--header-h);
      left: 0; bottom: 0;
      width: var(--sb-width);
      background: linear-gradient(180deg, var(--sb-bg) 0%, var(--sb-bg-dark) 100%);
      z-index: 996;
      overflow-y: auto;
      overflow-x: hidden;
      scrollbar-width: thin;
      scrollbar-color: rgba(255,255,255,.15) transparent;
      transition: left .3s ease, width .3s ease;
      display: flex;
      flex-direction: column;
      padding-bottom: 20px;
    }

    #sidebar::-webkit-scrollbar { width: 4px; }
    #sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 2px; }

    @media (max-width: 1199px) {
      #sidebar { left: calc(-1 * var(--sb-width)); }
      .toggle-sidebar #sidebar { left: 0; }
    }

    @media (min-width: 1200px) {
      #main, #footer { margin-left: var(--sb-width); }
      .toggle-sidebar #main, .toggle-sidebar #footer { margin-left: 0; }
      .toggle-sidebar #sidebar { left: calc(-1 * var(--sb-width)); }
    }

    /* Profil en haut de la sidebar */
    .sb-profile {
      padding: 20px 16px 16px;
      border-bottom: 1px solid rgba(255,255,255,.08);
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .sb-avatar {
      width: 42px; height: 42px; border-radius: 50%;
      background: linear-gradient(135deg, #0066CC, #00A86B);
      color: #fff; font-size: .85rem; font-weight: 700;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      border: 2px solid rgba(255,255,255,.2);
    }

    .sb-profile-name {
      font-size: .88rem; font-weight: 600; color: #fff;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .sb-profile-role {
      font-size: .72rem; color: var(--sb-text-dim);
      display: block;
    }

    /* Navigation */
    .sb-nav { padding: 12px 10px; flex: 1; }

    .sb-section-label {
      font-size: .65rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .1em;
      color: var(--sb-text-dim);
      padding: 14px 8px 6px;
    }

    .sb-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 12px;
      border-radius: 10px;
      color: var(--sb-text);
      text-decoration: none;
      font-size: .86rem;
      font-weight: 500;
      transition: background .18s, color .18s;
      position: relative;
      margin-bottom: 2px;
    }

    .sb-link:hover {
      background: var(--sb-hover-bg);
      color: #fff;
    }

    .sb-link.active {
      background: var(--sb-active-bg);
      color: #fff;
      font-weight: 600;
      box-shadow: inset 3px 0 0 var(--sb-accent);
    }

    .sb-link .sb-icon {
      width: 32px; height: 32px;
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      font-size: .95rem;
      flex-shrink: 0;
      background: rgba(255,255,255,.07);
      color: var(--sb-text);
      transition: background .18s, color .18s;
    }

    .sb-link:hover .sb-icon,
    .sb-link.active .sb-icon {
      background: var(--sb-accent);
      color: #fff;
    }

    .sb-link .sb-badge {
      margin-left: auto;
      background: rgba(255,255,255,.15);
      color: rgba(255,255,255,.8);
      font-size: .65rem;
      font-weight: 700;
      padding: 2px 7px;
      border-radius: 20px;
    }

    .sb-link.active .sb-badge {
      background: var(--sb-accent);
      color: #fff;
    }

    /* Séparateur bas de sidebar */
    .sb-footer {
      padding: 10px;
      border-top: 1px solid rgba(255,255,255,.08);
    }

    .sb-footer .sb-link { color: rgba(255,255,255,.5); }
    .sb-footer .sb-link:hover { color: #fff; }

    /* ══════════════════════════════════════════════
       MAIN CONTENT
    ══════════════════════════════════════════════ */
    #main {
      margin-top: var(--header-h);
      padding: 24px;
      min-height: calc(100vh - var(--header-h));
    }

    .pagetitle { margin-bottom: 20px; }
    .pagetitle h1 { font-size: 1.25rem; font-weight: 700; color: #0d1f3c; margin-bottom: 4px; }
    .breadcrumb { background: none; padding: 0; margin: 0; font-size: .8rem; }
    .breadcrumb-item a { color: #0066CC; text-decoration: none; }
    .breadcrumb-item.active { color: #8a99b2; }
    .breadcrumb-item + .breadcrumb-item::before { color: #c5cdd8; }

    #footer {
      margin-top: 20px;
      padding: 12px 24px;
      background: #fff;
      border-top: 1px solid #e8ecf1;
      font-size: .78rem;
      color: #8a99b2;
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 6px;
    }

    #footer a { color: #0066CC; text-decoration: none; }

    /* ══════════════════════════════════════════════
       RESPONSIVE MOBILE
    ══════════════════════════════════════════════ */
    @media (max-width: 767px) {

      /* Header : logo réduit à l'icône, avatar toujours visible */
      .header-logo {
        width: auto;
        min-width: 0;
        flex-shrink: 1;
      }
      .header-logo-text { display: none; }

      /* Avatar toujours visible — pas de débordement */
      #header {
        padding: 0 10px;
        gap: 8px;
      }
      .header-user-btn {
        padding: 4px 6px;
        flex-shrink: 0;
      }
      /* Masquer le nom/rôle sur mobile (icône seule) */
      .header-user-name,
      .header-user-role { display: none !important; }
      .header-avatar { width: 36px; height: 36px; font-size: .78rem; }

      /* Spacer ne pousse pas trop */
      .header-spacer { min-width: 0; }

      /* Contenu principal : padding réduit */
      #main { padding: 14px 10px; }
      #footer { padding: 10px 14px; font-size: .72rem; }

      /* Page title */
      .pagetitle h1 { font-size: 1.05rem; }

      /* Tables : colonnes secondaires masquées */
      .table-admin-hide { display: none !important; }

      /* Actions : boutons plus petits */
      .btn-sm { padding: .2rem .45rem; font-size: .75rem; }

      /* KPI cards */
      .kpi-card { padding: 14px 12px; gap: 10px; }
      .kpi-icon { width: 42px; height: 42px; font-size: 1.3rem; }
      .kpi-value { font-size: 1.35rem; }
      .kpi-label { font-size: .68rem; }

      /* Cards : retrait réduit */
      .card-body { padding: 14px 12px; }
      .card-header { padding: 10px 12px; }

      /* Formulaire article : pleine largeur */
      .col-md-8, .col-md-4 { width: 100% !important; }
    }

    @media (max-width: 575px) {
      /* Header encore plus compact */
      .header-logo-icon { width: 30px; height: 30px; font-size: .95rem; border-radius: 7px; }
      #main { padding: 10px 8px; }

      /* Tableau actions : une colonne défilante */
      .d-flex.gap-1.flex-nowrap { flex-wrap: wrap; gap: 4px !important; }
    }
  </style>

  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6925205610540207" crossorigin="anonymous"></script>
</head>

<body>

{{-- ══════ HEADER ══════ --}}
<header id="header">

  {{-- Logo --}}
  <a href="{{ route('dashboard') }}" class="header-logo">
    <div class="header-logo-icon">Φ</div>
    <div class="header-logo-text">
      <span class="header-logo-name">PhyloSanitas</span>
      <span class="header-logo-sub">Administration</span>
    </div>
  </a>

  {{-- Toggle sidebar --}}
  <button class="header-toggle toggle-sidebar-btn" type="button">
    <i class="bi bi-list"></i>
  </button>

  <div class="header-spacer"></div>

  {{-- Lien site --}}
  <a href="{{ route('accueil') }}" target="_blank" class="header-site-link d-none d-md-flex">
    <i class="bi bi-box-arrow-up-right"></i> Voir le site
  </a>

  {{-- Dropdown user --}}
  <div class="dropdown ms-2">
    <button class="header-user-btn dropdown-toggle" data-bs-toggle="dropdown">
      <div class="header-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
      <div class="d-none d-md-block text-start">
        <div class="header-user-name">{{ Auth::user()->name }}</div>
        <div class="header-user-role">{{ Auth::user()->roles->first()?->name ?? '—' }}</div>
      </div>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width:200px;">
      <li class="px-3 py-2">
        <div class="fw-semibold small">{{ Auth::user()->name }}</div>
        <div class="text-muted" style="font-size:.75rem;">{{ Auth::user()->email }}</div>
      </li>
      <li><hr class="dropdown-divider my-1"></li>
      <li>
        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('user.profil', Auth::user()->id) }}">
          <i class="bi bi-person-circle text-primary"></i> Mon profil
        </a>
      </li>
      <li>
        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('accueil') }}" target="_blank">
          <i class="bi bi-globe text-success"></i> Voir le site
        </a>
      </li>
      <li><hr class="dropdown-divider my-1"></li>
      <li>
        <form id="form_logout" action="{{ route('logout') }}" method="POST" class="d-inline">@csrf</form>
        <a class="dropdown-item d-flex align-items-center gap-2 text-danger"
           href="#" onclick="event.preventDefault(); document.getElementById('form_logout').submit();">
          <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
      </li>
    </ul>
  </div>

</header>

{{-- ══════ SIDEBAR ══════ --}}
<aside id="sidebar">

  {{-- Mini-profil --}}
  <div class="sb-profile">
    <div class="sb-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
    <div class="overflow-hidden">
      <div class="sb-profile-name">{{ Auth::user()->name }}</div>
      <span class="sb-profile-role">{{ Auth::user()->roles->first()?->name ?? 'Utilisateur' }}</span>
    </div>
  </div>

  {{-- Navigation --}}
  <nav class="sb-nav">

    {{-- Accueil --}}
    <a href="{{ route('dashboard') }}"
       class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <span class="sb-icon"><i class="bi bi-speedometer2"></i></span>
      Tableau de bord
    </a>

    {{-- ── Contenu ── --}}
    <div class="sb-section-label">Contenu</div>

    <a href="{{ route('post') }}"
       class="sb-link {{ request()->routeIs('post', 'post.create', 'post.edit') ? 'active' : '' }}">
      <span class="sb-icon"><i class="bi bi-file-earmark-medical"></i></span>
      Articles
    </a>

    <a href="{{ route('post', ['type' => 'sondage']) }}"
       class="sb-link {{ request()->routeIs('post.edit-sondage') || (request()->routeIs('post') && request('type') === 'sondage') ? 'active' : '' }}">
      <span class="sb-icon"><i class="bi bi-bar-chart-line"></i></span>
      Sondages
    </a>

    @role('administrateur')
    <a href="{{ route('category') }}"
       class="sb-link {{ request()->routeIs('category', 'category.edit') ? 'active' : '' }}">
      <span class="sb-icon"><i class="bi bi-collection"></i></span>
      Catégories
    </a>
    @endrole

    {{-- ── Administration ── --}}
    @role('administrateur')
    <div class="sb-section-label">Administration</div>

    <a href="{{ route('user') }}"
       class="sb-link {{ request()->routeIs('user', 'user.profil', 'user.edit') ? 'active' : '' }}">
      <span class="sb-icon"><i class="bi bi-people"></i></span>
      Utilisateurs
    </a>

    <a href="{{ route('actualite.index') }}"
       class="sb-link {{ request()->routeIs('actualite.index') ? 'active' : '' }}">
      <span class="sb-icon"><i class="bi bi-images"></i></span>
      Carrousel Hero
    </a>
    @endrole

  </nav>

  {{-- Footer sidebar --}}
  <div class="sb-footer">
    <a href="{{ route('accueil') }}" target="_blank" class="sb-link">
      <span class="sb-icon"><i class="bi bi-globe2"></i></span>
      Voir le site
    </a>
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="sb-link w-100 text-start border-0 bg-transparent">
        <span class="sb-icon"><i class="bi bi-box-arrow-right"></i></span>
        Déconnexion
      </button>
    </form>
  </div>

</aside>

{{-- ══════ MAIN ══════ --}}
<main id="main">

  <div class="pagetitle">
    <h1>@yield('title', 'Dashboard')</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house me-1"></i>Accueil</a></li>
        <li class="breadcrumb-item active">@yield('title', 'Dashboard')</li>
      </ol>
    </nav>
  </div>

  @yield('content')
  @include('sweetalert::alert')

</main>

<footer id="footer">
  <span>&copy; {{ date('Y') }} <strong>{{ config('app.name') }}</strong> — Tous droits réservés</span>
  <span>Développé par <a href="https://dolubux.com" target="_blank">dolubux.com</a></span>
</footer>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center">
  <i class="bi bi-arrow-up-short"></i>
</a>

{{-- Scripts --}}
<script src="{{ asset('assets_admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets_admin/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets_admin/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets_admin/vendor/simple-datatables/simple-datatables.js') }}"></script>
<script src="{{ asset('assets_admin/vendor/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets_admin/js/main.js') }}"></script>

<script>
  // Toastr notifications
  @if(Session::has('message'))
    toastr.options = { closeButton: true, progressBar: true, timeOut: 3000 };
    toastr.success("{{ session('message') }}");
  @endif
  @if(Session::has('error'))
    toastr.options = { closeButton: true, progressBar: true, timeOut: 8000 };
    toastr.error("{{ session('error') }}");
  @endif
  @if(Session::has('info'))
    toastr.options = { closeButton: true, progressBar: true };
    toastr.info("{{ session('info') }}");
  @endif
  @if(Session::has('warning'))
    toastr.options = { closeButton: true, progressBar: true };
    toastr.warning("{{ session('warning') }}");
  @endif
</script>

</body>
</html>
