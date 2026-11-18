(() => {
  const monitor = document.querySelector('[data-monitor-url]');
  if (!monitor || typeof fetch !== 'function') return;

  const statusLine = monitor.querySelector('[data-monitor-status]');
  if (!statusLine) return;

  fetch(monitor.dataset.monitorUrl, { credentials: 'same-origin' })
    .then((response) => response.ok ? response.json() : null)
    .then((data) => {
      const status = data?.enabled ? data.anfragen : null;
      if (!status || !['ok', 'warn', 'fail'].includes(status.state) || typeof status.label !== 'string') return;

      const checkedAt = new Date(status.at);
      if (!status.at || Number.isNaN(checkedAt.getTime())) return;

      const date = new Intl.DateTimeFormat('de-DE', {
        dateStyle: 'short',
        timeZone: 'Europe/Berlin',
      }).format(checkedAt);

      statusLine.textContent = status.label + ' · ' + date;
      statusLine.dataset.state = status.state;
    })
    .catch(() => {
      // The public monitor link remains usable if the status endpoint is unavailable.
    });
})();
