/* Die Anfrage-Website. Progressive enhancement; no cookies or stored visitor state. */
(function () {
    'use strict';
    var root = document.querySelector('[data-website-product]');
    if (!root) return;
    var $ = function (selector) { return root.querySelector(selector); };
    var $$ = function (selector) { return Array.prototype.slice.call(root.querySelectorAll(selector)); };
    var base = Number(root.dataset.base), pagePrice = Number(root.dataset.pagePrice);
    var trackingPrice = Number(root.dataset.trackingPrice), max = Number(root.dataset.maxPages);
    if (![base, pagePrice, trackingPrice, max].every(Number.isFinite)) return;
    var state = { seiten: 3, art: 'neubau', texte: true, tracking: false, screendesign: false, crm: false };
    var media = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : { matches: true };
    var eur = function (number) { return number.toLocaleString('de-DE') + '\u00a0€'; };
    var weeks = function () { return (state.seiten <= 2 ? 2 : state.seiten <= 5 ? 3 : 4) + (state.art === 'relaunch' ? 1 : 0); };
    var link = new URL($('[data-website-cta]').href, window.location.href);
    function timeline() {
        var total = weeks(), phases = [], week = 1;
        if (state.art === 'relaunch') phases.push({ name: 'Bestandsaufnahme und Weiterleitungsplan', duration: 1, className: 'relaunch' });
        phases.push({ name: 'Umsetzung auf der Testumgebung', duration: total - 1 - (state.art === 'relaunch' ? 1 : 0), className: '' });
        phases.push({ name: 'Korrekturen, Abnahme, Livegang', duration: 1, className: 'ende' });
        $('#bahn').replaceChildren();
        phases.forEach(function (phase) {
            var box = document.createElement('div'), label = document.createElement('span'), title = document.createElement('b');
            box.className = 'phase ' + phase.className;
            box.style.setProperty('--w', phase.duration);
            label.className = 'mono';
            label.textContent = 'Woche ' + week + (phase.duration > 1 ? '–' + (week + phase.duration - 1) : '');
            title.textContent = phase.name;
            box.append(label, title); $('#bahn').append(box); week += phase.duration;
        });
        $('#zeit-gesamt').textContent = total + ' Wochen Bauzeit';
        $('#zeit-umfang').textContent = 'Ihr Umfang: ' + state.seiten + (state.seiten === 1 ? ' Seite' : ' Seiten') + ' · ' + (state.art === 'relaunch' ? 'Relaunch' : 'Neubau');
        $('#bauzeit').textContent = total + ' Wochen';
        $('#hero-bauzeit').textContent = 'Ihr Umfang: ' + total + ' Wochen.';
    }
    function calculate() {
        var extra = state.seiten - 1, total = base + extra * pagePrice + (state.tracking ? trackingPrice : 0);
        var custom = [];
        if (state.screendesign) custom.push('Screendesign');
        if (state.crm) custom.push('CRM-Anbindung');
        var scope = state.seiten + (state.seiten === 1 ? ' Seite' : ' Seiten') + ' · ' + (state.art === 'relaunch' ? 'Relaunch' : 'Neubau');
        $('#seiten').textContent = state.seiten;
        $('#weitere').textContent = extra;
        $('#minus').disabled = state.seiten <= 1; $('#plus').disabled = state.seiten >= max;
        $('#betrag-weitere').textContent = eur(extra * pagePrice);
        $('#gesamt').textContent = eur(total);
        $('#rechnung').textContent = eur(base) + (extra ? ' + ' + extra + ' × ' + eur(pagePrice) : '') + (state.tracking ? ' + ' + eur(trackingPrice) + ' Tracking' : '');
        $('#auswahl-art').textContent = scope;
        $('#auswahl-texte').textContent = state.texte ? 'Texte erstellen lassen' : 'Eigene Texte einpflegen';
        $('#text-hinweis').textContent = state.texte ? 'Sie können auch eigene Texte liefern. Der Preis bleibt gleich.' : 'Ihre vorhandenen Texte werden eingepflegt und verfeinert. Der Preis bleibt gleich.';
        ['tracking', 'screendesign', 'crm'].forEach(function (key) { $('#summary-' + key).hidden = !state[key]; });
        $('#preis-label').textContent = custom.length ? 'Festpreis ohne individuelle Extras' : 'Ihr Festpreis';
        $('#angebot-hinweis').hidden = !custom.length;
        $('#angebot-hinweis').textContent = custom.length ? 'Zuzüglich ' + custom.join(' und ') + '. Diese Extras werden separat angeboten; der vollständige Gesamtpreis steht im Angebot.' : '';
        $('#pos-weiterleitung').classList.toggle('aus', state.art !== 'relaunch');
        $$('.groessen button').forEach(function (button) { button.setAttribute('aria-pressed', String(Number(button.dataset.seiten) === state.seiten)); });
        $$('.art button').forEach(function (button) { button.setAttribute('aria-pressed', String(button.dataset.art === state.art)); });
        var selected = [state.texte ? 'Texte inklusive' : 'Eigene Texte'];
        if (state.tracking) selected.push('Tracking');
        selected = selected.concat(custom);
        $('#leiste-text').textContent = scope + ' · ' + selected.join(' · ');
        $('#leiste-preis').textContent = eur(total) + ' netto' + (custom.length ? ' + ' + custom.length + (custom.length === 1 ? ' Extra nach Angebot' : ' Extras nach Angebot') : '');
        $('#konfiguration-status').textContent = scope + ' · ' + selected.join(' · ') + ' · ' + eur(total) + ' netto' + (custom.length ? ', zuzüglich individueller Extras nach Angebot.' : '.');
        timeline();
        link.searchParams.set('seiten', state.seiten); link.searchParams.set('art', state.art);
        link.searchParams.set('texte', state.texte ? '1' : '0');
        ['tracking', 'screendesign', 'crm'].forEach(function (key) {
            if (state[key]) link.searchParams.set(key, '1'); else link.searchParams.delete(key);
        });
        $$('[data-website-cta]').forEach(function (cta) { cta.href = link.href; });
    }
    $('#minus').addEventListener('click', function () { if (state.seiten > 1) { state.seiten--; calculate(); } });
    $('#plus').addEventListener('click', function () { if (state.seiten < max) { state.seiten++; calculate(); } });
    $$('.groessen button').forEach(function (button) {
        button.addEventListener('click', function () { if (state.seiten !== Number(button.dataset.seiten)) { state.seiten = Number(button.dataset.seiten); calculate(); } });
    });
    $$('.art button').forEach(function (button) {
        button.addEventListener('click', function () { if (state.art !== button.dataset.art) { state.art = button.dataset.art; calculate(); } });
    });
    ['texte', 'tracking', 'screendesign', 'crm'].forEach(function (key) {
        $('#' + key).addEventListener('change', function () { state[key] = this.checked; calculate(); });
    });
    var animation;
    function mode(name) {
        $('#durch').dataset.modus = name;
        $$('.schalter button').forEach(function (button) { button.setAttribute('aria-pressed', String(button.dataset.modus === name)); });
        $('.geraet').style.setProperty('--pin2', name === 'klassisch' ? 'var(--stempel)' : 'var(--aw-paper)');
        if (animation) animation.cancel();
        var bar = $('#ladebalken');
        if (!media.matches && bar.animate) animation = bar.animate([{ transform: 'scaleX(0)' }, { transform: 'scaleX(1)' }], { duration: name === 'klassisch' ? 2400 : 450, easing: 'ease-in-out' });
    }
    $$('.schalter button').forEach(function (button) {
        button.addEventListener('click', function () { if ($('#durch').dataset.modus !== button.dataset.modus) mode(button.dataset.modus); });
    });
    function cancelMotion() { if (media.matches && animation) animation.cancel(); }
    if (media.addEventListener) media.addEventListener('change', cancelMotion);
    $$('.stellen li').forEach(function (item) {
        function highlight() {
            var active = item.matches(':hover') || document.activeElement === item;
            $$('.pin[data-pin="' + item.dataset.pin + '"]').forEach(function (pin) { pin.classList.toggle('an', active); });
        }
        ['mouseenter', 'mouseleave', 'focus', 'blur'].forEach(function (name) { item.addEventListener(name, highlight); });
    });
    calculate();
    root.classList.add('aw-ready');
    $$('[data-website-controls]').forEach(function (control) { control.hidden = false; });
    mode('klassisch');
    // Hide the CTA from both keyboard and accessibility tree at the hero and close.
    var bar = $('#leiste'), stickyCTA = $('#cta-leiste'), scheduled = false;
    function updateSticky() {
        scheduled = false;
        var visible = $('#hero').getBoundingClientRect().bottom <= 0 && $('#anfrage').getBoundingClientRect().top >= window.innerHeight;
        if (!visible && bar.contains(document.activeElement)) $('[data-website-cta="abschluss"]').focus({ preventScroll: true });
        bar.hidden = !visible; bar.inert = !visible;
        bar.classList.toggle('zeigen', visible); bar.setAttribute('aria-hidden', String(!visible)); stickyCTA.tabIndex = visible ? 0 : -1;
    }
    function scheduleSticky() { if (!scheduled) { scheduled = true; requestAnimationFrame(updateSticky); } }
    window.addEventListener('scroll', scheduleSticky, { passive: true });
    window.addEventListener('resize', scheduleSticky, { passive: true });
    if (window.IntersectionObserver) {
        var observer = new IntersectionObserver(scheduleSticky);
        observer.observe($('#hero')); observer.observe($('#anfrage'));
    }
    updateSticky();
})();
