/**
 * AUDIT LIVE V3 — URL submit -> n8n polling -> on-page diagnosis
 *
 * Zielbild:
 * - kein Audit-E-Mail-Branch
 * - kein PDF/HTML-Report-Versand
 * - kein Fluent-Forms-Deep-Dive
 * - Ergebnis muss sofort auf der Seite überzeugen
 * - danach nativer Beratungs-CTA mit eigenem Webhook
 */
(function () {
  'use strict';

  var injectedConfig = window.NexusAuditConfig || {};
  var CONFIG = {
    webhookStart: injectedConfig.webhookStart || 'https://hasim.app.n8n.cloud/webhook/audit',
    webhookStatus: injectedConfig.webhookStatus || 'https://hasim.app.n8n.cloud/webhook/audit-status',
    webhookConsultation: injectedConfig.webhookConsultation || 'https://hasim.app.n8n.cloud/webhook/audit-consultation',
    consultationAltUrl: injectedConfig.consultationAltUrl || '/kontakt/',
    pollInterval: 4500,
    pollTimeout: 180000,
    animDelay: 110
  };

  var state = {
    jobId: null,
    pollTimer: null,
    pollStart: null,
    auditUrl: null,
    phase: 'idle',
    normalized: null
  };

  var loaderSteps = [
    { icon: '🧾', text: 'Das Seitenversprechen wird gelesen …', sub: 'Wir prüfen H1, Title, Copy und die Klarheit des ersten Eindrucks.' },
    { icon: '🤝', text: 'Proof-Signale werden gesucht …', sub: 'Wir prüfen Cases, Testimonials und sichtbare Vertrauensanker.' },
    { icon: '🎯', text: 'Der nächste Schritt wird geprüft …', sub: 'Wir schauen auf CTA, Kontaktpfad und Reibung vor der Anfrage.' },
    { icon: '⚡', text: 'Der mobile Eindruck wird gemessen …', sub: 'PageSpeed, Ladezeit und Reibung auf Mobilgeräten.' },
    { icon: '🧭', text: 'Die drei wichtigsten Hebel werden priorisiert …', sub: 'Am Ende sehen Sie nicht alles, sondern zuerst das Wirksamste.' }
  ];

  function init() {
    var form = document.getElementById('audit-live-form');
    if (!form) return;

    form.addEventListener('submit', handleSubmit);

    var urlInput = form.querySelector('[name="url"]');
    if (urlInput) {
      urlInput.addEventListener('blur', function () {
        var value = this.value.trim();
        if (value && !/^https?:\/\//i.test(value)) {
          this.value = 'https://' + value;
        }
      });
    }
  }

  function handleSubmit(event) {
    event.preventDefault();

    if (state.phase === 'submitting' || state.phase === 'polling') {
      return;
    }

    var form = event.target;
    var url = normalizeInputUrl(form.querySelector('[name="url"]').value);

    if (!url) {
      showFormError('Bitte eine gültige URL eingeben, z. B. https://example.de.');
      return;
    }

    clearPollTimer();
    clearFormError();
    clearResults();
    state.auditUrl = url;

    setPhase('submitting');
    showLoader();
    updateLoaderStep(loaderSteps[0]);

    fetch(CONFIG.webhookStart, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ url: url })
    })
      .then(function (res) { return parseJsonResponse(res, 'Check-Start'); })
      .then(function (data) {
        if (!data || !data.ok || !data.jobId) {
          throw new Error((data && data.error) || 'Der Check konnte nicht gestartet werden.');
        }

        state.jobId = data.jobId;
        state.pollStart = Date.now();
        setPhase('polling');
        startPolling();
      })
      .catch(function (error) {
        setPhase('error');
        showLoaderError(error.message || 'Verbindungsfehler. Bitte erneut versuchen.');
      });
  }

  function startPolling() {
    var stepIndex = 0;
    var consecutiveFailures = 0;

    clearPollTimer();

    state.pollTimer = window.setInterval(function () {
      if (!state.jobId) {
        clearPollTimer();
        return;
      }

      if (Date.now() - state.pollStart > CONFIG.pollTimeout) {
        clearPollTimer();
        setPhase('error');
        showLoaderError('Die Analyse braucht heute länger als erwartet. Bitte in ein bis zwei Minuten erneut versuchen.');
        return;
      }

      stepIndex = (stepIndex + 1) % loaderSteps.length;
      updateLoaderStep(loaderSteps[stepIndex]);

      fetch(CONFIG.webhookStatus + '?jobId=' + encodeURIComponent(state.jobId))
        .then(function (res) { return parseJsonResponse(res, 'Check-Status'); })
        .then(function (data) {
          consecutiveFailures = 0;

          if (data.status === 'done' && data.data) {
            clearPollTimer();
            setPhase('rendering');
            renderResults(data.data);
            return;
          }

          if (data.status === 'error' || data.status === 'expired') {
            clearPollTimer();
            setPhase('error');
            showLoaderError(data.error || 'Fehler bei der Analyse.');
          }
        })
        .catch(function (error) {
          consecutiveFailures += 1;

          if (error && /keine JSON-Antwort|ungueltiges JSON/i.test(error.message || '')) {
            clearPollTimer();
            setPhase('error');
            showLoaderError(error.message + ' Bitte n8n-Webhook prüfen.');
            return;
          }

          if (consecutiveFailures >= 3) {
            var sub = document.getElementById('loader-sub');
            if (sub) {
              sub.textContent = 'Verbindung instabil. Wir versuchen es weiter …';
            }
          }
        });
    }, CONFIG.pollInterval);
  }

  function renderResults(rawData) {
    var loader = document.getElementById('audit-loader');
    var progress = document.getElementById('loader-progress');
    var results = document.getElementById('audit-results');
    var wrapper = document.getElementById('audit-main-wrapper');

    if (loader) loader.style.display = 'none';
    if (progress) progress.style.width = '100%';
    if (!results) return;

    state.normalized = normalizeAuditData(rawData);

    results.style.display = 'block';
    if (wrapper) wrapper.classList.add('view-mode-results');

    results.innerHTML =
      renderResultHeader(state.normalized) +
      renderHighlights(state.normalized) +
      renderFindings(state.normalized) +
      renderOpportunity(state.normalized) +
      renderDetailSections(state.normalized) +
      renderStory(state.normalized) +
      renderConsultation(state.normalized);

    window.setTimeout(function () {
      results.classList.add('is-visible');
      staggerReveal(results.querySelectorAll('.result-animate'));
    }, 100);

    bindConsultationForm();

    results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    setPhase('done');
  }

  function renderResultHeader(data) {
    var meta = data.meta;
    var verdict = data.verdict;
    var toneClass = mapTone(verdict.status);

    return (
      '<section class="result-header result-animate">' +
        '<div class="result-pill result-pill-' + toneClass + '">' + escapeHtml(verdict.badge) + '</div>' +
        '<h2 class="result-title">' + escapeHtml(verdict.headline) + '</h2>' +
        '<p class="result-verdict-sub">' + escapeHtml(verdict.subline) + '</p>' +
        '<div class="result-meta">' +
          '<span class="result-domain">' + escapeHtml(meta.domain || '') + '</span>' +
          (meta.branche ? ' · ' + escapeHtml(meta.branche) : '') +
          (meta.standort ? ' · ' + escapeHtml(meta.standort) : '') +
        '</div>' +
      '</section>'
    );
  }

  function renderHighlights(data) {
    var highlights = data.highlights || [];
    if (!highlights.length) return '';

    var html = '<section class="score-grid result-animate" aria-label="Kurzdiagnose">';

    for (var i = 0; i < highlights.length; i++) {
      var item = highlights[i];
      var tone = mapTone(item.tone || 'warning');

      html += (
        '<article class="score-card score-' + tone + '">' +
          '<div class="score-card-label">' + escapeHtml(item.label) + '</div>' +
          '<div class="score-card-value">' + escapeHtml(item.value) + '</div>' +
          '<div class="score-card-help">' + escapeHtml(item.help || '') + '</div>' +
        '</article>'
      );
    }

    html += '</section>';
    return html;
  }

  function renderFindings(data) {
    var findings = data.findings || [];
    if (!findings.length) return '';

    var html = '<section class="result-findings result-animate">';
    html += '<h3 class="result-section-title">Die wichtigsten Hebel</h3>';
    html += '<div class="finding-grid">';

    for (var i = 0; i < findings.length; i++) {
      var item = findings[i];
      var tone = mapTone(item.status);

      html += '<article class="finding-card finding-' + tone + '">';
      html += '<div class="finding-head">';
      html += '<span class="finding-impact">' + escapeHtml(item.impact || ('Hebel #' + (i + 1))) + '</span>';
      html += '<span class="finding-tone finding-tone-' + tone + '">' + escapeHtml(item.toneLabel || toneLabel(tone)) + '</span>';
      html += '</div>';
      html += '<h4 class="finding-title">' + escapeHtml(item.title) + '</h4>';
      html += '<p class="finding-summary">' + escapeHtml(item.summary) + '</p>';

      if (item.evidence && item.evidence.length) {
        html += '<ul class="finding-list">';
        for (var j = 0; j < item.evidence.length; j++) {
          html += '<li>' + escapeHtml(item.evidence[j]) + '</li>';
        }
        html += '</ul>';
      }

      if (item.action) {
        html += '<p class="finding-action"><strong>Nächster Hebel:</strong> ' + escapeHtml(item.action) + '</p>';
      }

      html += '</article>';
    }

    html += '</div></section>';
    return html;
  }

  function renderOpportunity(data) {
    var opportunity = data.opportunity || {};
    if (!opportunity.rangeMonth || !opportunity.rangeMonth.max) return '';

    return (
      '<section class="result-opportunity result-animate">' +
        '<div class="result-revenue-label">Gebremstes Nachfragepotenzial</div>' +
        '<div class="opportunity-grid">' +
          '<div class="result-revenue-big">' +
            '<span class="rev-label">Potenzial pro Monat</span>' +
            '<span class="rev-amount">' + formatRange(opportunity.rangeMonth) + '</span>' +
          '</div>' +
          '<div class="result-revenue-big">' +
            '<span class="rev-label">Potenzial pro Jahr</span>' +
            '<span class="rev-amount rev-amount-year">' + formatRange(opportunity.rangeYear) + '</span>' +
          '</div>' +
        '</div>' +
        '<div class="result-revenue-grid">' +
          renderRevenueStat('Kaufnahe Sichtbarkeit', escapeHtml(opportunity.basis.visibleKeywords || 'n/a')) +
          renderRevenueStat('Auftragswert', formatEuro(opportunity.basis.avgOrderValue || 0)) +
          renderRevenueStat('Annahme Conversion', escapeHtml(opportunity.basis.assumedConversionRange || '1-2%')) +
        '</div>' +
        '<p class="opportunity-note">' + escapeHtml(opportunity.note || 'Konservative Spanne auf Basis öffentlicher Daten und heuristischer Annahmen.') + '</p>' +
      '</section>'
    );
  }

  function renderRevenueStat(label, value) {
    return (
      '<div class="rev-stat">' +
        '<span class="rev-stat-label">' + label + '</span>' +
        '<span class="rev-stat-value">' + value + '</span>' +
      '</div>'
    );
  }

  function renderDetailSections(data) {
    var sections = data.details && data.details.sections ? data.details.sections : [];
    if (!sections.length) return '';

    var html = '<section class="result-details result-animate">';
    html += '<h3 class="result-section-title">Belege aus der Analyse</h3>';
    html += '<div class="detail-grid">';

    for (var i = 0; i < sections.length; i++) {
      var section = sections[i];
      html += '<article class="detail-card">';
      html += '<h4 class="detail-card-title">' + escapeHtml(section.title) + '</h4>';

      if (section.rows && section.rows.length) {
        for (var j = 0; j < section.rows.length; j++) {
          html += renderDetailRow(section.rows[j]);
        }
      }

      if (section.note) {
        html += '<p class="detail-card-note">' + escapeHtml(section.note) + '</p>';
      }

      html += '</article>';
    }

    html += '</div></section>';
    return html;
  }

  function renderDetailRow(row) {
    var tone = mapTone(row.status || 'warning');
    return (
      '<div class="result-detail-row">' +
        '<span class="result-detail-label">' + escapeHtml(row.label) + '</span>' +
        '<span class="result-detail-value result-val-' + tone + '">' + escapeHtml(row.value) + '</span>' +
      '</div>'
    );
  }

  function renderStory(data) {
    var story = (data.story || '').toString().trim();
    if (!story) return '';

    var paragraphs = story.split(/\n\n+/).filter(function (entry) {
      return entry && entry.trim().length > 12;
    });

    if (!paragraphs.length) return '';

    var html = '<section class="result-story result-animate">';
    html += '<h3 class="result-section-title">Strategische Einordnung</h3>';

    for (var i = 0; i < paragraphs.length; i++) {
      html += '<p>' + escapeHtml(paragraphs[i].replace(/\n/g, ' ').trim()) + '</p>';
    }

    html += '</section>';
    return html;
  }

  function renderConsultation(data) {
    var cta = data.cta || {};
    var primaryFinding = data.findings && data.findings.length ? data.findings[0].title : 'die Priorisierung Ihrer Hebel';
    var defaultMessage = 'Ich möchte die drei wichtigsten Hebel dieser Seite gemeinsam priorisieren.';

    return (
      '<section class="result-cta result-animate">' +
        '<h3>' + escapeHtml(cta.headline || 'Wenn Sie aus der Diagnose einen klaren Plan machen wollen, ist jetzt der richtige Schritt das Gespräch.') + '</h3>' +
        '<p class="result-cta-bridge">' + escapeHtml(cta.subline || 'Sie haben jetzt Klarheit über die Bremsen. Im Gespräch priorisieren wir, was auf dieser Seite zuerst Wirkung bringt.') + '</p>' +
        '<form id="audit-consultation-form" class="consultation-form" novalidate>' +
          '<div class="consultation-row">' +
            '<div class="consultation-field">' +
              '<label for="consult-name">Name</label>' +
              '<input id="consult-name" name="name" type="text" autocomplete="name" required>' +
            '</div>' +
            '<div class="consultation-field">' +
              '<label for="consult-email">Geschäftliche E-Mail</label>' +
              '<input id="consult-email" name="email" type="email" autocomplete="email" required>' +
            '</div>' +
          '</div>' +
          '<div class="consultation-row consultation-row-single">' +
            '<div class="consultation-field">' +
              '<label for="consult-company">Unternehmen</label>' +
              '<input id="consult-company" name="company" type="text" autocomplete="organization">' +
            '</div>' +
          '</div>' +
          '<div class="consultation-row consultation-row-single">' +
            '<div class="consultation-field">' +
              '<label for="consult-message">Worauf sollen wir zuerst schauen?</label>' +
              '<textarea id="consult-message" name="message" rows="4">' + escapeHtml(defaultMessage) + '</textarea>' +
            '</div>' +
          '</div>' +
          '<input type="hidden" name="jobId" value="' + escapeHtml(state.jobId || '') + '">' +
          '<input type="hidden" name="url" value="' + escapeHtml(data.meta.url || state.auditUrl || '') + '">' +
          '<input type="hidden" name="domain" value="' + escapeHtml(data.meta.domain || '') + '">' +
          '<input type="hidden" name="primaryFinding" value="' + escapeHtml(primaryFinding) + '">' +
          '<button type="submit" class="result-cta-btn consultation-submit-btn">' + escapeHtml(cta.primaryLabel || 'Analyse gemeinsam priorisieren') + '</button>' +
          '<span class="result-cta-sub">' + escapeHtml(cta.primarySubLabel || '20 Minuten · klare Reihenfolge · kein Sales-Theater') + '</span>' +
          '<div id="audit-consultation-feedback" class="consultation-feedback" aria-live="polite"></div>' +
        '</form>' +
        '<div class="result-cta-alt">' +
          '<span>Oder direkt:</span> ' +
          '<a href="' + escapeHtml(cta.altUrl || CONFIG.consultationAltUrl) + '" target="_blank" rel="noopener">' + escapeHtml(cta.altLabel || 'Direkt Strategiegespräch buchen') + '</a>' +
        '</div>' +
      '</section>'
    );
  }

  function bindConsultationForm() {
    var form = document.getElementById('audit-consultation-form');
    if (!form) return;
    form.addEventListener('submit', handleConsultationSubmit);
  }

  function handleConsultationSubmit(event) {
    event.preventDefault();

    var form = event.target;
    var feedback = document.getElementById('audit-consultation-feedback');
    var submitButton = form.querySelector('.consultation-submit-btn');
    var payload = {
      step: 'consultation',
      source: 'audit_results_v3',
      jobId: valueOf(form, 'jobId'),
      url: valueOf(form, 'url'),
      domain: valueOf(form, 'domain'),
      primaryFinding: valueOf(form, 'primaryFinding'),
      name: valueOf(form, 'name'),
      email: valueOf(form, 'email'),
      company: valueOf(form, 'company'),
      message: valueOf(form, 'message')
    };

    if (!payload.name) {
      showConsultationFeedback('Bitte Ihren Namen eingeben.', 'error');
      return;
    }

    if (!payload.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(payload.email)) {
      showConsultationFeedback('Bitte eine gültige E-Mail-Adresse eingeben.', 'error');
      return;
    }

    if (!CONFIG.webhookConsultation) {
      window.open(CONFIG.consultationAltUrl, '_blank', 'noopener');
      return;
    }

    if (submitButton) {
      submitButton.disabled = true;
      submitButton.textContent = 'Wird gesendet …';
    }

    fetch(CONFIG.webhookConsultation, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(function (res) { return parseJsonResponse(res, 'Beratung'); })
      .then(function (data) {
        if (!data || !data.ok) {
          throw new Error((data && data.error) || 'Beratung konnte nicht gesendet werden.');
        }

        form.innerHTML =
          '<div class="consultation-success">' +
            '<strong>Anfrage ist raus.</strong>' +
            '<p>Ich melde mich mit einer konkreten Einschätzung zum sinnvollsten nächsten Schritt.</p>' +
          '</div>';
      })
      .catch(function (error) {
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = 'Analyse gemeinsam priorisieren';
        }
        showConsultationFeedback(error.message || 'Senden fehlgeschlagen. Bitte erneut versuchen.', 'error');
      });

    function showConsultationFeedback(message, type) {
      if (!feedback) return;
      feedback.textContent = message;
      feedback.className = 'consultation-feedback consultation-feedback-' + type;
      feedback.style.display = 'block';
    }
  }

  function normalizeAuditData(data) {
    if (data && data.verdict && Array.isArray(data.findings)) {
      return normalizeV3Payload(data);
    }

    return normalizeLegacyPayload(data || {});
  }

  function normalizeV3Payload(data) {
    var meta = normalizeMeta(data.meta || {});
    var opportunity = normalizeOpportunity(data.opportunity || data.revenue || {});
    var details = normalizeDetails(data.details, data);

    return {
      meta: meta,
      verdict: {
        status: mapTone((data.verdict && data.verdict.status) || 'warning'),
        badge: (data.verdict && data.verdict.badge) || toneLabel((data.verdict && data.verdict.status) || 'warning'),
        headline: (data.verdict && data.verdict.headline) || ('Bei ' + (meta.domain || 'dieser Website') + ' bremst vor allem der Anfrageweg.'),
        subline: (data.verdict && data.verdict.subline) || (data.verdict && data.verdict.summary) || 'Die Analyse zeigt, welche Stellen zuerst priorisiert werden sollten.'
      },
      highlights: normalizeHighlights(data.highlights, data),
      findings: normalizeFindings(data.findings),
      opportunity: opportunity,
      details: details,
      story: (data.story || '').toString().trim(),
      cta: normalizeCta(data.cta)
    };
  }

  function normalizeLegacyPayload(data) {
    var meta = normalizeMeta(data.meta || {});
    var serp = Array.isArray(data.serpResults) ? data.serpResults : [];
    var performance = data.performance || {};
    var scores = data.scores || {};
    var seo = scores.seo || {};
    var tracking = scores.tracking || {};
    var steps = Array.isArray(data.journeySteps) ? data.journeySteps : [];

    var step3 = findStep(steps, 3);
    var step4 = findStep(steps, 4);

    var visibleKeywords = serp.filter(function (item) { return item.status && item.status !== 'not_found'; }).length;
    var totalKeywords = serp.length || 6;
    var missingKeywords = serp.filter(function (item) { return item.status === 'not_found'; });
    var topMissing = missingKeywords[0] || null;
    var missingCompetitors = topMissing && topMissing.competitors ? topMissing.competitors : [];

    var mobileScore = Number(performance.mobileScore || 0);
    var lcpMs = Number(performance.lcpMs || 0);

    var hasCases = isPositiveDetail(step3, 'Referenzen / Cases');
    var hasTestimonials = isPositiveDetail(step3, 'Kundenstimmen / Testimonials');
    var hasImpressum = isPositiveDetail(step3, 'Impressum');
    var hasContactForm = isPositiveDetail(step4, 'Kontaktformular');
    var hasLeadMagnet = isPositiveDetail(step4, 'Lead-Magnet');
    var hasPhone = isPositiveDetail(step4, 'Telefonnummer sichtbar');
    var hasBlog = isPositiveDetail(step4, 'Blog / Content Hub');

    var categories = [
      buildVisibilityFinding(serp, visibleKeywords, totalKeywords, topMissing, missingCompetitors),
      buildPerformanceFinding(mobileScore, lcpMs, performance),
      buildTrustFinding(hasCases, hasTestimonials, hasImpressum),
      buildConversionFinding(hasContactForm, hasLeadMagnet, hasPhone, hasBlog)
    ];

    categories.sort(function (a, b) { return b.severity - a.severity; });

    var topFindings = categories.slice(0, 3).map(function (item, index) {
      item.impact = 'Hebel #' + (index + 1);
      item.toneLabel = toneLabel(item.status);
      return item;
    });

    var top = topFindings[0] || buildFallbackFinding();
    var second = topFindings[1] || null;
    var verdictTone = mapTone(top.status);

    return {
      meta: meta,
      verdict: {
        status: verdictTone,
        badge: toneLabel(verdictTone),
        headline: buildLegacyVerdictHeadline(meta.domain, top, second),
        subline: top.summary
      },
      highlights: [
        {
          label: 'Kaufnahe Keywords',
          value: visibleKeywords + ' / ' + totalKeywords,
          help: visibleKeywords < totalKeywords ? 'sichtbar auf Seite 1' : 'voll sichtbar im geprüften Set',
          tone: visibleKeywords >= totalKeywords ? 'good' : visibleKeywords >= Math.ceil(totalKeywords / 2) ? 'warning' : 'bad'
        },
        {
          label: 'Mobile Performance',
          value: mobileScore + '/100',
          help: 'LCP ' + (performance.lcp || 'n/a'),
          tone: mobileScore >= 90 ? 'good' : mobileScore >= 65 ? 'warning' : 'bad'
        },
        {
          label: 'Nächster Schritt',
          value: hasLeadMagnet ? 'klar führbar' : hasContactForm ? 'Kontakt möglich' : 'unsauber',
          help: hasLeadMagnet ? 'Lead-Capture vorhanden' : hasContactForm ? 'nur Kontakt ohne Capture' : 'kein sauberer Conversion-Pfad',
          tone: hasLeadMagnet ? 'good' : hasContactForm ? 'warning' : 'bad'
        }
      ],
      findings: topFindings,
      opportunity: normalizeOpportunity({ revenue: data.revenue }),
      details: {
        sections: [
          {
            title: 'Google & Markt',
            rows: buildLegacySerpRows(serp),
            note: topMissing ? 'Der größte Sichtbarkeitsverlust liegt aktuell bei „' + topMissing.keyword + '“.' : 'Im geprüften Keyword-Set ist bereits Sichtbarkeit vorhanden.'
          },
          {
            title: 'Technik & Performance',
            rows: [
              { label: 'Mobile Score', value: mobileScore + '/100', status: mobileScore >= 90 ? 'good' : mobileScore >= 65 ? 'warning' : 'bad' },
              { label: 'Largest Contentful Paint', value: performance.lcp || 'n/a', status: lcpMs && lcpMs < 2500 ? 'good' : lcpMs && lcpMs < 4000 ? 'warning' : 'bad' },
              { label: 'Meta Description', value: seo.hasMetaDesc ? 'vorhanden' : 'fehlt', status: seo.hasMetaDesc ? 'good' : 'warning' },
              { label: 'Canonical', value: seo.hasCanonical ? 'vorhanden' : 'fehlt', status: seo.hasCanonical ? 'good' : 'warning' },
              { label: 'Sitemap', value: seo.hasSitemap ? 'vorhanden' : 'fehlt', status: seo.hasSitemap ? 'good' : 'warning' }
            ],
            note: 'Technik allein verkauft nicht, aber sie entscheidet, ob Sichtbarkeit und erster Eindruck überhaupt tragen.'
          },
          {
            title: 'Vertrauen & Conversion',
            rows: [
              { label: 'Referenzen / Cases', value: hasCases ? 'vorhanden' : 'nicht gefunden', status: hasCases ? 'good' : 'bad' },
              { label: 'Testimonials', value: hasTestimonials ? 'vorhanden' : 'nicht gefunden', status: hasTestimonials ? 'good' : 'bad' },
              { label: 'Kontaktformular', value: hasContactForm ? 'vorhanden' : 'nicht gefunden', status: hasContactForm ? 'good' : 'bad' },
              { label: 'Lead-Capture', value: hasLeadMagnet ? 'vorhanden' : 'nicht gefunden', status: hasLeadMagnet ? 'good' : 'warning' },
              { label: 'Telefon / direkter Kontakt', value: hasPhone ? 'sichtbar' : 'nicht gefunden', status: hasPhone ? 'good' : 'warning' },
              { label: 'Consent / Tracking-Basis', value: tracking.hasConsent ? 'vorhanden' : 'nicht sauber erkennbar', status: tracking.hasConsent ? 'good' : 'warning' }
            ],
            note: 'Hier entscheidet sich, ob aus Aufmerksamkeit tatsächlich Vertrauen und Anfrage wird.'
          }
        ]
      },
      story: (data.story || '').toString().trim(),
      cta: {
        headline: 'Der Check zeigt die Bremsen. Im Gespräch priorisieren wir, was auf dieser Seite zuerst Wirkung bringt.',
        subline: 'Wenn Sie wissen wollen, was zuerst geschärft werden sollte und was warten kann, ist jetzt der richtige Schritt das Gespräch.',
        primaryLabel: 'Analyse gemeinsam priorisieren',
        primarySubLabel: '20 Minuten · klare Reihenfolge · kein Sales-Theater',
        altUrl: CONFIG.consultationAltUrl,
        altLabel: 'Direkt Strategiegespräch buchen'
      }
    };
  }

  function normalizeMeta(meta) {
    return {
      domain: meta.domain || '',
      url: meta.url || '',
      branche: meta.branche || '',
      standort: meta.standort || ''
    };
  }

  function normalizeHighlights(highlights, data) {
    if (Array.isArray(highlights) && highlights.length) {
      return highlights.slice(0, 3).map(function (item) {
        return {
          label: item.label || '',
          value: item.value || '',
          help: item.help || item.detail || '',
          tone: mapTone(item.tone || item.status || 'warning')
        };
      });
    }

    return normalizeLegacyPayload(data).highlights;
  }

  function normalizeFindings(findings) {
    return (findings || []).slice(0, 3).map(function (item, index) {
      return {
        title: item.title || ('Hebel ' + (index + 1)),
        summary: item.summary || '',
        evidence: Array.isArray(item.evidence) ? item.evidence.slice(0, 4) : [],
        action: item.action || item.recommendation || '',
        impact: item.impact || ('Hebel #' + (index + 1)),
        status: mapTone(item.status || 'warning'),
        toneLabel: item.toneLabel || toneLabel(item.status || 'warning')
      };
    });
  }

  function normalizeOpportunity(source) {
    var revenue = source && source.revenue ? source.revenue : source;
    var monthRange;
    var yearRange;

    if (source && source.rangeMonth) {
      monthRange = sanitizeRange(source.rangeMonth);
      yearRange = sanitizeRange(source.rangeYear || { min: monthRange.min * 12, max: monthRange.max * 12 });
    } else {
      var monthBase = Number(revenue && revenue.lostRevenueMonth || 0);
      monthRange = buildRange(monthBase);
      yearRange = buildRange(Number(revenue && revenue.lostRevenueYear || (monthRange.max * 12)));
    }

    return {
      rangeMonth: monthRange,
      rangeYear: yearRange,
      basis: {
        visibleKeywords: source && source.basis && source.basis.visibleKeywords ? source.basis.visibleKeywords : buildBasisVisibleKeywords(revenue),
        avgOrderValue: Number(source && source.basis && source.basis.avgOrderValue || revenue && revenue.kundenwert || 0),
        assumedConversionRange: source && source.basis && source.basis.assumedConversionRange ? source.basis.assumedConversionRange : buildBasisConversionRange(revenue)
      },
      note: source && source.note ? source.note : 'Konservative Spanne auf Basis öffentlicher Daten, Suchvolumen und heuristischer Conversion-Annahmen.'
    };
  }

  function normalizeDetails(details, data) {
    if (details && Array.isArray(details.sections) && details.sections.length) {
      return {
        sections: details.sections.map(function (section) {
          return {
            title: section.title || '',
            note: section.note || '',
            rows: (section.rows || []).map(function (row) {
              return {
                label: row.label || '',
                value: row.value || '',
                status: mapTone(row.status || 'warning')
              };
            })
          };
        })
      };
    }

    return normalizeLegacyPayload(data).details;
  }

  function normalizeCta(cta) {
    if (!cta) {
      return {
        headline: 'Wenn Sie aus der Diagnose einen klaren Plan machen wollen, ist jetzt der richtige Schritt das Gespräch.',
        subline: 'Wir priorisieren gemeinsam, was auf dieser Seite zuerst Wirkung bringt und was nur Aktivität erzeugt.',
        primaryLabel: 'Analyse gemeinsam priorisieren',
        primarySubLabel: '20 Minuten · klare Reihenfolge · kein Sales-Theater',
        altUrl: CONFIG.consultationAltUrl,
        altLabel: 'Direkt Strategiegespräch buchen'
      };
    }

    if (cta.primary || cta.secondary) {
      return {
        headline: cta.headline || 'Wenn Sie aus der Diagnose einen klaren Plan machen wollen, ist jetzt der richtige Schritt das Gespräch.',
        subline: cta.subline || 'Der Schnell-Check zeigt die Bremsen. Im Gespräch priorisieren wir den sinnvollen Hebel für diese Seite.',
        primaryLabel: cta.primary && cta.primary.label ? cta.primary.label : 'Analyse gemeinsam priorisieren',
        primarySubLabel: cta.primary && cta.primary.sublabel ? cta.primary.sublabel : '20 Minuten · klare Reihenfolge · kein Sales-Theater',
        altUrl: cta.secondary && cta.secondary.url ? cta.secondary.url : CONFIG.consultationAltUrl,
        altLabel: cta.secondary && cta.secondary.label ? cta.secondary.label : 'Direkt Strategiegespräch buchen'
      };
    }

    return {
      headline: 'Wenn Sie aus der Diagnose einen klaren Plan machen wollen, ist jetzt der richtige Schritt das Gespräch.',
      subline: 'Der Schnell-Check zeigt die Bremsen. Im Gespräch priorisieren wir den sinnvollen Hebel für diese Seite.',
      primaryLabel: cta.label || 'Analyse gemeinsam priorisieren',
      primarySubLabel: cta.sublabel || '20 Minuten · klare Reihenfolge · kein Sales-Theater',
      altUrl: cta.altUrl || CONFIG.consultationAltUrl,
      altLabel: cta.altLabel || 'Direkt Strategiegespräch buchen'
    };
  }

  function buildVisibilityFinding(serp, visibleKeywords, totalKeywords, topMissing, competitors) {
    var status = visibleKeywords >= totalKeywords ? 'good' : visibleKeywords >= Math.ceil(totalKeywords / 2) ? 'warning' : 'bad';
    var severity = status === 'bad' ? 4 : status === 'warning' ? 3 : 1;
    var evidence = [
      visibleKeywords + ' von ' + totalKeywords + ' geprüften Keywords landen auf Seite 1.'
    ];

    if (topMissing && topMissing.keyword) {
      evidence.push('Für „' + topMissing.keyword + '“ fehlt die Seite-1-Sichtbarkeit.');
    }

    if (competitors && competitors.length) {
      evidence.push('Stattdessen tauchen dort ' + competitors.slice(0, 2).map(function (entry) { return entry.name; }).join(' und ') + ' auf.');
    }

    return {
      title: 'Sichtbarkeit',
      status: status,
      severity: severity,
      summary: status === 'bad'
        ? 'Ein relevanter Teil der Nachfrage landet aktuell zuerst bei Wettbewerbern und nicht bei Ihnen.'
        : status === 'warning'
          ? 'Es gibt Sichtbarkeit, aber zu wenig Dominanz für kaufnahe Suchanfragen.'
          : 'Die Website ist im geprüften Suchraum bereits sichtbar.',
      evidence: evidence,
      action: 'Angebotsseiten auf Suchintention, lokale Proof-Signale und klaren Conversion-Fokus zuschneiden.'
    };
  }

  function buildPerformanceFinding(mobileScore, lcpMs, performance) {
    var status = mobileScore >= 90 && lcpMs > 0 && lcpMs < 2500 ? 'good' : mobileScore >= 65 ? 'warning' : 'bad';
    var severity = status === 'bad' ? 4 : status === 'warning' ? 2 : 1;

    return {
      title: 'Erster Eindruck',
      status: status,
      severity: severity,
      summary: status === 'bad'
        ? 'Der erste Besuch kostet zu viel Geduld. Das bremst noch vor Vertrauen und Conversion.'
        : status === 'warning'
          ? 'Der erste Eindruck ist brauchbar, aber noch nicht stark genug für kalten Traffic.'
          : 'Die Performance wirkt nicht wie der größte Blocker.',
      evidence: [
        'Mobile Score: ' + mobileScore + '/100',
        'Largest Contentful Paint: ' + (performance.lcp || 'n/a'),
        'CLS: ' + (performance.cls || 'n/a')
      ],
      action: 'Mobile Ladeweg und Above-the-fold-Inhalt zuerst optimieren, bevor mehr Traffic auf die Seite geht.'
    };
  }

  function buildTrustFinding(hasCases, hasTestimonials, hasImpressum) {
    var missing = 0;
    if (!hasCases) missing += 1;
    if (!hasTestimonials) missing += 1;
    if (!hasImpressum) missing += 1;

    var status = missing === 0 ? 'good' : missing === 1 ? 'warning' : 'bad';
    var severity = status === 'bad' ? 3 : status === 'warning' ? 2 : 1;

    return {
      title: 'Vertrauen',
      status: status,
      severity: severity,
      summary: status === 'bad'
        ? 'Die Seite zeigt zu wenig Beweis dafür, warum ein Besucher ausgerechnet Ihnen schreiben sollte.'
        : status === 'warning'
          ? 'Es gibt erste Vertrauenssignale, aber die Argumentation bleibt noch zu dünn.'
          : 'Trust-Signale sind sichtbar und helfen dem Abschluss.',
      evidence: [
        'Cases: ' + (hasCases ? 'vorhanden' : 'nicht gefunden'),
        'Testimonials: ' + (hasTestimonials ? 'vorhanden' : 'nicht gefunden'),
        'Impressum / Basis-Vertrauen: ' + (hasImpressum ? 'vorhanden' : 'nicht gefunden')
      ],
      action: 'Proof nicht streuen, sondern direkt an den kaufnahen Stellen der Seite verankern.'
    };
  }

  function buildConversionFinding(hasContactForm, hasLeadMagnet, hasPhone, hasBlog) {
    var severity = 1;
    var status = 'good';

    if (!hasContactForm) {
      severity = 4;
      status = 'bad';
    } else if (!hasLeadMagnet) {
      severity = 3;
      status = 'warning';
    } else if (!hasPhone || !hasBlog) {
      severity = 2;
      status = 'warning';
    }

    return {
      title: 'Nächster Schritt',
      status: status,
      severity: severity,
      summary: status === 'bad'
        ? 'Selbst wenn Interesse entsteht, führt die Seite zu unscharf oder gar nicht in eine Anfrage.'
        : status === 'warning'
          ? 'Ein Kontaktweg ist da, aber der Conversion-Pfad bleibt zu schwach oder zu spät.'
          : 'Der nächste Schritt ist bereits klar führbar.',
      evidence: [
        'Kontaktformular: ' + (hasContactForm ? 'vorhanden' : 'nicht gefunden'),
        'Lead-Capture: ' + (hasLeadMagnet ? 'vorhanden' : 'nicht gefunden'),
        'Direkter Kontakt: ' + (hasPhone ? 'sichtbar' : 'nicht gefunden'),
        'Content-Hub: ' + (hasBlog ? 'vorhanden' : 'nicht gefunden')
      ],
      action: 'Nur einen klaren Conversion-Pfad priorisieren und ihn in Copy, Proof und CTA durchziehen.'
    };
  }

  function buildFallbackFinding() {
    return {
      title: 'Priorisierung',
      status: 'warning',
      severity: 2,
      summary: 'Die Analyse zeigt Reibung, aber keine einzelne dominante Bremse.',
      evidence: ['Die Datenbasis reicht für eine erste Einschätzung.'],
      action: 'Im nächsten Schritt die Hebel gemeinsam priorisieren.'
    };
  }

  function buildLegacyVerdictHeadline(domain, top, second) {
    var label = domain || 'dieser Website';
    var primary = top && top.title ? top.title : 'der Anfrageweg';
    var secondary = second && second.title ? ' und ' + second.title : '';
    return 'Bei ' + label + ' bremsen vor allem ' + primary + secondary + ' die Nachfrage.';
  }

  function buildLegacySerpRows(serp) {
    if (!serp || !serp.length) {
      return [{ label: 'Sichtbarkeit', value: 'keine SERP-Daten', status: 'warning' }];
    }

    return serp.slice(0, 4).map(function (item) {
      var statusText = item.status === 'not_found'
        ? 'nicht auf Seite 1'
        : item.status === 'top3'
          ? 'Top 3'
          : item.position
            ? 'Position ' + item.position
            : 'sichtbar';

      return {
        label: item.keyword,
        value: statusText + ' · ~' + (item.volume || 0) + '/Mo',
        status: item.status === 'top3' ? 'good' : item.status === 'page1' ? 'warning' : 'bad'
      };
    });
  }

  function buildBasisVisibleKeywords(revenue) {
    if (revenue && revenue.totalLostTraffic) {
      return '~' + revenue.totalLostTraffic + ' verlorene Besuche / Monat';
    }
    return 'n/a';
  }

  function buildBasisConversionRange(revenue) {
    if (revenue && revenue.conversionRate) {
      return revenue.conversionRate.toString();
    }
    return '1-2%';
  }

  function buildRange(value) {
    value = Number(value || 0);
    if (!value) {
      return { min: 0, max: 0 };
    }

    var min = roundRangeValue(value * 0.65);
    var max = roundRangeValue(value * 1.35);

    if (max < min) {
      max = min;
    }

    return { min: min, max: max };
  }

  function sanitizeRange(range) {
    return {
      min: Number(range && range.min || 0),
      max: Number(range && range.max || 0)
    };
  }

  function roundRangeValue(value) {
    if (value < 1000) return Math.round(value / 50) * 50;
    if (value < 5000) return Math.round(value / 100) * 100;
    if (value < 15000) return Math.round(value / 250) * 250;
    return Math.round(value / 500) * 500;
  }

  function findStep(steps, nr) {
    for (var i = 0; i < steps.length; i++) {
      if (String(steps[i].nr) === String(nr)) {
        return steps[i];
      }
    }
    return null;
  }

  function isPositiveDetail(step, label) {
    if (!step || !Array.isArray(step.details)) return false;

    for (var i = 0; i < step.details.length; i++) {
      var detail = step.details[i];
      if ((detail.label || '').toLowerCase().indexOf(label.toLowerCase()) !== -1) {
        return detail.status === 'good';
      }
    }

    return false;
  }

  function showLoader() {
    var formWrap = document.getElementById('audit-form-inner');
    var loader = document.getElementById('audit-loader');

    if (formWrap) formWrap.style.display = 'none';
    if (loader) {
      loader.innerHTML =
        '<div class="loader-icon" id="loader-icon">🔍</div>' +
        '<div class="loader-text" id="loader-text">Anfrage wird übermittelt …</div>' +
        '<div class="loader-sub" id="loader-sub">Manueller, tiefer Marktcheck — händische Analyse deiner Region innerhalb von 48 Stunden per E-Mail.</div>' +
        '<div class="loader-progress-track"><div class="loader-progress-fill" id="loader-progress"></div></div>';
      loader.style.display = 'block';
      loader.classList.add('is-active');
    }
  }

  function updateLoaderStep(step) {
    var icon = document.getElementById('loader-icon');
    var text = document.getElementById('loader-text');
    var sub = document.getElementById('loader-sub');
    var progress = document.getElementById('loader-progress');

    if (icon) icon.textContent = step.icon;
    if (text) {
      text.style.opacity = '0';
      window.setTimeout(function () {
        text.textContent = step.text;
        text.style.opacity = '1';
      }, 180);
    }
    if (sub) {
      sub.style.opacity = '0';
      window.setTimeout(function () {
        sub.textContent = step.sub;
        sub.style.opacity = '1';
      }, 260);
    }

    if (progress && state.pollStart) {
      var elapsed = Date.now() - state.pollStart;
      var pct = Math.min(95, (elapsed / CONFIG.pollTimeout) * 100);
      progress.style.width = pct + '%';
    }
  }

  function showLoaderError(message) {
    var loader = document.getElementById('audit-loader');
    if (!loader) return;

    loader.innerHTML =
      '<div class="loader-error">' +
        '<div class="loader-error-icon">⚠️</div>' +
        '<p class="loader-error-text">' + escapeHtml(message) + '</p>' +
        '<button class="audit-retry-btn" onclick="location.reload()">Erneut versuchen</button>' +
        '<div class="result-cta-alt">' +
          '<span>Wenn es dringend ist:</span> ' +
          '<a href="' + escapeHtml(CONFIG.consultationAltUrl) + '" target="_blank" rel="noopener">Direkt Strategiegespräch buchen</a>' +
        '</div>' +
      '</div>';
  }

  function showFormError(message) {
    var el = document.getElementById('audit-form-error');
    if (!el) return;
    el.textContent = message;
    el.style.display = 'block';
  }

  function clearFormError() {
    var el = document.getElementById('audit-form-error');
    if (el) el.style.display = 'none';
  }

  function clearResults() {
    var results = document.getElementById('audit-results');
    var wrapper = document.getElementById('audit-main-wrapper');

    if (results) {
      results.innerHTML = '';
      results.style.display = 'none';
      results.classList.remove('is-visible');
    }

    if (wrapper) {
      wrapper.classList.remove('view-mode-results');
    }
  }

  function clearPollTimer() {
    if (state.pollTimer) {
      window.clearInterval(state.pollTimer);
      state.pollTimer = null;
    }
  }

  function setPhase(phase) {
    state.phase = phase;
    var wrapper = document.querySelector('.audit-wrapper');
    if (wrapper) wrapper.setAttribute('data-audit-phase', phase);
  }

  function staggerReveal(elements) {
    for (var i = 0; i < elements.length; i++) {
      (function (element, delay) {
        window.setTimeout(function () {
          element.classList.add('is-revealed');
        }, delay);
      })(elements[i], i * CONFIG.animDelay);
    }
  }

  function mapTone(status) {
    var value = (status || '').toString().toLowerCase();
    if (value === 'critical') return 'bad';
    if (value === 'mixed') return 'warning';
    if (value === 'solid') return 'good';
    if (value === 'page1') return 'warning';
    if (value === 'top3') return 'good';
    if (value === 'not_found') return 'bad';
    if (value !== 'good' && value !== 'warning' && value !== 'bad') {
      return 'warning';
    }
    return value;
  }

  function toneLabel(status) {
    var tone = mapTone(status);
    return tone === 'good' ? 'Solide' : tone === 'warning' ? 'Verbesserbar' : 'Kritisch';
  }

  function formatRange(range) {
    if (!range || (!range.min && !range.max)) return 'n/a';
    return formatEuro(range.min) + ' – ' + formatEuro(range.max);
  }

  function formatEuro(value) {
    var amount = Number(value || 0);
    return amount.toLocaleString('de-DE') + ' €';
  }

  function normalizeInputUrl(input) {
    var value = (input || '').trim();
    if (!value) return '';
    if (!/^https?:\/\//i.test(value)) {
      value = 'https://' + value;
    }
    try {
      var parsed = new URL(value);
      if (!/^https?:$/.test(parsed.protocol)) return '';
      parsed.hash = '';
      return parsed.toString().replace(/\/+$/, '');
    } catch (error) {
      return '';
    }
  }

  function valueOf(form, name) {
    var field = form.querySelector('[name="' + name + '"]');
    return field ? (field.value || '').trim() : '';
  }

  function parseJsonResponse(res, label) {
    return res.text().then(function (text) {
      var body = (text || '').trim();
      var http = res && typeof res.status === 'number' ? ' (HTTP ' + res.status + ')' : '';

      if (!body) {
        throw new Error(label + ' liefert keine JSON-Antwort' + http + '.');
      }

      try {
        return JSON.parse(body);
      } catch (error) {
        throw new Error(label + ' liefert ungueltiges JSON' + http + '.');
      }
    });
  }

  function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    var div = document.createElement('div');
    div.textContent = String(value);
    return div.innerHTML;
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
