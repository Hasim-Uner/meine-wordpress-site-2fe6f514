/* hasim-uener.js
   Personenseite /hasim-uener/ — einzige Aufgabe: die vier Stationen
   beim Scrollen nacheinander einblenden.

   Gleiche Utility-Basis wie [data-sol-reveal] auf der Solar-Lead-Seite:
   die Gate-Klasse html.hu-about-anim kommt erst nach Reduced-Motion- und
   IntersectionObserver-Pruefung. Ohne JS, ohne Observer oder bei Reduced
   Motion existiert kein versteckter Ausgangszustand — die Karten stehen
   sofort da. Bereits sichtbare Karten werden vor dem Gate als .is-in
   markiert, damit ein Reload mitten auf der Seite nicht blinkt. */
(function () {
  'use strict';

  var ROOT_SELECTOR = '.hu-about';

  function setupReveal() {
    var reduced = false;
    try { reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches; } catch (e) {}
    if (reduced || !('IntersectionObserver' in window)) return;

    var root = document.querySelector(ROOT_SELECTOR);
    if (!root) return;

    var reveals = [].slice.call(root.querySelectorAll('[data-hu-reveal]'));
    if (!reveals.length) return;

    var vh = window.innerHeight || document.documentElement.clientHeight || 0;
    var pending = [];

    reveals.forEach(function (el) {
      if (el.getBoundingClientRect().top < vh * 0.88) el.classList.add('is-in');
      else pending.push(el);
    });

    document.documentElement.classList.add('hu-about-anim');
    if (!pending.length) return;

    try {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          io.unobserve(entry.target);
          entry.target.classList.add('is-in');
        });
      }, { rootMargin: '0px 0px -12% 0px' });
      pending.forEach(function (el) { io.observe(el); });
    } catch (e) {
      document.documentElement.classList.remove('hu-about-anim');
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupReveal);
  } else {
    setupReveal();
  }
})();
