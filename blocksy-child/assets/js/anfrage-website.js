/* Die Anfrage-Website. Progressive enhancement; no cookies or stored visitor state. */
(function () {
    'use strict';
    var root = document.querySelector('[data-website-product]');
    if (!root) return;
    var $ = function (selector) { return root.querySelector(selector); };
    var $$ = function (selector) { return Array.prototype.slice.call(root.querySelectorAll(selector)); };
    var rules;
    try { rules = JSON.parse(root.dataset.websiteRules); } catch (error) { return; }
    if (!rules || !rules.prices || !rules.days || !Array.isArray(rules.implementation_tiers) || !rules.implementation_tiers.length || !rules.implementation_tiers.every(function (tier) { return Number.isFinite(tier.max_pages) && Number.isFinite(tier.days); }) || !Object.values(rules.prices).concat(Object.values(rules.days), rules.max_pages).every(Number.isFinite)) return;
    var base = rules.prices.base, pagePrice = rules.prices.page, trackingPrice = rules.prices.tracking, max = rules.max_pages;
    var factors = rules.days;
    var state = { seiten: 3, art: 'neubau', texte: true, tracking: false, design: 'basis', designLayouts: 1, crm: false, dashboard: false };
    var media = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : { matches: true };
    var eur = function (number) { return number.toLocaleString('de-DE') + '\u00a0€'; };
    var days = function (number) { return number.toLocaleString('de-DE') + (number === 1 ? ' Werktag' : ' Werktage'); };
    function quote() {
        var tier = rules.implementation_tiers.find(function (entry) { return state.seiten <= entry.max_pages; });
        var components = {
            implementation: tier.days,
            texts: state.texte ? factors.text_first + (state.seiten - 1) * factors.text_extra : 0,
            design: state.design === 'neu' ? factors.design_first + (state.designLayouts - 1) * factors.design_extra_layout : 0,
            tracking: state.tracking ? factors.tracking : 0,
            relaunch: state.art === 'relaunch' ? factors.relaunch : 0,
            crm: state.crm ? factors.crm : 0
        };
        var designPrice = state.design === 'neu' ? rules.prices.design_first + (state.designLayouts - 1) * rules.prices.design_extra : 0;
        return { components: components, days: Math.ceil(Object.values(components).reduce(function (a, b) { return a + b; }, 0)), designPrice: designPrice,
            price: base + (state.seiten - 1) * pagePrice + (state.tracking ? trackingPrice : 0) + designPrice + (state.crm ? rules.prices.crm : 0) };
    }
    var link = new URL($('[data-website-cta]').href, window.location.href);
    function timeline(result) {
        var parts = result.components, phases = [];
        if (parts.relaunch) phases.push({ name: 'Bestand und Weiterleitungen', duration: parts.relaunch });
        if (parts.texts) phases.push({ name: 'Texte erstellen', duration: parts.texts });
        if (parts.design) phases.push({ name: 'Screendesign erstellen', duration: parts.design });
        phases.push({ name: 'WordPress umsetzen', duration: parts.implementation - factors.qa });
        if (parts.tracking) phases.push({ name: 'Conversion-Tracking einrichten', duration: parts.tracking });
        if (parts.crm) phases.push({ name: 'Standard-CRM anbinden', duration: parts.crm });
        phases.push({ name: 'Qualitätsprüfung und Abnahmestand', duration: factors.qa, className: 'ende' });
        if (state.dashboard) phases.push({ name: 'Daten-Dashboard zusätzlich', duration: 0, className: 'offen' });
        $('#bahn').replaceChildren();
        phases.forEach(function (phase) {
            var box = document.createElement('div'), label = document.createElement('span'), title = document.createElement('b');
            box.className = 'phase ' + (phase.className || ''); label.className = 'mono';
            label.textContent = phase.duration ? days(phase.duration) : 'Zeit nach Angebot';
            title.textContent = phase.name; box.append(label, title); $('#bahn').append(box);
        });
        var duration = (state.dashboard ? 'Mindestens ' : '') + days(result.days);
        $('#zeit-gesamt').textContent = duration + ' geplant';
        $('#zeit-umfang').textContent = 'Ihr Umfang: ' + state.seiten + (state.seiten === 1 ? ' Seite' : ' Seiten') + ' · ' + (state.art === 'relaunch' ? 'Relaunch' : 'Neubau');
        $('#bauzeit').textContent = duration;
        $('#hero-bauzeit').textContent = 'Ihre Auswahl: ' + (state.dashboard ? 'mindestens ' : '') + days(result.days) + ' geplant.';
        $('#dauer-label').textContent = state.dashboard ? 'Produktionszeit ohne Dashboard' : 'Produktionszeit';
        $('#zeit-aufteilung').textContent = 'Vorbereitung ' + days(parts.texts + parts.design) + ' · Umsetzung ' + days(parts.implementation + parts.tracking + parts.relaunch + parts.crm);
        $('#zeit-hinweis').textContent = 'Planung bis zum geprüften Abnahmestand. Ihre Freigabezeiten und der Starttermin kommen separat dazu. Halbe Tage werden erst in der Gesamtsumme aufgerundet.' + (state.dashboard ? ' Dashboard-Aufwand ist noch nicht enthalten.' : '') + (state.crm ? ' CRM-Standardumfang nach Systemprüfung bestätigen.' : '') + (state.design === 'vorhanden' ? ' Vorlagen vor Beauftragung prüfen.' : '');
        ['implementation', 'texts', 'design', 'tracking', 'relaunch', 'crm'].forEach(function (key) {
            $('#tage-' + key).textContent = days(parts[key]);
            if (key !== 'implementation') $('#zeit-' + (key === 'texts' ? 'texte' : key)).hidden = !parts[key];
        });
        $('#zeit-dashboard').hidden = !state.dashboard;
    }
    var selectionAnimations = new Map(), announcement;
    function confirmChange(element) {
        if (selectionAnimations.has(element)) selectionAnimations.get(element).cancel();
        if (media.matches || !element.animate) return;
        var style = getComputedStyle(root);
        var motion = element.animate([{ opacity: .7, transform: 'translateY(4px)' }, { opacity: 1, transform: 'translateY(0)' }], {
            duration: parseFloat(style.getPropertyValue('--t-norm')) || 200,
            easing: style.getPropertyValue('--ease-aus').trim() || 'ease-out'
        });
        selectionAnimations.set(element, motion);
        motion.finished.then(function () { if (selectionAnimations.get(element) === motion) selectionAnimations.delete(element); }, function () {});
    }
    function calculate() {
        var previousPrice = $('#gesamt').textContent, detailsWereHidden = $('#design-details').hidden;
        state.designLayouts = Math.min(state.seiten, Math.max(1, state.designLayouts));
        var layoutSelect = $('#design-layouts');
        if (layoutSelect.options.length !== state.seiten) {
            layoutSelect.replaceChildren();
            for (var n = 1; n <= state.seiten; n++) {
                var option = document.createElement('option'); option.value = n; option.textContent = n + (n === 1 ? ' Layout' : ' Layouts'); layoutSelect.append(option);
            }
        }
        layoutSelect.value = state.designLayouts;
        var result = quote(), newDesign = state.design === 'neu', extra = state.seiten - 1, total = result.price;
        var scope = state.seiten + (state.seiten === 1 ? ' Seite' : ' Seiten') + ' · ' + (state.art === 'relaunch' ? 'Relaunch' : 'Neubau');
        $('#seiten').textContent = state.seiten; $('#weitere').textContent = extra;
        $('#weitere-label').textContent = extra === 1 ? 'weitere Seite' : 'weitere Seiten';
        $('#summary-pages').hidden = !extra;
        $('#minus').disabled = state.seiten <= 1; $('#plus').disabled = state.seiten >= max;
        $('#betrag-weitere').textContent = eur(extra * pagePrice); $('#gesamt').textContent = eur(total);
        $('#rechnung').textContent = eur(base) + (extra ? ' + ' + extra + ' × ' + eur(pagePrice) : '') + (newDesign ? ' + ' + eur(result.designPrice) + ' Design' : '') + (state.tracking ? ' + ' + eur(trackingPrice) + ' Tracking' : '') + (state.crm ? ' + ' + eur(rules.prices.crm) + ' CRM' : '');
        $('#auswahl-art').textContent = scope;
        $('#auswahl-texte').textContent = state.texte ? 'Texte erstellen lassen' : 'Eigene Texte einpflegen';
        $('#text-hinweis').textContent = state.texte ? 'Texte bereits fertig? Abwählen. Sonst +' + days(result.components.texts) + ' Vorbereitung, ohne Aufpreis.' : 'Ihre Texte sind vollständig und freigegeben. Keine zusätzliche Texterstellung.';
        $('#auswahl-design').textContent = newDesign ? 'Screendesign · ' + state.designLayouts + (state.designLayouts === 1 ? ' Layout' : ' Layouts') : state.design === 'vorhanden' ? 'Vorhandenes Design umsetzen' : 'Basisdesign inklusive';
        $('#design-details').hidden = !newDesign;
        $('#design-option-preis').textContent = '+' + eur(rules.prices.design_first + (state.designLayouts - 1) * rules.prices.design_extra);
        $('#design-option-layouts').textContent = state.designLayouts + (state.designLayouts === 1 ? ' Layout' : ' Layouts');
        var extras = ['tracking', 'crm', 'dashboard'].filter(function (key) { return state[key]; }).length;
        $('#extras-count').textContent = extras ? extras + ' gewählt' : 'Keine gewählt';
        $('#design-hinweis').textContent = newDesign ? eur(result.designPrice) + ' · +' + days(result.components.design) + ' · Desktop und Mobil inklusive.' : state.design === 'vorhanden' ? '*Kein Design-Aufpreis. Umfang, Mobilansichten und Sonderfunktionen vorab prüfen.' : 'Basisdesign inklusive.';
        ['tracking', 'crm', 'dashboard'].forEach(function (key) { $('#summary-' + key).hidden = !state[key]; });
        $('#summary-screendesign').hidden = !newDesign; $('#betrag-design').textContent = eur(result.designPrice);
        $('#summary-screendesign dt').textContent = 'Screendesign · ' + state.designLayouts + (state.designLayouts === 1 ? ' Layout' : ' Layouts');
        $('#preis-label').textContent = state.dashboard ? 'Einmalpreis ohne Dashboard' : 'Ihr Einmalpreis';
        var notes = [];
        if (state.dashboard) notes.push('Zuzüglich Daten-Dashboard nach Angebot: Preis und Zeit separat.');
        if (state.crm) notes.push('CRM: Standardumfang vorab prüfen.');
        if (state.design === 'vorhanden') notes.push('Vorlage: Umsetzungsumfang vorab prüfen.');
        $('#angebot-hinweis').hidden = !notes.length; $('#angebot-hinweis').textContent = notes.join(' ');
        $('#pos-weiterleitung').classList.toggle('aus', state.art !== 'relaunch');
        $$('.groessen button').forEach(function (button) { button.setAttribute('aria-pressed', String(Number(button.dataset.seiten) === state.seiten)); });
        $$('[data-website-scenario]').forEach(function (button) { button.setAttribute('aria-pressed', String(Number(button.dataset.websiteScenario) === state.seiten)); });
        $$('.art button').forEach(function (button) { button.setAttribute('aria-pressed', String(button.dataset.art === state.art)); });
        var selected = [];
        if (newDesign) selected.push('Screendesign');
        if (state.tracking) selected.push('Tracking');
        if (state.crm) selected.push('CRM');
        if (state.dashboard) selected.push('Dashboard nach Angebot');
        $('#leiste-text').textContent = scope + (selected.length ? ' · ' + selected.join(' · ') : '');
        $('#leiste-preis').textContent = eur(total) + ' netto' + (state.dashboard ? ' + Dashboard' : '') + ' · ' + (state.dashboard ? 'ab ' : '') + days(result.days);
        clearTimeout(announcement);
        announcement = setTimeout(function () {
            $('#konfiguration-status').textContent = scope + ' · ' + eur(total) + ' netto. ' + (state.dashboard ? 'Daten-Dashboard zusätzlich nach Angebot. Mindestens ' : '') + days(result.days) + ' geplant.';
        }, 180);
        timeline(result);
        link.searchParams.set('seiten', state.seiten); link.searchParams.set('art', state.art); link.searchParams.set('texte', state.texte ? '1' : '0');
        ['tracking', 'crm', 'dashboard'].forEach(function (key) { if (state[key]) link.searchParams.set(key, '1'); else link.searchParams.delete(key); });
        if (state.design !== 'basis') link.searchParams.set('design', state.design); else link.searchParams.delete('design');
        if (newDesign) { link.searchParams.set('screendesign', '1'); link.searchParams.set('design_layouts', state.designLayouts); }
        else { link.searchParams.delete('screendesign'); link.searchParams.delete('design_layouts'); }
        $$('[data-website-cta]').forEach(function (cta) { cta.href = link.href; });
        if (previousPrice !== $('#gesamt').textContent) confirmChange($('#gesamt'));
        if (newDesign && detailsWereHidden) confirmChange($('#design-details'));
    }
    $('#minus').addEventListener('click', function () { if (state.seiten > 1) { state.seiten--; calculate(); } });
    $('#plus').addEventListener('click', function () { if (state.seiten < max) { state.seiten++; calculate(); } });
    $$('.groessen button').forEach(function (button) {
        button.addEventListener('click', function () { if (state.seiten !== Number(button.dataset.seiten)) { state.seiten = Number(button.dataset.seiten); calculate(); } });
    });
    $$('.art button').forEach(function (button) {
        button.addEventListener('click', function () { if (state.art !== button.dataset.art) { state.art = button.dataset.art; calculate(); } });
    });
    ['texte', 'tracking', 'crm', 'dashboard'].forEach(function (key) {
        $('#' + key).addEventListener('change', function () { state[key] = this.checked; calculate(); });
    });
    $$('[name="website-design"]').forEach(function (input) {
        input.addEventListener('change', function () { if (this.checked) { state.design = this.value; calculate(); } });
    });
    $('#design-layouts').addEventListener('change', function () { state.designLayouts = Number(this.value); calculate(); });
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
    function cancelMotion() {
        if (!media.matches) return;
        if (animation) animation.cancel();
        selectionAnimations.forEach(function (motion) { motion.cancel(); });
        selectionAnimations.clear();
    }
    $('.aw-extras-panel').addEventListener('toggle', function () {
        if (this.open) confirmChange(this.querySelector('.aw-extras'));
        scheduleSticky();
    });
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
    mode('anfragen');
    // Hide the CTA from both keyboard and accessibility tree at the hero and close.
    var bar = $('#leiste'), stickyCTA = $('#cta-leiste'), scheduled = false;
    function updateSticky() {
        scheduled = false;
        var summaryCTA = $('#cta-angebot').getBoundingClientRect();
        var examples = $('#beispiele').getBoundingClientRect();
        var viewportTop = parseFloat(getComputedStyle(root).getPropertyValue('--leiste-h')) || 52;
        var examplesVisible = examples.top < window.innerHeight && examples.bottom > viewportTop;
        var summaryVisible = summaryCTA.top < window.innerHeight && summaryCTA.bottom > viewportTop;
        var visible = !examplesVisible && !summaryVisible && $('#hero').getBoundingClientRect().bottom <= 0 && $('#anfrage').getBoundingClientRect().top >= window.innerHeight;
        if (bar.contains(document.activeElement)) visible = true;
        bar.hidden = !visible; bar.inert = !visible;
        bar.classList.toggle('zeigen', visible); bar.setAttribute('aria-hidden', String(!visible)); stickyCTA.tabIndex = visible ? 0 : -1;
        root.style.setProperty('--aw-sticky-space', visible ? bar.getBoundingClientRect().height + 'px' : '0px');
    }
    function scheduleSticky() { if (!scheduled) { scheduled = true; requestAnimationFrame(updateSticky); } }
    bar.addEventListener('focusout', scheduleSticky);
    window.addEventListener('scroll', scheduleSticky, { passive: true });
    window.addEventListener('resize', scheduleSticky, { passive: true });
    if (window.IntersectionObserver) {
        var observer = new IntersectionObserver(scheduleSticky);
        observer.observe($('#hero')); observer.observe($('#beispiele')); observer.observe($('#anfrage')); observer.observe($('#cta-angebot'));
    }
    if (window.ResizeObserver) new ResizeObserver(scheduleSticky).observe(bar);
    updateSticky();
})();
