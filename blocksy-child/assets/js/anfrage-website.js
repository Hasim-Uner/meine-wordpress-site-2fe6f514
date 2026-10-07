/* Die Anfrage-Website. Progressive enhancement; no cookies or stored visitor state. */
(function () {
    'use strict';
    var root = document.querySelector('[data-website-product]');
    if (!root) return;
    var $ = function (selector) { return root.querySelector(selector); };
    var $$ = function (selector) { return Array.prototype.slice.call(root.querySelectorAll(selector)); };
    var rules;
    try { rules = JSON.parse(root.dataset.websiteRules); } catch (error) { return; }
    if (!rules || !rules.prices || !rules.days || !Number.isFinite(rules.max_pages) || !Object.values(rules.prices).concat(Object.values(rules.days)).every(Number.isFinite)) return;
    var base = rules.prices.base, trackingPrice = rules.prices.tracking, max = rules.max_pages;
    var factors = rules.days;
    var state = { utility: 0, standard: 1, sales: 1, art: 'neubau', texte: false, tracking: false, design: 'basis', designLayouts: 1, crm: false, dashboard: false };
    var media = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : { matches: true };
    var eur = function (number) { return number.toLocaleString('de-DE') + '\u00a0€'; };
    var days = function (number) { return number.toLocaleString('de-DE') + (number === 1 ? ' Werktag' : ' Werktage'); };
    var totalPages = function () { return 1 + state.utility + state.standard + state.sales; };
    function scopeLabel() {
        var pages = totalPages();
        var parts = [pages + (pages === 1 ? ' Seite' : ' Seiten'), state.art === 'relaunch' ? 'Relaunch' : 'Neubau'];
        if (state.utility) parts.push(state.utility + (state.utility === 1 ? ' kurze Seite' : ' kurze Seiten'));
        if (state.standard) parts.push(state.standard + (state.standard === 1 ? ' Standard' : ' Standard'));
        if (state.sales) parts.push(state.sales + (state.sales === 1 ? ' Leistung' : ' Leistungen'));
        return parts.join(' · ');
    }
    function prospectiveTextPrice() {
        return rules.prices.copy_base
            + state.utility * rules.prices.copy_utility
            + state.standard * rules.prices.copy_standard
            + state.sales * rules.prices.copy_sales;
    }
    function quote() {
        var components = {
            implementation: factors.base_implementation
                + state.utility * factors.page_utility
                + state.standard * factors.page_standard
                + state.sales * factors.page_sales,
            texts: state.texte ? factors.copy_base
                + state.utility * factors.copy_utility
                + state.standard * factors.copy_standard
                + state.sales * factors.copy_sales : 0,
            design: state.design === 'neu' ? factors.design_first + (state.designLayouts - 1) * factors.design_extra_layout : 0,
            tracking: state.tracking ? factors.tracking : 0,
            relaunch: state.art === 'relaunch' ? factors.relaunch : 0,
            crm: state.crm ? factors.crm : 0
        };
        var pagePrice = state.utility * rules.prices.page_utility
            + state.standard * rules.prices.page_standard
            + state.sales * rules.prices.page_sales;
        var textPrice = state.texte ? prospectiveTextPrice() : 0;
        var designPrice = state.design === 'neu' ? rules.prices.design_first + (state.designLayouts - 1) * rules.prices.design_extra : 0;
        return {
            components: components,
            days: Math.ceil(Object.values(components).reduce(function (a, b) { return a + b; }, 0)),
            pagePrice: pagePrice,
            textPrice: textPrice,
            designPrice: designPrice,
            price: base + pagePrice + textPrice + (state.tracking ? trackingPrice : 0) + designPrice + (state.crm ? rules.prices.crm : 0)
        };
    }
    var link = new URL($('[data-website-cta]').href, window.location.href);
    function timeline(result) {
        var parts = result.components;
        var duration = (state.dashboard ? 'Mindestens ' : '') + days(result.days);
        var phases = [
            { name: 'Briefing & Material', label: 'Vor dem Start' },
            { name: 'Umsetzung & Prüfung', label: duration },
            { name: 'Freigabe & Livegang', label: 'danach', className: 'ende' }
        ];
        $('#bahn').replaceChildren();
        phases.forEach(function (phase) {
            var box = document.createElement('div'), label = document.createElement('span'), title = document.createElement('b');
            box.className = 'phase ' + (phase.className || ''); label.className = 'mono';
            label.textContent = phase.label; title.textContent = phase.name;
            box.append(label, title); $('#bahn').append(box);
        });
        $('#zeit-gesamt').textContent = duration + ' geplant';
        $('#zeit-umfang').textContent = 'Ihr Umfang: ' + scopeLabel();
        $('#bauzeit').textContent = duration;
        $('#hero-bauzeit').textContent = 'Ihre Auswahl: ' + (state.dashboard ? 'mindestens ' : '') + days(result.days) + ' geplant.';
        $('#dauer-label').textContent = state.dashboard ? 'Produktionszeit ohne Dashboard' : 'Produktionszeit';
        $('#zeit-aufteilung').textContent = 'Vorbereitung ' + days(parts.texts + parts.design) + ' · Umsetzung ' + days(parts.implementation + parts.tracking + parts.relaunch + parts.crm);
        $('#zeit-hinweis').textContent = 'Planung bis zum geprüften Abnahmestand. Ihre Freigabezeiten und der Starttermin kommen separat dazu.' + (state.dashboard ? ' Dashboard-Aufwand ist noch nicht enthalten.' : '') + (state.crm ? ' CRM-Standardumfang nach Systemprüfung bestätigen.' : '') + (state.design === 'vorhanden' ? ' Vorlagen vor Beauftragung prüfen.' : '');
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
        var pages = totalPages();
        state.designLayouts = Math.min(pages, Math.max(1, state.designLayouts));
        var layoutSelect = $('#design-layouts');
        if (layoutSelect.options.length !== pages) {
            layoutSelect.replaceChildren();
            for (var n = 1; n <= pages; n++) {
                var option = document.createElement('option'); option.value = n; option.textContent = n + (n === 1 ? ' Layout' : ' Layouts'); layoutSelect.append(option);
            }
        }
        layoutSelect.value = state.designLayouts;
        var result = quote(), newDesign = state.design === 'neu', total = result.price, scope = scopeLabel();

        ['utility', 'standard', 'sales'].forEach(function (key) {
            $('#' + key + '-pages').textContent = state[key];
            $('#' + key + '-minus').disabled = state[key] <= 0;
            $('#' + key + '-plus').disabled = pages >= max;
            $('#summary-' + key).hidden = state[key] === 0;
            $('#count-' + key).textContent = state[key];
            $('#betrag-' + key).textContent = eur(state[key] * rules.prices['page_' + key]);
        });

        $('#gesamt').textContent = eur(total);
        var equation = [eur(base)];
        if (state.utility) equation.push(state.utility + ' × ' + eur(rules.prices.page_utility) + ' kurz');
        if (state.standard) equation.push(state.standard + ' × ' + eur(rules.prices.page_standard) + ' Standard');
        if (state.sales) equation.push(state.sales + ' × ' + eur(rules.prices.page_sales) + ' Leistung');
        if (state.texte) equation.push(eur(result.textPrice) + ' Texte');
        if (newDesign) equation.push(eur(result.designPrice) + ' Design');
        if (state.tracking) equation.push(eur(trackingPrice) + ' Tracking');
        if (state.crm) equation.push(eur(rules.prices.crm) + ' CRM');
        $('#rechnung').textContent = equation.join(' + ');
        $('#auswahl-art').textContent = scope;
        $('#abschluss-umfang').textContent = scope;
        $('#abschluss-preis').textContent = eur(total);
        $('#abschluss-preiszusatz').textContent = 'netto · zzgl. USt.' + (state.dashboard ? ' · Zuzüglich individuelles Daten-Dashboard nach Angebot' : '');

        $('#text-option-preis').textContent = '+' + eur(prospectiveTextPrice());
        $('#auswahl-texte').textContent = state.texte ? 'Texte erstellen lassen' : 'Eigene Texte';
        $('#betrag-texte').textContent = state.texte ? eur(result.textPrice) : eur(0);
        $('#text-hinweis').textContent = state.texte
            ? eur(result.textPrice) + ' für die gewählten Seitentypen · +' + days(result.components.texts) + ' Vorbereitung.'
            : 'Fertige, freigegebene Texte werden eingepflegt. Texterstellung für diese Auswahl: +' + eur(prospectiveTextPrice()) + '.';

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
        if (state.dashboard) notes.push('Zuzüglich individuelles Daten-Dashboard nach Angebot: Preis und Zeit separat.');
        if (state.crm) notes.push('CRM: Standardumfang vorab prüfen.');
        if (state.design === 'vorhanden') notes.push('Vorlage: Umsetzungsumfang vorab prüfen.');
        $('#angebot-hinweis').hidden = !notes.length; $('#angebot-hinweis').textContent = notes.join(' ');
        $('#pos-weiterleitung').classList.toggle('aus', state.art !== 'relaunch');

        $$('[data-website-scenario]').forEach(function (button) {
            var match = Number(button.dataset.utility) === state.utility
                && Number(button.dataset.standard) === state.standard
                && Number(button.dataset.sales) === state.sales;
            button.setAttribute('aria-pressed', String(match));
        });
        $$('.art button').forEach(function (button) { button.setAttribute('aria-pressed', String(button.dataset.art === state.art)); });

        var selected = [];
        if (state.texte) selected.push('Texte');
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

        link.searchParams.set('seiten', pages);
        link.searchParams.set('art', state.art);
        link.searchParams.set('kurz', state.utility);
        link.searchParams.set('standard', state.standard);
        link.searchParams.set('leistung', state.sales);
        link.searchParams.set('texte', state.texte ? '1' : '0');
        ['tracking', 'crm', 'dashboard'].forEach(function (key) { if (state[key]) link.searchParams.set(key, '1'); else link.searchParams.delete(key); });
        if (state.design !== 'basis') link.searchParams.set('design', state.design); else link.searchParams.delete('design');
        if (newDesign) { link.searchParams.set('screendesign', '1'); link.searchParams.set('design_layouts', state.designLayouts); }
        else { link.searchParams.delete('screendesign'); link.searchParams.delete('design_layouts'); }
        $$('[data-website-cta]').forEach(function (cta) { cta.href = link.href; });
        if (previousPrice !== $('#gesamt').textContent) confirmChange($('#gesamt'));
        if (newDesign && detailsWereHidden) confirmChange($('#design-details'));
    }
    $$('[data-page-delta]').forEach(function (button) {
        button.addEventListener('click', function () {
            var key = button.dataset.pageType, delta = Number(button.dataset.pageDelta);
            if (!Object.prototype.hasOwnProperty.call(state, key) || !Number.isFinite(delta)) return;
            if (delta > 0 && totalPages() >= max) return;
            state[key] = Math.max(0, state[key] + delta);
            calculate();
        });
    });
    $$('[data-website-scenario]').forEach(function (button) {
        button.addEventListener('click', function () {
            state.utility = Number(button.dataset.utility) || 0;
            state.standard = Number(button.dataset.standard) || 0;
            state.sales = Number(button.dataset.sales) || 0;
            state.designLayouts = Math.min(state.designLayouts, totalPages());
            calculate();
        });
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
    function cancelMotion() {
        if (!media.matches) return;
        selectionAnimations.forEach(function (motion) { motion.cancel(); });
        selectionAnimations.clear();
    }
    $('.aw-extras-panel').addEventListener('toggle', function () {
        if (this.open) confirmChange(this.querySelector('.aw-extras'));
        scheduleSticky();
    });
    $$('[data-exclusive-details]').forEach(function (group) {
        var items = Array.from(group.children).filter(function (item) { return item.tagName === 'DETAILS'; });
        items.forEach(function (item) {
            item.addEventListener('toggle', function () {
                if (!item.open) return;
                items.forEach(function (other) {
                    if (other !== item && other.open) other.open = false;
                });
            });
        });
    });
    var qualitySection = $('#unterschied');
    if (qualitySection && !media.matches && window.IntersectionObserver) {
        root.classList.add('aw-motion-ready');
        var qualityObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.22 });
        qualityObserver.observe(qualitySection);
    } else if (qualitySection) {
        qualitySection.classList.add('is-visible');
    }
    if (media.addEventListener) media.addEventListener('change', cancelMotion);
    calculate();
    root.classList.add('aw-ready');
    $$('[data-website-controls]').forEach(function (control) { control.hidden = false; });
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
        var focused = bar.contains(document.activeElement);
        if (focused) visible = true;
        bar.hidden = !visible;
        // At large text sizes the normal summary remains available; avoid covering the page.
        if (visible && !focused && bar.getBoundingClientRect().height > window.innerHeight / 3) visible = false;
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
