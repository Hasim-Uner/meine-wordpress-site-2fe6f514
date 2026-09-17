(function () {
    'use strict';

    function normalizeContext(value) {
        var context = String(value || '').toLowerCase().trim();

        if (['agency', 'agentur', 'whitelabel', 'white-label'].indexOf(context) !== -1) {
            return 'agency';
        }

        if (['energy', 'solar', 'pv', 'waermepumpe', 'wärmepumpe'].indexOf(context) !== -1) {
            return 'energy';
        }

        if (['tracking', 'ga4', 'gtm', 'sst', 'server-side-tracking'].indexOf(context) !== -1) {
            return 'tracking';
        }

        if (['project', 'projekt', 'wordpress', 'direct'].indexOf(context) !== -1) {
            return 'project';
        }

        return '';
    }

    function contextFromReferrer() {
        if (!document.referrer) {
            return '';
        }

        try {
            var referrer = new URL(document.referrer, window.location.href);
            if (referrer.origin !== window.location.origin) {
                return '';
            }

            var path = referrer.pathname.toLowerCase();

            if (
                path.indexOf('/server-side-tracking-b2b/') !== -1 ||
                path.indexOf('/ga4-tracking-setup/') !== -1
            ) {
                return 'tracking';
            }

            if (path.indexOf('/whitelabel-retainer/') !== -1) {
                return 'agency';
            }

            if (
                /\/(solar-waermepumpen-leadgenerierung|case-study-solar-leadgenerierung|solar-leads-|cost-per-lead-photovoltaik|waermepumpen-leads|lead-funnel-solar|qualifizierte-pv-anfragen|kunden-gewinnen-solarteure|b2b-solar-leads|aroundhome-solar|checkfox-solar|wattfox-solar|daa-photovoltaik)/.test(path)
            ) {
                return 'energy';
            }
        } catch (error) {
            return '';
        }

        return 'project';
    }

    function resolveContext() {
        var explicitContext = '';

        try {
            explicitContext = normalizeContext(new URLSearchParams(window.location.search).get('from'));
        } catch (error) {
            explicitContext = '';
        }

        return explicitContext || contextFromReferrer() || 'project';
    }

    function setLinkLabel(link, label) {
        if (!link || !label) {
            return;
        }

        link.textContent = label + ' ';
        var arrow = document.createElement('span');
        arrow.setAttribute('aria-hidden', 'true');
        arrow.textContent = '→';
        link.appendChild(arrow);
    }

    function applyTrackingVariant(row) {
        if (!row || !row.dataset.contextUrlTracking) {
            return;
        }

        var kicker = row.querySelector('.erg-next-kicker');
        var title = row.querySelector('.erg-next-title');
        var desc = row.querySelector('.erg-next-desc');
        var link = row.querySelector('.erg-next-link');
        var note = row.querySelector('.erg-next-note');

        if (kicker && row.dataset.contextKickerTracking) {
            kicker.textContent = row.dataset.contextKickerTracking;
        }
        if (title && row.dataset.contextTitleTracking) {
            title.textContent = row.dataset.contextTitleTracking;
        }
        if (desc && row.dataset.contextDescTracking) {
            desc.textContent = row.dataset.contextDescTracking;
        }
        if (link) {
            link.href = row.dataset.contextUrlTracking;
            link.dataset.funnelContext = 'tracking';
            setLinkLabel(link, row.dataset.contextLabelTracking);
        }
        if (note && row.dataset.contextNoteTracking) {
            note.textContent = row.dataset.contextNoteTracking;
        }
    }

    function initResultsRouting() {
        var section = document.getElementById('weiter');
        if (!section) {
            return;
        }

        var list = section.querySelector('[data-results-next-steps]');
        if (!list) {
            return;
        }

        var rows = Array.prototype.slice.call(list.querySelectorAll('[data-funnel-kind]'));
        if (!rows.length) {
            return;
        }

        var context = resolveContext();
        var targetKind = context === 'agency' || context === 'energy' ? context : 'project';
        var primaryRow = null;

        rows.forEach(function (row) {
            row.classList.remove('erg-next-row--primary');
            row.removeAttribute('aria-current');

            if (row.getAttribute('data-funnel-kind') === targetKind) {
                primaryRow = row;
            }
        });

        if (!primaryRow) {
            primaryRow = rows[0];
            context = 'project';
        }

        if (context === 'tracking') {
            applyTrackingVariant(primaryRow);
        }

        primaryRow.classList.add('erg-next-row--primary');
        primaryRow.setAttribute('aria-current', 'true');
        primaryRow.dataset.funnelContext = context;

        var primaryLink = primaryRow.querySelector('.erg-next-link');
        if (primaryLink) {
            primaryLink.dataset.funnelContext = context;
        }

        if (list.firstElementChild !== primaryRow) {
            list.insertBefore(primaryRow, list.firstElementChild);
        }

        section.dataset.funnelContext = context;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initResultsRouting, { once: true });
    } else {
        initResultsRouting();
    }
})();
