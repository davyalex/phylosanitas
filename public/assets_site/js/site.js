/**
 * PhyloSanitas — comportements du site public (sans jQuery)
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- Header : ombre au défilement ---------- */
  var header = document.getElementById('header');
  var toTop = document.querySelector('.js-to-top');
  var progress = document.getElementById('reading-progress');
  var ticking = false;

  function onScroll() {
    var y = window.scrollY;
    if (header) header.classList.toggle('is-scrolled', y > 8);
    if (toTop) toTop.classList.toggle('is-visible', y > 600);
    if (progress) {
      var doc = document.documentElement;
      var max = doc.scrollHeight - doc.clientHeight;
      progress.style.transform = 'scaleX(' + (max > 0 ? Math.min(y / max, 1) : 0) + ')';
    }
    ticking = false;
  }
  window.addEventListener('scroll', function () {
    if (!ticking) { window.requestAnimationFrame(onScroll); ticking = true; }
  }, { passive: true });
  onScroll();

  if (toTop) {
    toTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
  }

  /* ---------- Recherche ---------- */
  var searchPanel = document.getElementById('site-search');
  var searchToggle = document.querySelector('.js-search-toggle');

  function setSearch(open) {
    if (!searchPanel || !searchToggle) return;
    searchPanel.hidden = !open;
    searchToggle.setAttribute('aria-expanded', String(open));
    if (open) {
      var input = searchPanel.querySelector('input[type="search"]');
      if (input) input.focus();
    }
  }
  if (searchToggle) {
    searchToggle.addEventListener('click', function () { setSearch(searchPanel.hidden); });
  }
  document.querySelectorAll('.js-search-close').forEach(function (btn) {
    btn.addEventListener('click', function () { setSearch(false); searchToggle && searchToggle.focus(); });
  });

  /* ---------- Menu mobile (tiroir) ---------- */
  var drawer = document.getElementById('nav-drawer');
  var backdrop = document.querySelector('.drawer-backdrop');
  var drawerOpenBtn = document.querySelector('.js-drawer-open');

  function setDrawer(open) {
    if (!drawer) return;
    drawer.classList.toggle('is-open', open);
    drawer.setAttribute('aria-hidden', String(!open));
    if (drawerOpenBtn) drawerOpenBtn.setAttribute('aria-expanded', String(open));
    document.body.classList.toggle('has-drawer', open);
    if (backdrop) {
      if (open) {
        backdrop.hidden = false;
        requestAnimationFrame(function () { backdrop.classList.add('is-open'); });
      } else {
        backdrop.classList.remove('is-open');
        setTimeout(function () { backdrop.hidden = true; }, 300);
      }
    }
    if (open) {
      var first = drawer.querySelector('.js-drawer-close');
      if (first) first.focus();
    } else if (drawerOpenBtn) {
      drawerOpenBtn.focus();
    }
  }
  if (drawerOpenBtn) drawerOpenBtn.addEventListener('click', function () { setDrawer(true); });
  document.querySelectorAll('.js-drawer-close').forEach(function (el) {
    el.addEventListener('click', function () { setDrawer(false); });
  });

  // Garder le focus dans le tiroir
  if (drawer) {
    drawer.addEventListener('keydown', function (e) {
      if (e.key !== 'Tab') return;
      var items = drawer.querySelectorAll('a[href], button, input');
      if (!items.length) return;
      var first = items[0], last = items[items.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    });
  }

  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    if (drawer && drawer.classList.contains('is-open')) setDrawer(false);
    else if (searchPanel && !searchPanel.hidden) { setSearch(false); searchToggle && searchToggle.focus(); }
  });

  // Fermer le tiroir si l'écran s'élargit
  window.matchMedia('(min-width: 1200px)').addEventListener('change', function (mq) {
    if (mq.matches && drawer && drawer.classList.contains('is-open')) setDrawer(false);
  });

  /* ---------- Carrousels (Swiper) ---------- */
  if (typeof Swiper !== 'undefined') {
    if (document.querySelector('.heroSwiper')) {
      var heroSlides = document.querySelectorAll('.heroSwiper .swiper-slide').length;
      new Swiper('.heroSwiper', {
        loop: heroSlides > 1,
        speed: reduceMotion ? 0 : 900,
        effect: 'fade',
        fadeEffect: { crossFade: true },
        autoplay: heroSlides > 1 && !reduceMotion ? { delay: 6500, disableOnInteraction: false, pauseOnMouseEnter: true } : false,
        pagination: { el: '.hero__pagination', clickable: true },
        navigation: { nextEl: '.hero-next', prevEl: '.hero-prev' },
        a11y: { prevSlideMessage: 'Diapositive précédente', nextSlideMessage: 'Diapositive suivante', paginationBulletMessage: 'Aller à la diapositive {{index}}' }
      });
    }

    if (document.querySelector('.newsSwiper')) {
      var newsSlides = document.querySelectorAll('.newsSwiper .swiper-slide').length;
      new Swiper('.newsSwiper', {
        loop: newsSlides > 1,
        speed: reduceMotion ? 0 : 600,
        spaceBetween: 12,
        autoplay: newsSlides > 1 && !reduceMotion ? { delay: 6000, disableOnInteraction: false, pauseOnMouseEnter: true } : false,
        pagination: { el: '.news-pagination', clickable: true },
        navigation: { nextEl: '.news-next', prevEl: '.news-prev' },
        a11y: { prevSlideMessage: 'Actualité précédente', nextSlideMessage: 'Actualité suivante' }
      });
    }
  }

  /* ---------- Rotation des sondages ---------- */
  document.querySelectorAll('.js-rotator').forEach(function (rotator) {
    var items = rotator.querySelectorAll('.poll-rotator__item');
    var widget = rotator.closest('.widget');
    var dots = widget ? widget.querySelectorAll('.dots__dot') : [];
    if (items.length < 2) return;
    var index = 0;
    var timer = null;
    var interval = parseInt(rotator.getAttribute('data-interval'), 10) || 8000;

    function show(i) {
      index = (i + items.length) % items.length;
      items.forEach(function (item, n) {
        var active = n === index;
        item.classList.toggle('is-active', active);
        item.setAttribute('aria-hidden', String(!active));
        item.querySelectorAll('a, button').forEach(function (el) { el.tabIndex = active ? 0 : -1; });
      });
      dots.forEach(function (dot, n) {
        dot.classList.toggle('is-active', n === index);
        dot.setAttribute('aria-pressed', String(n === index));
      });
    }
    function start() { if (!reduceMotion) timer = setInterval(function () { show(index + 1); }, interval); }
    function stop() { clearInterval(timer); }

    dots.forEach(function (dot) {
      dot.addEventListener('click', function () { stop(); show(parseInt(dot.getAttribute('data-index'), 10)); start(); });
    });
    rotator.addEventListener('mouseenter', stop);
    rotator.addEventListener('mouseleave', start);
    rotator.addEventListener('focusin', stop);
    rotator.addEventListener('focusout', start);
    show(0);
    start();
  });

  /* ---------- Publicité dans l'article : après le 3e paragraphe ---------- */
  var prose = document.querySelector('.js-prose');
  var inArticle = document.querySelector('.js-inarticle-ad');
  if (prose && inArticle && inArticle.firstElementChild) {
    var paragraphs = Array.prototype.filter.call(prose.children, function (el) {
      return el.tagName === 'P' && el.textContent.trim().length > 40;
    });
    if (paragraphs.length >= 5) {
      paragraphs[2].insertAdjacentElement('afterend', inArticle.firstElementChild);
    }
  }

  /* ---------- Copier le lien ---------- */
  document.querySelectorAll('.js-copy-link').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var url = btn.getAttribute('data-url') || window.location.href;
      var feedback = btn.parentElement.querySelector('.share__feedback');
      var done = function () {
        btn.innerHTML = '<i class="bi bi-check-lg" aria-hidden="true"></i>';
        if (feedback) feedback.textContent = 'Lien copié';
        setTimeout(function () {
          btn.innerHTML = '<i class="bi bi-link-45deg" aria-hidden="true"></i>';
          if (feedback) feedback.textContent = '';
        }, 2500);
      };
      if (navigator.clipboard) navigator.clipboard.writeText(url).then(done);
    });
  });

  /* ---------- Infos flash : pause du défilement ---------- */
  document.querySelectorAll('.js-flash-pause').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var bar = btn.closest('.flash');
      var paused = bar.classList.toggle('is-paused');
      btn.setAttribute('aria-pressed', String(paused));
      btn.setAttribute('aria-label', paused ? 'Reprendre le défilement' : 'Mettre en pause le défilement');
      btn.innerHTML = '<i class="bi bi-' + (paused ? 'play-fill' : 'pause-fill') + '" aria-hidden="true"></i>';
    });
  });

  /* ---------- Application installable (PWA) ---------- */
  // Enregistré dès que la page est utilisable (sans attendre les scripts publicitaires, parfois lents)
  if ('serviceWorker' in navigator) {
    var registerSW = function () { navigator.serviceWorker.register('/sw.js').catch(function () {}); };
    if ('requestIdleCallback' in window) requestIdleCallback(registerSW, { timeout: 2000 });
    else setTimeout(registerSW, 1000);
  }

  (function () {
    var banner = document.querySelector('.js-install');
    if (!banner) return;

    var standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    if (standalone) return; // déjà installée : on ne propose rien

    // « Plus tard » masque la bannière pour cette visite seulement : elle revient à la prochaine ouverture
    var dismissed = false;
    try { dismissed = sessionStorage.getItem('ps-install-dismissed') === '1'; } catch (e) {}
    if (dismissed) return;

    var deferred = null;
    var acceptBtn = banner.querySelector('.js-install-accept');
    var textEl = banner.querySelector('.js-install-text');
    var ua = window.navigator.userAgent;
    var isIOS = /iphone|ipad|ipod/i.test(ua) && !window.MSStream;

    var show = function () {
      banner.hidden = false;
      requestAnimationFrame(function () { banner.classList.add('is-visible'); });
    };
    var hide = function () {
      banner.classList.remove('is-visible');
      setTimeout(function () { banner.hidden = true; }, 350);
    };

    // Android / Chrome / Edge : le navigateur fournit l'invite d'installation
    window.addEventListener('beforeinstallprompt', function (e) {
      e.preventDefault();
      deferred = e;
      setTimeout(show, 2500);
    });

    // iPhone / iPad (Safari) : pas d'invite automatique, on explique la marche à suivre
    if (isIOS) {
      textEl.innerHTML = 'Touchez <strong>Partager</strong> <i class="bi bi-box-arrow-up" aria-hidden="true"></i> puis <strong>« Sur l\'écran d\'accueil »</strong>.';
      acceptBtn.textContent = 'Compris';
      setTimeout(show, 2500);
    }

    acceptBtn.addEventListener('click', function () {
      if (deferred) {
        deferred.prompt();
        deferred.userChoice.finally(function () { deferred = null; hide(); });
      } else {
        hide();
      }
    });

    banner.querySelector('.js-install-dismiss').addEventListener('click', function () {
      try { sessionStorage.setItem('ps-install-dismissed', '1'); } catch (e) {}
      hide();
    });

    window.addEventListener('appinstalled', hide);
  })();

  /* ---------- Apparition au défilement + compteurs ---------- */
  // Seuls les éléments encore sous l'écran sont masqués : rien ne clignote au chargement.
  if (!reduceMotion && 'IntersectionObserver' in window) {
    var REVEAL = '.sec-head, .lead, .prow--feature, .pcard, .ptile, .prevention__head, .consult__art, .consult__body, .stat, '
      + '.endread, .litem, .widget, .cta-band, .related, .panel, .page-head, .empty, .contact__line';
    var fold = window.innerHeight * 0.95;

    var revealObs = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-in');
        revealObs.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

    document.querySelectorAll(REVEAL).forEach(function (el) {
      if (el.closest('.reveal') || el.getBoundingClientRect().top < fold) return;
      // Décalage en cascade entre éléments frères (cartes d'une même grille)
      var siblings = Array.prototype.filter.call(el.parentElement.children, function (s) { return s.matches(REVEAL); });
      var i = siblings.indexOf(el);
      if (i > 0) el.style.setProperty('--rd', Math.min(i, 5) * 80 + 'ms');
      el.classList.add('reveal');
      revealObs.observe(el);
    });

    // Compteurs : de 0 à la valeur finale quand ils deviennent visibles
    var fmt = new Intl.NumberFormat('fr-FR');
    var countObs = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        countObs.unobserve(entry.target);
        var el = entry.target, target = parseInt(el.getAttribute('data-count'), 10) || 0, t0 = null, dur = 1400;
        var step = function (t) {
          if (!t0) t0 = t;
          var p = Math.min((t - t0) / dur, 1), eased = 1 - Math.pow(1 - p, 3);
          el.textContent = fmt.format(Math.round(target * eased));
          if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
      });
    }, { threshold: 0.5 });
    document.querySelectorAll('[data-count]').forEach(function (el) {
      if (el.getBoundingClientRect().top < fold) return;
      el.textContent = '0';
      countObs.observe(el);
    });
  }

  /* ---------- Notifications : disparition auto ---------- */
  document.querySelectorAll('.toast.show').forEach(function (toast) {
    setTimeout(function () { toast.classList.remove('show'); }, 6000);
  });
});
