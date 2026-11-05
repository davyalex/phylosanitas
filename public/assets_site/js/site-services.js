/**
 * PhyloSanitas — Services santé (site public)
 * Menu « Services santé », bandeau des journées santé, avis sur les articles,
 * quiz de l'accueil.
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  var store = {
    get: function (k) { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set: function (k, v) { try { localStorage.setItem(k, v); } catch (e) {} },
  };
  function postJSON(url, data) {
    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
      body: JSON.stringify(data || {}),
    });
  }

  /* ---------- Menu « Services santé » (ordinateur) ---------- */
  document.querySelectorAll('.js-nav-more').forEach(function (btn) {
    var panel = document.getElementById(btn.getAttribute('aria-controls'));
    if (!panel) return;
    var set = function (open) {
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      panel.hidden = !open;
      if (!open) return;
      // Garde le panneau dans l'écran (fenêtres étroites) : décalage calculé sans l'animation
      panel.style.marginLeft = '0px';
      panel.style.animation = 'none';
      var r = panel.getBoundingClientRect(), gap = 16, shift = 0;
      if (r.right > window.innerWidth - gap) shift = window.innerWidth - gap - r.right;
      if (r.left + shift < gap) shift = gap - r.left;
      panel.style.marginLeft = shift + 'px';
      panel.offsetWidth; // relance l'animation d'ouverture
      panel.style.animation = '';
    };
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      set(panel.hidden);
      if (!panel.hidden) { var first = panel.querySelector('a'); if (first) first.focus(); }
    });
    document.addEventListener('click', function (e) { if (!panel.hidden && !panel.contains(e.target)) set(false); });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !panel.hidden) { set(false); btn.focus(); }
    });
    panel.addEventListener('focusout', function (e) {
      if (e.relatedTarget && !panel.contains(e.relatedTarget) && e.relatedTarget !== btn) set(false);
    });
  });

  /* ---------- Barres défilantes : flèches gauche / droite ---------- */
  document.querySelectorAll('.js-hscroll').forEach(function (box) {
    var track = box.querySelector('.js-hscroll-track');
    var prev = box.querySelector('.js-hscroll-prev');
    var next = box.querySelector('.js-hscroll-next');
    if (!track || !prev || !next) return;

    var update = function () {
      var max = track.scrollWidth - track.clientWidth;
      var hasPrev = track.scrollLeft > 4, hasNext = track.scrollLeft < max - 4;
      prev.hidden = !hasPrev;
      next.hidden = !hasNext;
      box.classList.toggle('has-prev', hasPrev);
      box.classList.toggle('has-next', hasNext);
    };
    var step = function (dir) {
      track.scrollBy({ left: dir * Math.max(160, track.clientWidth * 0.7), behavior: 'smooth' });
    };
    prev.addEventListener('click', function () { step(-1); });
    next.addEventListener('click', function () { step(1); });
    track.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);

    // Le lien actif (page en cours) est amené dans la zone visible
    var active = track.querySelector('.is-active');
    if (active) track.scrollLeft = active.offsetLeft - track.clientWidth / 2 + active.offsetWidth / 2;
    update();
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(update);
  });

  /* ---------- Bandeau journée de santé : masquable ---------- */
  document.querySelectorAll('.js-hday').forEach(function (bar) {
    var key = 'ps-hday-' + bar.getAttribute('data-id');
    if (store.get(key)) { bar.remove(); return; }
    var close = bar.querySelector('.js-hday-close');
    if (close) close.addEventListener('click', function () { store.set(key, '1'); bar.remove(); });
  });

  /* ---------- « Cet article vous a-t-il aidé ? » ---------- */
  document.querySelectorAll('.js-helpful').forEach(function (box) {
    var url = box.getAttribute('data-url');
    var thanks = box.querySelector('.js-helpful-thanks');
    box.querySelectorAll('[data-utile]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var utile = btn.getAttribute('data-utile') === '1';
        box.querySelectorAll('[data-utile]').forEach(function (b) { b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'); });
        postJSON(url, { utile: utile }).catch(function () {});
        if (thanks) {
          thanks.hidden = false;
          thanks.querySelector('.js-helpful-yes').hidden = !utile;
          thanks.querySelector('.js-helpful-no').hidden = utile;
        }
      });
    });
  });

  /* ---------- Quiz de l'accueil : réponse immédiate ---------- */
  document.querySelectorAll('.js-quiz-teaser').forEach(function (box) {
    var answer = box.getAttribute('data-answer') === '1';
    var out = box.querySelector('.js-teaser-answer');
    box.querySelectorAll('[data-pick]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var ok = (btn.getAttribute('data-pick') === '1') === answer;
        out.querySelector('strong').textContent = (ok ? 'Bonne réponse ! ' : 'Raté ! ') + 'C\'est ' + (answer ? 'vrai.' : 'faux.');
        out.hidden = false;
        box.querySelector('.js-teaser-btns').hidden = true;
        out.focus();
      });
    });
  });
});
