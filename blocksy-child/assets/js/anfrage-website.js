/* Die Anfrage-Website. Progressive enhancement; no cookies or stored visitor state. */
(function () {
    'use strict';
    var root = document.querySelector('[data-website-product]');
    if (!root) return;
    var $ = function (selector) { return root.querySelector(selector); };
    var $$ = function (selector) { return Array.prototype.slice.call(root.querySelectorAll(selector)); };
    var rules;
    try { rules = JSON.parse(root.dataset.websiteRules); } catch (error) { return; }
    if (!rules || !rules.prices || !rules.days || !Object.values(rules.prices).concat(Object.values(rules.days), rules.max_pages).every(Number.isFinite)) return;
    var base = rules.prices.base, pagePrice = rules.prices.page, trackingPrice = rules.prices.tracking, max = rules.max_pages;
    var factors = rules.days;
    var state = { seiten: 3, art: 'neubau', texte: true, tracking: false, design: 'basis', designLayouts: 3, crm: false };
    var automaticLayouts = true;
    var media = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : { matches: true };
    var eur = function (number) { return number.toLocaleString('de-DE') + '\u00a0€'; };
    var days = function (number) { return number + (number === 1 ? ' Werktag' : ' Werktage'); };
    function quote() {
        var components = {
            implementation: factors.first_page + (state.seiten - 1) * factors.extra_page,
            texts: state.texte ? Math.ceil(factors.text_first + (state.seiten - 1) * factors.text_extra) : 0,
            design: state.design === 'neu' ? factors.design_first + (state.designLayouts - 1) * factors.design_extra_layout : 0,
            tracking: state.tracking ? factors.tracking : 0,
            relaunch: state.art === 'relaunch' ? factors.relaunch : 0
        };
        return { components: components, days: Object.values(components).reduce(function (a, b) { return a + b; }, 0) };
    }
    var link = new URL($('[data-website-cta]').href, window.location.href);
    function timeline(result) {
        var parts = result.components, phases = [], day = 1;
        if (parts.relaunch) phases.push({ name: 'Bestandsaufnahme und Weiterleitungen', duration: parts.relaunch });
        if (parts.texts) phases.push({ name: 'Texte erstellen', duration: parts.texts });
        if (parts.design) phases.push({ name: 'Screendesign erstellen', duration: parts.design });
        phases.push({ name: 'WordPress umsetzen', duration: parts.implementation - factors.qa });
        if (parts.tracking) phases.push({ name: 'Conversion-Tracking einrichten', duration: parts.tracking });
        phases.push({ name: 'Qualitätsprüfung und Abnahmestand', duration: factors.qa, className: 'ende' });
        if (state.crm) phases.push({ name: 'CRM-Anbindung zusätzlich', duration: 0, className: 'offen' });
        $('#bahn').replaceChildren();
        phases.forEach(function (phase) {
            var box = document.createElement('div'), label = document.createElement('span'), title = document.createElement('b');
            box.className = 'phase ' + (phase.className || '');
            label.className = 'mono';
            label.textContent = phase.duration ? 'Werktag ' + day + (phase.duration > 1 ? '–' + (day + phase.duration - 1) : '') : 'Zeit nach Klärung';
            title.textContent = phase.name;
            box.append(label, title); $('#bahn').append(box); day += phase.duration;
        });
        var duration = (state.crm ? 'Mindestens ' : '') + days(result.days);
        $('#zeit-gesamt').textContent = duration + ' geplant';
        $('#zeit-umfang').textContent = 'Ihr Umfang: ' + state.seiten + (state.seiten === 1 ? ' Seite' : ' Seiten') + ' · ' + (state.art === 'relaunch' ? 'Relaunch' : 'Neubau');
        $('#bauzeit').textContent = duration;
        $('#hero-bauzeit').textContent = 'Ihre Auswahl: ' + duration.toLowerCase() + ' geplant.';
        $('#dauer-label').textContent = state.crm ? 'Projektzeit ohne CRM-Aufwand' : 'Geplante Projektzeit';
        $('#zeit-aufteilung').textContent = 'Vorbereitung ' + days(parts.texts + parts.design) + ' · Umsetzung ' + days(parts.implementation + parts.tracking + parts.relaunch);
        $('#zeit-hinweis').textContent = 'Planung bis zum geprüften Abnahmestand. Ihre Freigabezeiten und der Starttermin kommen separat dazu.' + (state.crm ? ' Zusätzlicher CRM-Aufwand wird erst nach Systemprüfung kalkuliert.' : '') + (state.design === 'vorhanden' ? ' Preis und Zeit bestätigen wir nach Prüfung Ihrer Vorlagen.' : '');
        ['implementation', 'texts', 'design', 'tracking', 'relaunch'].forEach(function (key) {
            $('#tage-' + key).textContent = days(parts[key]);
            if (key !== 'implementation') $('#zeit-' + (key === 'texts' ? 'texte' : key)).hidden = !parts[key];
        });
        $('#zeit-crm').hidden = !state.crm;
    }
    function calculate() {
        if (automaticLayouts) state.designLayouts = state.seiten;
        state.designLayouts = Math.min(state.seiten, Math.max(1, state.designLayouts));
        var layoutSelect = $('#design-layouts');
        if (layoutSelect.options.length !== state.seiten) {
            layoutSelect.replaceChildren();
            for (var n = 1; n <= state.seiten; n++) {
                var option = document.createElement('option'); option.value = n; option.textContent = n + (n === 1 ? ' Layout' : ' Layouts'); layoutSelect.append(option);
            }
        }
        layoutSelect.value = state.designLayouts;
        var result = quote(), newDesign = state.design === 'neu';
        var extra = state.seiten - 1, total = base + extra * pagePrice + (state.tracking ? trackingPrice : 0);
        var custom = [];
        if (newDesign) custom.push('Screendesign');
        if (state.crm) custom.push('CRM-Anbindung');
        var scope = state.seiten + (state.seiten === 1 ? ' Seite' : ' Seiten') + ' · ' + (state.art === 'relaunch' ? 'Relaunch' : 'Neubau');
        $('#seiten').textContent = state.seiten;
        $('#weitere').textContent = extra;
        $('#minus').disabled = state.seiten <= 1; $('#plus').disabled = state.seiten >= max;
        $('#betrag-weitere').textContent = eur(extra * pagePrice);
        $('#gesamt').textContent = eur(total);
        $('#rechnung').textContent = eur(base) + (extra ? ' + ' + extra + ' × ' + eur(pagePrice) : '') + (state.tracking ? ' + ' + eur(trackingPrice) + ' Tracking' : '');
        $('#auswahl-art').textContent = scope;
        $('#auswahl-texte').textContent = state.texte ? 'Texte erstellen lassen' : 'Freigegebene Texte einpflegen';
        $('#text-hinweis').textContent = state.texte ? 'Texterstellung: ' + days(result.components.texts) + ' Vorbereitung geplant. Der Seitenpreis bleibt gleich.' : 'Ihre Texte sind vollständig und freigegeben. Keine zusätzliche Texterstellung; der Seitenpreis bleibt gleich.';
        $('#auswahl-design').textContent = newDesign ? 'Neues Screendesign · ' + state.designLayouts + (state.designLayouts === 1 ? ' Layout' : ' Layouts') : state.design === 'vorhanden' ? 'Freigegebenes Design umsetzen' : 'Responsive Basisgestaltung';
        $('#design-details').hidden = !newDesign;
        $('#design-hinweis').textContent = newDesign ? 'Screendesign: ' + days(result.components.design) + ' geplant. Erstes Layout ' + days(factors.design_first) + ', jedes weitere unterschiedliche Layout +' + days(factors.design_extra_layout) + '. Preis nach Angebot.' : state.design === 'vorhanden' ? 'Keine neue Designphase. Vorab prüfen wir Vollständigkeit, mobile Ansichten und Sonderfunktionen; danach bestätigen wir Preis und Zeit.' : 'Die Basisgestaltung braucht keine separate Designphase.';
        $('#summary-tracking').hidden = !state.tracking; $('#summary-screendesign').hidden = !newDesign; $('#summary-crm').hidden = !state.crm;
        $('#summary-screendesign dt').textContent = 'Screendesign · ' + state.designLayouts + (state.designLayouts === 1 ? ' Layout' : ' Layouts');
        $('#preis-label').textContent = custom.length ? 'Festpreis ohne individuelle Extras' : state.design === 'vorhanden' ? 'Basispreis vor Vorlagenprüfung' : 'Ihr Festpreis';
        var quoteNote = custom.length ? 'Zuzüglich ' + custom.join(' und ') + '. Diese Extras werden separat angeboten; der vollständige Gesamtpreis steht im Angebot.' : '';
        if (state.design === 'vorhanden') quoteNote += ' Die Umsetzung Ihrer Vorlagen wird vor Beauftragung geprüft und kalkuliert. Individuelle Abweichungen können den Basispreis ändern.';
        $('#angebot-hinweis').hidden = !quoteNote;
        $('#angebot-hinweis').textContent = quoteNote.trim();
        $('#pos-weiterleitung').classList.toggle('aus', state.art !== 'relaunch');
        $$('.groessen button').forEach(function (button) { button.setAttribute('aria-pressed', String(Number(button.dataset.seiten) === state.seiten)); });
        $$('.art button').forEach(function (button) { button.setAttribute('aria-pressed', String(button.dataset.art === state.art)); });
        var selected = [state.texte ? 'Texte erstellen' : 'Texte fertig'];
        if (state.design === 'vorhanden') selected.push('Design fertig');
        if (state.tracking) selected.push('Tracking');
        selected = selected.concat(custom);
        $('#leiste-text').textContent = scope + ' · ' + selected.join(' · ');
        $('#leiste-preis').textContent = eur(total) + ' netto' + (custom.length ? ' + Extras' : state.design === 'vorhanden' ? ' vor Prüfung' : '') + ' · ' + (state.crm ? 'ab ' : '') + result.days + ' Werktage' + (state.crm ? ' + CRM' : '');
        $('#konfiguration-status').textContent = scope + ' · ' + selected.join(' · ') + ' · ' + eur(total) + ' netto' + (custom.length ? ', zuzüglich individueller Extras nach Angebot.' : state.design === 'vorhanden' ? ', Preis nach Vorlagenprüfung.' : '.') + ' ' + (state.crm ? 'Mindestens ' : '') + days(result.days) + ' geplant.';
        timeline(result);
        link.searchParams.set('seiten', state.seiten); link.searchParams.set('art', state.art);
        link.searchParams.set('texte', state.texte ? '1' : '0');
        ['tracking', 'crm'].forEach(function (key) {
            if (state[key]) link.searchParams.set(key, '1'); else link.searchParams.delete(key);
        });
        if (state.design !== 'basis') link.searchParams.set('design', state.design); else link.searchParams.delete('design');
        if (newDesign) { link.searchParams.set('screendesign', '1'); link.searchParams.set('design_layouts', state.designLayouts); }
        else { link.searchParams.delete('screendesign'); link.searchParams.delete('design_layouts'); }
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
    ['texte', 'tracking', 'crm'].forEach(function (key) {
        $('#' + key).addEventListener('change', function () { state[key] = this.checked; calculate(); });
    });
    $$('[name="website-design"]').forEach(function (input) {
        input.addEventListener('change', function () { if (this.checked) { state.design = this.value; calculate(); } });
    });
    $('#design-layouts').addEventListener('change', function () { state.designLayouts = Number(this.value); automaticLayouts = false; calculate(); });
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
