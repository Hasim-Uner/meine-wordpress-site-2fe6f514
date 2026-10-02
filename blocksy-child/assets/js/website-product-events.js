/* Matomo event contract; the site's configured Matomo runtime owns transport. */
(function () {
    'use strict';
    window._paq = window._paq || [];
    window._paq.push(['disableCookies']);
    window.HuWebsiteProductEvent = function (name, details) {
        if (['cta_click', 'rechner_change', 'toggle_durchleuchtung', 'form_submit'].indexOf(name) === -1) return;
        var clean = {};
        ['position', 'seiten', 'art', 'tracking', 'modus'].forEach(function (key) {
            if (details && Object.prototype.hasOwnProperty.call(details, key)) clean[key] = details[key];
        });
        window._paq.push(['trackEvent', 'anfrage_website', name, JSON.stringify(clean)]);
    };
})();
