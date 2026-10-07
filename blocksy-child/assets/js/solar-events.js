/* Solar daily counters: no browser storage, identity, referrer or form values. */
(function () {
  'use strict';
  var root = document.querySelector('.strecke-doc');
  var config = window.NexusSolarEventsConfig || {};
  if (!root || !config.endpoint || !config.page || typeof window.fetch !== 'function') return;
  try {
    if (new URL(config.endpoint, window.location.href).origin !== window.location.origin) return;
  } catch (error) { return; }

  var doors = ['marktcheck', 'system', 'sofortkontakt'];
  var sent = 0;

  function send(event, door) {
    if (sent >= 100 || !/^[a-z0-9_]{1,64}$/.test(event)) return;
    sent += 1;
    window.fetch(config.endpoint, {
      method: 'POST',
      credentials: 'omit',
      referrerPolicy: 'no-referrer',
      keepalive: true,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ event: event, door: door, page: config.page, day: new Date().toISOString().slice(0, 10) })
    }).catch(function () {});
  }

  function doorFor(element) {
    if (!element || typeof element.closest !== 'function') return '';

    var explicit = element.closest('[data-door]');
    if (explicit) {
      var explicitDoor = explicit.getAttribute('data-door');
      if (doors.indexOf(explicitDoor) >= 0) return explicitDoor;
    }

    if (element.closest('[data-system-configurator], #einstieg')) return 'system';
    if (element.closest('#marktcheck')) return 'marktcheck';
    if (element.closest('#sofortkontakt')) return 'sofortkontakt';

    var link = element.closest('a[href]');
    if (link) {
      try {
        var url = new URL(link.href, window.location.href);
        if (url.searchParams.get('focus') === 'audit_scope') return 'marktcheck';
        if (url.searchParams.get('focus') === 'energy_system') return 'system';
        if (url.searchParams.get('focus') === 'response_setup') return 'sofortkontakt';
      } catch (error) {}
    }

    return '';
  }

  root.addEventListener('click', function (event) {
    var element = event.target;
    if (!element || typeof element.closest !== 'function') return;
    var action = element.closest('[data-track-action]');
    if (action) send(action.getAttribute('data-track-action'), doorFor(element));
  });

  // Der Fokus-Header steht ausserhalb von .strecke-doc.
  document.addEventListener('click', function (event) {
    var link = event.target && typeof event.target.closest === 'function'
      ? event.target.closest('.leiste [data-door]')
      : null;
    if (!link) return;
    var action = link.getAttribute('data-track-action');
    var door = link.getAttribute('data-door');
    if (action && doors.indexOf(door) >= 0) send(action, door);
  });
}());
