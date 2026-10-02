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
    var state = { seiten: 3, art: 'neubau', tracking: false };
    var media = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : { matches: true };
    var eur = function (number) { return number.toLocaleString('de-DE') + '\u00a0€'; };
    var weeks = function () { return (state.seiten <= 2 ? 2 : state.seiten <= 5 ? 3 : 4) + (state.art === 'relaunch' ? 1 : 0); };
    var scope = function () { return { seiten: state.seiten, art: state.art, tracking: state.tracking ? 1 : 0 }; };
    var event = function (name, details) {
        if (window.HuWebsiteProductEvent) window.HuWebsiteProductEvent(name, details);
    };
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
    function calculate(changed) {
        var extra = state.seiten - 1, total = base + extra * pagePrice + (state.tracking ? trackingPrice : 0);
        $('#weitere').textContent = extra;
        $('#minus').disabled = state.seiten <= 1; $('#plus').disabled = state.seiten >= max;
        $('#betrag-weitere').textContent = eur(extra * pagePrice);
        $('#gesamt').textContent = eur(total);
        $('#rechnung').textContent = eur(base) + (extra ? ' + ' + extra + ' × ' + eur(pagePrice) : '') + (state.tracking ? ' + ' + eur(trackingPrice) + ' Tracking' : '');
        $('#betrag-tracking').classList.toggle('inkl', !state.tracking);
        $('#pos-weiterleitung').classList.toggle('aus', state.art !== 'relaunch');
        $$('.groessen button').forEach(function (button) { button.setAttribute('aria-pressed', String(Number(button.dataset.seiten) === state.seiten)); });
        $$('.art button').forEach(function (button) { button.setAttribute('aria-pressed', String(button.dataset.art === state.art)); });
        $('#leiste-text').textContent = state.seiten + (state.seiten === 1 ? ' Seite' : ' Seiten') + ' · ' + (state.art === 'relaunch' ? 'Relaunch' : 'Neubau') + (state.tracking ? ' · Tracking' : '') + ' · ' + eur(total) + ' netto · ' + weeks() + ' Wochen';
        timeline();
        link.searchParams.set('seiten', state.seiten); link.searchParams.set('art', state.art);
        if (state.tracking) link.searchParams.set('tracking', '1'); else link.searchParams.delete('tracking');
        $$('[data-website-cta]').forEach(function (cta) { cta.href = link.href; });
        if (changed) event('rechner_change', scope());
    }
    $('#minus').addEventListener('click', function () { if (state.seiten > 1) { state.seiten--; calculate(true); } });
    $('#plus').addEventListener('click', function () { if (state.seiten < max) { state.seiten++; calculate(true); } });
    $$('.groessen button').forEach(function (button) {
        button.addEventListener('click', function () { if (state.seiten !== Number(button.dataset.seiten)) { state.seiten = Number(button.dataset.seiten); calculate(true); } });
    });
    $$('.art button').forEach(function (button) {
        button.addEventListener('click', function () { if (state.art !== button.dataset.art) { state.art = button.dataset.art; calculate(true); } });
    });
    $('#tracking').addEventListener('change', function () { state.tracking = this.checked; calculate(true); });
    $$('[data-website-cta]').forEach(function (cta) {
        cta.addEventListener('click', function () { event('cta_click', Object.assign({ position: cta.dataset.websiteCta }, scope())); });
    });
    var animation;
    function mode(name, changed) {
        $('#durch').dataset.modus = name;
        $$('.schalter button').forEach(function (button) { button.setAttribute('aria-pressed', String(button.dataset.modus === name)); });
        $('.geraet').style.setProperty('--pin2', name === 'klassisch' ? 'var(--stempel)' : 'var(--aw-paper)');
        if (animation) animation.cancel();
        var bar = $('#ladebalken');
        if (!media.matches && bar.animate) animation = bar.animate([{ transform: 'scaleX(0)' }, { transform: 'scaleX(1)' }], { duration: name === 'klassisch' ? 2400 : 450, easing: 'ease-in-out' });
        if (changed) event('toggle_durchleuchtung', { modus: name });
    }
    $$('.schalter button').forEach(function (button) {
        button.addEventListener('click', function () { if ($('#durch').dataset.modus !== button.dataset.modus) mode(button.dataset.modus, true); });
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
    calculate(false);
    $('#anzahl').textContent = $$('.gruppe li').length;
    root.classList.add('aw-ready');
    $$('[data-website-controls]').forEach(function (control) { control.hidden = false; });
    mode('klassisch', false);
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
