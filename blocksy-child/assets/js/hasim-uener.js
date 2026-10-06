(() => {
  'use strict';

  const root = document.querySelector('[data-about-story]');
  if (!root || !('IntersectionObserver' in window)) {
    return;
  }

  const steps = Array.from(root.querySelectorAll('[data-about-step]'));
  const progress = root.querySelector('[data-about-progress]');
  const indexLinks = Array.from(document.querySelectorAll('.about-index a[href^="#"]'));

  if (!steps.length) {
    return;
  }

  document.documentElement.classList.add('has-about-motion');

  const setActive = (step) => {
    const activeIndex = steps.indexOf(step);
    if (activeIndex < 0) {
      return;
    }

    steps.forEach((item, index) => {
      item.classList.toggle('is-active', index === activeIndex);
    });

    if (progress) {
      const scale = Math.max(0, Math.min(1, (activeIndex + 1) / steps.length));
      progress.style.transform = `scaleY(${scale})`;
    }

    const stepId = step.id;
    const groupedStory = ['geschichte', 'erstes-gespraech', 'zuhoeren'].includes(stepId)
      ? 'geschichte'
      : stepId;

    indexLinks.forEach((link) => {
      const isCurrent = link.getAttribute('href') === `#${groupedStory}`;
      link.classList.toggle('is-current', isCurrent);

      if (isCurrent) {
        link.setAttribute('aria-current', 'location');
      } else {
        link.removeAttribute('aria-current');
      }
    });
  };

  const visible = new Map();

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          visible.set(entry.target, entry.intersectionRatio);
        } else {
          visible.delete(entry.target);
        }
      });

      if (!visible.size) {
        return;
      }

      const active = Array.from(visible.entries())
        .sort((a, b) => b[1] - a[1])[0][0];

      setActive(active);
    },
    {
      rootMargin: '-24% 0px -46% 0px',
      threshold: [0.08, 0.2, 0.35, 0.5, 0.7],
    }
  );

  steps.forEach((step) => observer.observe(step));
  setActive(steps[0]);
})();
