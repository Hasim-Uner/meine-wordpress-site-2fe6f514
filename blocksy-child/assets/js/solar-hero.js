/**
 * /solar-waermepumpen-leadgenerierung/ — interactive layer
 *
 * - Architecture preview rotates the active step every 1.8s
 * - FAQ accordion enforces single-open behaviour
 * - Sticky CTA bar appears after first scroll past the hero
 * - Reveal-on-scroll for [data-reveal] elements
 *
 * All effects pause when off-screen and respect
 * prefers-reduced-motion.
 */
(function () {
  'use strict';

  var ROTATE_MS = 1800;
  var ACTIVE_CLASS = 'is-active';
  var REVEAL_CLASS = 'is-revealed';
  var STICKY_VISIBLE_CLASS = 'is-visible';
  var STICKY_THRESHOLD = 1100;

  var reduceMotion = window.matchMedia
    && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function initArchPreview() {
    var container = document.querySelector('[data-solar-arch-rows]');
    if (!container) return;

    var rows = Array.prototype.slice.call(
      container.querySelectorAll('.solar-arch__row')
    );
    if (rows.length < 2) return;

    if (reduceMotion) {
      rows.forEach(function (row) { row.classList.add(ACTIVE_CLASS); });
      return;
    }

    var active = 0;
    rows.forEach(function (row, i) {
      row.classList.toggle(ACTIVE_CLASS, i === 0);
    });

    var timer = null;

    function tick() {
      rows[active].classList.remove(ACTIVE_CLASS);
      active = (active + 1) % rows.length;
      rows[active].classList.add(ACTIVE_CLASS);
    }

    function start() {
      if (timer) return;
      timer = window.setInterval(tick, ROTATE_MS);
    }

    function stop() {
      if (!timer) return;
      window.clearInterval(timer);
      timer = null;
    }

    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            start();
          } else {
            stop();
          }
        });
      }, { threshold: 0.2 });
      io.observe(container);
    } else {
      start();
    }

    document.addEventListener('visibilitychange', function () {
      if (document.hidden) stop();
      else start();
    });
  }

  function initFaqSingleOpen() {
    var container = document.querySelector('[data-solar-faq]');
    if (!container) return;

    var items = Array.prototype.slice.call(
      container.querySelectorAll('.solar-faq__item')
    );

    items.forEach(function (item) {
      item.addEventListener('toggle', function () {
        if (!item.open) return;
        items.forEach(function (other) {
          if (other !== item) other.open = false;
        });
      });
    });
  }

  function initReveal() {
    var els = Array.prototype.slice.call(
      document.querySelectorAll('[data-reveal]')
    );
    if (!els.length) return;

    if (reduceMotion || !('IntersectionObserver' in window)) {
      els.forEach(function (el) { el.classList.add(REVEAL_CLASS); });
      return;
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add(REVEAL_CLASS);
        io.unobserve(entry.target);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

    els.forEach(function (el) { io.observe(el); });
  }

  function initStickyCta() {
    var bar = document.querySelector('[data-solar-sticky]');
    if (!bar) return;

    var ticking = false;

    function update() {
      ticking = false;
      var show = window.scrollY > STICKY_THRESHOLD;
      bar.classList.toggle(STICKY_VISIBLE_CLASS, show);
      bar.setAttribute('aria-hidden', show ? 'false' : 'true');
    }

    function onScroll() {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(update);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    update();
  }

  function init() {
    initArchPreview();
    initFaqSingleOpen();
    initReveal();
    initStickyCta();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
