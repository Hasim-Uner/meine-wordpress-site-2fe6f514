/*
 * Solar / Wärmepumpen Marktcheck — route bootstrap
 *
 * The visible two-step intake lives in solar-marketcheck-compact.js.
 * This stable bootstrap keeps the existing WordPress enqueue handle intact,
 * loads the matching compact stylesheet, then starts the intake controller.
 */
(function () {
  'use strict';

  var current = document.currentScript;
  if (!current || !current.src) {
    var candidates = document.querySelectorAll('script[src*="/assets/js/solar-leadgenerierung-solara.js"]');
    current = candidates.length ? candidates[candidates.length - 1] : null;
  }

  if (!current || !current.src) {
    return;
  }

  var sourceUrl;
  try {
    sourceUrl = new URL(current.src, window.location.href);
  } catch (error) {
    return;
  }

  var styleUrl = new URL('../css/solar-marketcheck-compact.css', sourceUrl);
  var controllerUrl = new URL('solar-marketcheck-compact.js', sourceUrl);
  var version = sourceUrl.searchParams.get('ver');

  if (version) {
    styleUrl.searchParams.set('ver', version + '-mc2');
    controllerUrl.searchParams.set('ver', version + '-mc2');
  }

  function startController() {
    if (document.querySelector('script[data-solar-marketcheck-compact]')) {
      return;
    }

    var script = document.createElement('script');
    script.src = controllerUrl.href;
    script.defer = true;
    script.setAttribute('data-solar-marketcheck-compact', '');
    document.head.appendChild(script);
  }

  if (!document.querySelector('link[data-solar-marketcheck-compact-style]')) {
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = styleUrl.href;
    link.setAttribute('data-solar-marketcheck-compact-style', '');
    link.addEventListener('load', startController, { once: true });
    link.addEventListener('error', startController, { once: true });
    document.head.appendChild(link);
    window.setTimeout(startController, 1500);
  } else {
    startController();
  }
})();
