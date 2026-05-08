/**
 * /solar-waermepumpen-leadgenerierung/
 * Architecture preview — rotates the active step every 1.8s.
 * Pauses when off-screen, respects prefers-reduced-motion.
 */
(function () {
  'use strict';

  var ROTATE_MS = 1800;
  var ACTIVE_CLASS = 'is-active';

  function init() {
    var container = document.querySelector('[data-solar-arch-rows]');
    if (!container) return;

    var rows = Array.prototype.slice.call(
      container.querySelectorAll('.solar-arch__row')
    );
    if (rows.length < 2) return;

    var reduceMotion = window.matchMedia
      && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion) return;

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
      if (document.hidden) {
        stop();
      } else {
        start();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
