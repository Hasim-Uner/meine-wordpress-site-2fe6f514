const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
