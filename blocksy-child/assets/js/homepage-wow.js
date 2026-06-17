/* Homepage WOW route interactions */
(function () {
  'use strict';

  var root = document.querySelector('.hu-wow');
  if (!root) {
    return;
  }

  var revealEls = root.querySelectorAll('[data-wow-reveal]');
  if (!revealEls.length) {
    return;
  }

  if ('IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -12% 0px', threshold: 0.12 });

    revealEls.forEach(function (el) {
      observer.observe(el);
    });
  } else {
    revealEls.forEach(function (el) {
      el.classList.add('is-visible');
    });
  }

  root.querySelectorAll('.hu-wow-step').forEach(function (step) {
    step.addEventListener('mouseenter', function () {
      step.classList.add('is-active');
    });

    step.addEventListener('mouseleave', function () {
      step.classList.remove('is-active');
    });

    step.addEventListener('focus', function () {
      step.classList.add('is-active');
    });

    step.addEventListener('blur', function () {
      step.classList.remove('is-active');
    });
  });
})();
