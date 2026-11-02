/**
 * PhyloSanitas — petits comportements des formulaires d'administration
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  /* Compteur de caractères : <input data-count="255"> + <span class="field-count" data-count-for="id"> */
  document.querySelectorAll('[data-count]').forEach(function (input) {
    var max = parseInt(input.getAttribute('data-count'), 10);
    var out = document.querySelector('[data-count-for="' + input.id + '"]');
    if (!out) return;
    var update = function () {
      var n = input.value.length;
      out.textContent = n + ' / ' + max;
      out.classList.toggle('is-warn', n > max * 0.85 && n <= max);
      out.classList.toggle('is-over', n > max);
    };
    input.addEventListener('input', update);
    update();
  });

  /* Zones d'upload : aperçu immédiat + glisser-déposer */
  document.querySelectorAll('.dropzone').forEach(function (zone) {
    var input = zone.querySelector('input[type=file]');
    var img = zone.querySelector('.dropzone__preview');
    if (!input) return;

    var show = function (file) {
      if (!file || !file.type.startsWith('image/')) return;
      var reader = new FileReader();
      reader.onload = function (e) {
        if (!img) {
          img = document.createElement('img');
          img.className = 'dropzone__preview' + (zone.dataset.fit === 'contain' ? ' dropzone__preview--contain' : '');
          img.alt = 'Aperçu';
          zone.insertBefore(img, zone.firstChild);
        }
        img.src = e.target.result;
        zone.classList.add('has-image');
        zone.dispatchEvent(new CustomEvent('preview', { detail: e.target.result, bubbles: true }));
      };
      reader.readAsDataURL(file);
    };

    input.addEventListener('change', function () { show(input.files[0]); });
    ['dragenter', 'dragover'].forEach(function (ev) {
      zone.addEventListener(ev, function () { zone.classList.add('is-drag'); });
    });
    ['dragleave', 'drop'].forEach(function (ev) {
      zone.addEventListener(ev, function () { zone.classList.remove('is-drag'); });
    });
  });

  /* Affichage conditionnel : <div data-show-when="type=image"> */
  var conditionals = document.querySelectorAll('[data-show-when]');
  var refreshConditionals = function () {
    conditionals.forEach(function (el) {
      var rule = el.getAttribute('data-show-when').split('=');
      var checked = document.querySelector('[name="' + rule[0] + '"]:checked') || document.querySelector('select[name="' + rule[0] + '"]');
      var visible = checked && checked.value === rule[1];
      el.hidden = !visible;
      el.querySelectorAll('input, textarea, select').forEach(function (f) { f.disabled = !visible; });
    });
  };
  if (conditionals.length) {
    document.addEventListener('change', refreshConditionals);
    refreshConditionals();
  }

  /* Menus « ⋯ » des tableaux : positionnement fixe pour ne pas être coupés par le défilement du tableau.
     Délégation (phase de capture) : fonctionne aussi pour les lignes redessinées par la pagination. */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.row-menu [data-bs-toggle="dropdown"]');
    if (!btn || typeof bootstrap === 'undefined' || bootstrap.Dropdown.getInstance(btn)) return;
    new bootstrap.Dropdown(btn, { popperConfig: function (cfg) { return Object.assign({}, cfg, { strategy: 'fixed' }); } });
  }, true);

  /* Confirmation avant suppression : <form data-confirm="Message"> */
  // Délégation : fonctionne aussi pour les lignes redessinées par la pagination des tableaux
  document.addEventListener('submit', function (e) {
    var form = e.target.closest('form[data-confirm]');
    if (form && !window.confirm(form.getAttribute('data-confirm'))) {
      e.preventDefault();
      e.stopImmediatePropagation();
    }
  }, true);

  /* Empêche le double envoi d'un formulaire */
  document.querySelectorAll('form[data-once]').forEach(function (form) {
    form.addEventListener('submit', function () {
      if (form.checkValidity && !form.checkValidity()) return;
      form.querySelectorAll('button[type=submit]').forEach(function (btn) {
        btn.disabled = true;
        btn.dataset.label = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Enregistrement…';
      });
    });
  });
});
