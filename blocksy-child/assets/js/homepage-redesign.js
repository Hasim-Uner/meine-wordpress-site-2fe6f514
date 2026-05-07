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

  /* ── Diagram animation ─────────────────────────────────── */
  var cards = document.querySelectorAll('.hu-hp .hu-diagram__card');
  if (cards.length) {
    var active = 0;
    var groups = 3;
    function cycleDiagram() {
      cards.forEach(function (c) { c.classList.remove('is-active', 'is-bad', 'is-good'); });
      /* left cards: 0,1,2 → is-bad; right cards: 3,4,5 → is-good */
      if (cards[active]) { cards[active].classList.add('is-active', 'is-bad'); }
      if (cards[active + groups]) { cards[active + groups].classList.add('is-active', 'is-good'); }
      active = (active + 1) % groups;
    }
    cycleDiagram();
    setInterval(cycleDiagram, 2200);
  }

  /* ── FAQ accordion ─────────────────────────────────────── */
  document.querySelectorAll('.hu-hp .hu-faq-item__q').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item = btn.closest('.hu-faq-item');
      if (!item) return;
      var isOpen = item.classList.contains('is-open');
      /* close all */
      document.querySelectorAll('.hu-hp .hu-faq-item.is-open').forEach(function (el) {
        el.classList.remove('is-open');
        el.querySelector('.hu-faq-item__icon').textContent = '+';
      });
      /* open clicked if it was closed */
      if (!isOpen) {
        item.classList.add('is-open');
        item.querySelector('.hu-faq-item__icon').textContent = '−';
      }
    });
  });

  /* ── Smooth scroll for hash anchors ───────────────────── */
  document.querySelectorAll('.hu-hp a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var id = a.getAttribute('href').slice(1);
      if (!id) return;
      var target = document.getElementById(id);
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

})();
