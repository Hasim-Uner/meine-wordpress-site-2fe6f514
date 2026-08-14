/* Homepage Redesign — Interactive behaviour */
(function () {
  'use strict';

  /* ── Scroll reveal ─────────────────────────────────────── */
  var revealEls = document.querySelectorAll('.hu-hp .hu-reveal');
  if (revealEls.length && 'IntersectionObserver' in window) {
    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('in'); }
      });
    }, { threshold: 0.1 });
    revealEls.forEach(function (el) { obs.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('in'); });
  }

  /* ── Smooth scroll for hash anchors ───────────────────── */
  /* Der Fokus muss mitwandern: sonst springt die Seite optisch zum Ziel,
     die Tastatur steht aber weiter im Hero und tabbt an der Sektion
     vorbei. Reduced Motion bekommt den Sprung ohne Animation. */
  var reduceMotion = window.matchMedia
    ? window.matchMedia('(prefers-reduced-motion: reduce)')
    : null;

  document.querySelectorAll('.hu-hp a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var id = a.getAttribute('href').slice(1);
      if (!id) return;
      var target = document.getElementById(id);
      if (!target) return;
      e.preventDefault();

      target.scrollIntoView({
        behavior: (reduceMotion && reduceMotion.matches) ? 'auto' : 'smooth',
        block: 'start'
      });

      if (!target.hasAttribute('tabindex')) {
        target.setAttribute('tabindex', '-1');
      }
      try { target.focus({ preventScroll: true }); } catch (err) { target.focus(); }

      if (window.history && typeof window.history.replaceState === 'function') {
        window.history.replaceState(null, '', '#' + id);
      }
    });
  });

})();
