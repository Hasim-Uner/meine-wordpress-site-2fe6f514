/* solar-leadgenerierung-solara.js
   - FAQ-Accordion (eine offen zur Zeit)
   - Sticky Mobile-CTA (erscheint nach Hero)
   - Sonnenstrahlen werden client-seitig gemountet (24 Rays)
   Vanilla, ohne Dependencies. Touch- und Keyboard-freundlich. */
(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState !== 'loading') { fn(); return; }
    document.addEventListener('DOMContentLoaded', fn, { once: true });
  }

  function mountSunRays() {
    var hosts = document.querySelectorAll('.solara-landing [data-sol-rays]');
    if (!hosts.length) return;
    hosts.forEach(function (host) {
      if (host.dataset.solRaysMounted === '1') return;
      var frag = document.createDocumentFragment();
      for (var i = 0; i < 24; i++) {
        var ray = document.createElement('span');
        ray.className = 'sol-hero-sun-ray';
        ray.style.transform = 'rotate(' + (i * 15) + 'deg)';
        ray.setAttribute('aria-hidden', 'true');
        frag.appendChild(ray);
      }
      host.appendChild(frag);
      host.dataset.solRaysMounted = '1';
    });
  }

  function setupFaq() {
    var items = document.querySelectorAll('.solara-landing .sol-faq-item');
    if (!items.length) return;

    items.forEach(function (item) {
      var btn = item.querySelector('.sol-faq-q');
      var ans = item.querySelector('.sol-faq-a');
      if (!btn || !ans) return;

      var id = 'sol-faq-a-' + Math.random().toString(36).slice(2, 8);
      ans.id = id;
      btn.setAttribute('aria-controls', id);
      btn.setAttribute('aria-expanded', item.classList.contains('is-open') ? 'true' : 'false');

      btn.addEventListener('click', function () {
        var isOpen = item.classList.contains('is-open');
        // close all
        items.forEach(function (other) {
          other.classList.remove('is-open');
          var otherBtn = other.querySelector('.sol-faq-q');
          if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
        });
        // open clicked if it was closed
        if (!isOpen) {
          item.classList.add('is-open');
          btn.setAttribute('aria-expanded', 'true');
        }
      });
    });
  }

  function setupStickyCta() {
    var sticky = document.querySelector('.solara-landing .sol-sticky-cta');
    var hero   = document.querySelector('.solara-landing .sol-hero');
    if (!sticky || !hero) return;

    if (!('IntersectionObserver' in window)) {
      sticky.classList.add('is-visible');
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          sticky.classList.remove('is-visible');
        } else {
          // only when hero is above viewport (we've scrolled past it)
          if (entry.boundingClientRect.top < 0) {
            sticky.classList.add('is-visible');
          }
        }
      });
    }, { threshold: 0 });
    io.observe(hero);
  }

  function setupScrollAnchor() {
    document.querySelectorAll('.solara-landing a[href^="#"]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var hash = a.getAttribute('href');
        if (!hash || hash.length < 2) return;
        var target = document.querySelector(hash);
        if (!target) return;
        e.preventDefault();
        var top = target.getBoundingClientRect().top + window.scrollY - 20;
        window.scrollTo({ top: top, behavior: 'smooth' });
      });
    });
  }

  ready(function () {
    mountSunRays();
    setupFaq();
    setupStickyCta();
    setupScrollAnchor();
  });
})();
