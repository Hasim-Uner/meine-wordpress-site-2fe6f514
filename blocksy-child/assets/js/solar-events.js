/* Solar daily counters: no browser storage, identity, referrer or form values. */
(function () {
  'use strict';
  var root = document.querySelector('.strecke-doc');
  var config = window.NexusSolarEventsConfig || {};
  if (!root || !config.endpoint || !config.page || typeof window.fetch !== 'function') return;
  try {
    if (new URL(config.endpoint, window.location.href).origin !== window.location.origin) return;
  } catch (error) { return; }

  var doors = ['marktcheck', 'sofortkontakt', 'analyse'];
  var opened = {};
  var reached = false;
  var errors = {};
  var sent = 0;
  var names = ['form_opened', 'form_step_two', 'form_submitted', 'form_validation_error'];

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

  function formEvent(event, door) {
    if (doors.indexOf(door) < 0 || names.indexOf(event) < 0) return;
    if (!opened[door]) {
      opened[door] = true;
      send('form_opened', door);
    }
    if (event === 'form_opened') return;
    if (event === 'form_step_two') {
      if (door !== 'marktcheck' || reached) return;
      reached = true;
    }
    send(event, door);
  }

  function doorFor(element) {
    if (!element || typeof element.closest !== 'function') return '';
    var form = element.closest('[data-order-form]');
    if (form) return form.getAttribute('data-order-form');
    if (element.closest('#sol-quiz-mount')) return 'marktcheck';
    var link = element.closest('a[href]');
    if (link) {
      var hash = new URL(link.href, window.location.href).hash.slice(1);
      if (doors.indexOf(hash) >= 0) return hash;
    }
    return '';
  }

  root.addEventListener('click', function (event) {
    var element = event.target;
    if (!element || typeof element.closest !== 'function') return;
    var action = element.closest('[data-track-action]');
    var door = doorFor(element);
    if (action) send(action.getAttribute('data-track-action'), door);
    if (door) formEvent('form_opened', door);
  });
  root.addEventListener('focusin', function (event) {
    if (!event.target.closest('[data-order-form], #sol-quiz-mount')) return;
    var door = doorFor(event.target);
    if (door) formEvent('form_opened', door);
  });
  root.addEventListener('invalid', function (event) {
    var door = doorFor(event.target);
    if (!door || errors[door]) return;
    errors[door] = true;
    window.setTimeout(function () {
      errors[door] = false;
      formEvent('form_validation_error', door);
    }, 0);
  }, true);
  window.addEventListener('nexus:solar-form', function (event) {
    var detail = event.detail || {};
    formEvent(detail.event, detail.door);
  });

  var hash = window.location.hash.slice(1);
  if (doors.indexOf(hash) >= 0) formEvent('form_opened', hash);
}());
