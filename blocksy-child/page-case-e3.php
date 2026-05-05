<?php
/**
 * Template Name: Case Study – E3 New Energy
 * Description: Flagship Case Study: E3 New Energy GmbH – vom Lead-Einkauf zur Lead-Autonomie
 *
 * Design: Nutzt Nexus Design System (design-system.css + case-study.css)
 * SEO-Meta: inc/seo-meta.php (ACF-Felder: seo_title, seo_description, og_image)
 *
 * @package Blocksy_Child
 */

get_header();

$analysis_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/anfrage-system-analyse/' );
$demo_url     = home_url( '/energie-fahrplan-demo/' );
$wgos_url     = nexus_get_primary_public_url( 'wgos', home_url( '/wordpress-growth-operating-system/' ) );
$local_wp_url = nexus_get_primary_public_url( 'agentur', home_url( '/wordpress-agentur-hannover/' ) );
$energy_url  = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
$e3_canon    = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics  = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];

$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? '-85,3 %';
$e3_cpl_before       = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after        = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '9 Monate';
$e3_timeframe_dative = $e3_metrics['timeframe']['display_dative'] ?? '9 Monaten';
?>

<div class="cs-case-wrapper">

<!-- Reading Progress Bar -->
<div id="nx-progress-bar"></div>

<!-- ========================================
     SMART SIDE NAV
     ======================================== -->
<nav class="nx-sidenav nx-hide-mobile" id="case-nav" aria-label="Seitennavigation">
    <ul>
        <li><a href="#kontext"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">Kontext</span></a></li>
        <li><a href="#constraints"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">Ausgangslage</span></a></li>
        <li><a href="#warum"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">Warum Setup</span></a></li>
        <li><a href="#vorgehen"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">Vorgehen</span></a></li>
        <li><a href="#nicht"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">Nicht gemacht</span></a></li>
        <li><a href="#ergebnis"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">Ergebnis</span></a></li>
        <li><a href="#faq"><span class="nx-sidenav__dot"></span><span class="nx-sidenav__text">FAQ</span></a></li>
    </ul>
</nav>

<!-- ========================================
     SECTION 1: HERO
     ======================================== -->
<section class="cs-case-hero nx-section nx-hero--compact" id="hero">
    <div class="nx-container">
        <span class="nx-badge nx-badge--gold">Flagship Case Study</span>

        <h1 class="cs-case-hero__title">
            Von <?php echo esc_html( $e3_cpl_before ); ?> CPL auf <span class="nx-text-gold"><?php echo esc_html( $e3_cpl_after ); ?></span>&nbsp;—<br>
            <?php echo esc_html( $e3_lead_count ); ?> Anfragen im System.
        </h1>

        <p class="cs-case-hero__subtitle">
            Wie E3 New Energy GmbH den externen Lead-Einkauf durch ein eigenes,
            skalierbares Lead-System ersetzte&nbsp;— mit Technical SEO, Basis-Content,
            Performance Ads und automatisierter Vorqualifizierung.
        </p>

        <!-- KPI Snapshot (3 Tiles) -->
        <div class="cs-kpi-row">
            <div class="cs-kpi-tile">
                <span class="cs-kpi-value nx-text-gold"><?php echo esc_html( $e3_lead_count ); ?></span>
                <span class="cs-kpi-label">Qualif. Anfragen</span>
            </div>
            <div class="cs-kpi-tile cs-kpi-tile--center">
                <span class="cs-kpi-value nx-text-gold"><?php echo esc_html( $e3_cpl_reduction ); ?></span>
                <span class="cs-kpi-label">Cost per Lead</span>
            </div>
            <div class="cs-kpi-tile">
                <span class="cs-kpi-value nx-text-gold"><?php echo esc_html( $e3_sales_conversion ); ?></span>
                <span class="cs-kpi-label">Sales-Conversion</span>
            </div>
        </div>

        <!-- Hero CTAs -->
        <div class="cs-hero-ctas">
            <a href="<?php echo esc_url( $analysis_url ); ?>"
               class="nx-btn nx-btn--primary"
               data-track-action="cta_case_hero_analysis"
               data-track-category="lead_gen">
                Anfrage-System-Analyse starten
            </a>
            <a href="<?php echo esc_url( $demo_url ); ?>"
               class="nx-btn nx-btn--ghost">
                EnergieFahrplan-Demo ansehen →
            </a>
        </div>

        <!-- Meta Row -->
        <div class="cs-hero-meta">
            <span class="cs-meta-item">Branche: Erneuerbare Energien</span>
            <span class="cs-meta-sep">·</span>
            <span class="cs-meta-item">Zielmarkt: KMU + B2C</span>
            <span class="cs-meta-sep">·</span>
            <span class="cs-meta-item">Kanal: Technical SEO &amp; CRO + Performance Ads</span>
            <span class="cs-meta-sep">·</span>
            <span class="cs-meta-item">Fokus: Segmentierung + Qualifizierung + Automatisierung</span>
            <span class="cs-meta-sep">·</span>
            <span class="cs-meta-item">Zeitraum: <?php echo esc_html( $e3_timeframe ); ?></span>
            <span class="cs-meta-sep">·</span>
            <span class="cs-meta-item">Budget: 20.000&nbsp;€</span>
        </div>
    </div>
</section>

<!-- ========================================
     SECTION 2: KONTEXT
     ======================================== -->
<section class="nx-section cs-section-alt" id="kontext">
    <div class="nx-container">

        <div class="nx-section-header">
            <span class="nx-badge nx-badge--ghost">Ausgangssituation</span>
            <h2 class="nx-headline-section" style="margin-top:1rem;">
                Was E3 New Energy wirklich brauchte
            </h2>
        </div>

        <div class="cs-kontext-grid">
            <div class="cs-kontext-text nx-prose">
                <p>
                    E3 New Energy GmbH plant und vermittelt Photovoltaik-Anlagen und
                    Wärmepumpen für KMU- und B2C-Kunden. Trotz fehlender digitaler
                    Akquisekanäle war die operative Basis stark: solide Website,
                    klare interne Struktur und ein leistungsfähiges Team.
                </p>
                <p>
                    Das Wachstum hing jedoch zu stark am externen Lead-Einkauf
                    (ø <?php echo esc_html( $e3_cpl_before ); ?> pro Lead). Ziel war nicht mehr Reichweite um jeden Preis,
                    sondern eine eigene Pipeline: eine Nachfragebasis, die kontinuierlich
                    qualifizierte Anfragen liefert und die Abhängigkeit von Drittanbietern senkt.
                </p>
                <p>
                    Die Qualität im Projekt war kein Zufall: ein hochqualifiziertes
                    Handwerksteam, starke operative Umsetzung und die strategische
                    Partnerschaft als Viessmann Premium-Partner bildeten das Fundament.
                </p>
                <ul class="cs-strength-list">
                    <li><strong>Solide Basis:</strong> Website und Prozesse waren bereits belastbar.</li>
                    <li><strong>Fachkompetenz:</strong> Starkes Handwerksteam plus operative Exzellenz.</li>
                    <li><strong>Premium-Netzwerk:</strong> Viessmann Premium-Partner als Qualitätsanker.</li>
                </ul>
            </div>

            <div class="cs-kontext-card nx-card">
                <p class="nx-card__subtitle">Eckdaten</p>
                <ul class="cs-fact-list">
                    <li>
                        <span class="cs-fact-label">Unternehmen</span>
                        <span class="cs-fact-value">E3 New Energy GmbH</span>
                    </li>
                    <li>
                        <span class="cs-fact-label">Website</span>
                        <span class="cs-fact-value">
                            <a class="cs-fact-link" href="https://e3-newenergy.de/" target="_blank" rel="noopener noreferrer">
                                e3-newenergy.de
                            </a>
                        </span>
                    </li>
                    <li>
                        <span class="cs-fact-label">Branche</span>
                        <span class="cs-fact-value">Erneuerbare Energien</span>
                    </li>
                    <li>
                        <span class="cs-fact-label">Zielgruppe</span>
                        <span class="cs-fact-value">KMU + B2C (> 5 Mio. € Umsatz)</span>
                    </li>
                    <li>
                        <span class="cs-fact-label">Ausgangslage</span>
                        <span class="cs-fact-value">Solide Website, aber ohne digitale Akquisekanäle</span>
                    </li>
                    <li>
                        <span class="cs-fact-label">Team</span>
                        <span class="cs-fact-value">Hochqualifiziertes Handwerksteam</span>
                    </li>
                    <li>
                        <span class="cs-fact-label">Partner</span>
                        <span class="cs-fact-value">Viessmann Premium-Partner</span>
                    </li>
                    <li>
                        <span class="cs-fact-label">Zeitraum</span>
                        <span class="cs-fact-value"><?php echo esc_html( $e3_timeframe ); ?> WGOS</span>
                    </li>
                    <li>
                        <span class="cs-fact-label">System</span>
                        <span class="cs-fact-value">WordPress Growth OS</span>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</section>

<!-- ========================================
     SECTION 3: CONSTRAINTS
     ======================================== -->
<section class="nx-section" id="constraints">
    <div class="nx-container">

        <div class="nx-section-header">
            <span class="nx-badge nx-badge--ghost">Die echten Hürden</span>
            <h2 class="nx-headline-section" style="margin-top:1rem;">
                Was die Arbeit schwierig machte
            </h2>
            <p class="nx-subheadline" style="margin:1rem auto 0;">
                Drei strukturelle Probleme, die ad-hoc-Lösungen ausschlossen.
            </p>
        </div>

        <div class="nx-grid nx-grid-3" style="margin-top:3rem;">

            <div class="nx-card nx-card--flat cs-constraint-card">
                <div class="cs-constraint-num">01</div>
                <h3 class="nx-card__title">Kein verwertbares Tracking</h3>
                <p class="nx-card__text">
                    GA4 war vorhanden, aber ohne saubere Event-Logik, Conversion-Definitionen
                    und Attribution. Entscheidungen basierten auf Annahmen statt Daten.
                </p>
            </div>

            <div class="nx-card nx-card--flat cs-constraint-card">
                <div class="cs-constraint-num">02</div>
                <h3 class="nx-card__title">Kein digitaler Akquise-Funnel</h3>
                <p class="nx-card__text">
                    Es fehlte ein integriertes Setup aus Tracking, Landingpages,
                    Content-Basis und Segmentierungslogik. Ergebnis: keine planbare
                    Pipeline aus eigenen Leads.
                </p>
            </div>

            <div class="nx-card nx-card--flat cs-constraint-card cs-constraint-card--highlight">
                <div class="cs-constraint-num">03</div>
                <h3 class="nx-card__title">Leads zugekauft — ø <?php echo esc_html( $e3_cpl_before ); ?>/Stück</h3>
                <p class="nx-card__text">
                    Externe Listen und Kauf-Leads waren teuer und heterogen.
                    Keine belastbare Vorqualifizierung nach Anlagengröße, Bedarf und
                    Entscheidungsphase&nbsp;— Vertrieb und Neugierige landeten im selben Prozess.
                </p>
            </div>

        </div>

    </div>
</section>

<!-- ========================================
     SECTION 4: METHODIK-WARUM
     ======================================== -->
<section class="nx-section" id="warum">
    <div class="nx-container">

        <div class="nx-section-header">
            <span class="nx-badge nx-badge--gold">Methodik</span>
            <h2 class="nx-headline-section" style="margin-top:1rem;">
                Warum Technical SEO &amp; Basis-Content zuerst kamen
            </h2>
            <p class="nx-subheadline" style="margin:1rem auto 0;">
                Performance Ads skalieren nur dann effizient, wenn Tracking,
                Ladezeiten und Landingpage-Struktur zuerst sauber stehen.
            </p>
        </div>

        <div class="cs-method-grid" style="margin-top:3rem;">
            <article class="cs-method-card">
                <p class="cs-method-kicker">Performance-Hebel</p>
                <h3 class="nx-card__title">Technisches Fundament für Ads</h3>
                <p class="nx-card__text">
                    Technical SEO wurde als Infrastruktur-Layer aufgebaut:
                    Tracking-Integrität, Mobile-Performance und klare Landingpage-Struktur
                    als Grundlage für belastbare Kampagnenentscheidungen.
                </p>
            </article>

            <article class="cs-method-card cs-method-card--highlight">
                <p class="cs-method-kicker">Effizienz</p>
                <h3 class="nx-card__title">Höherer Quality Score, niedrigere CPCs</h3>
                <p class="nx-card__text">
                    Ein sauberer technischer Aufbau verbessert Relevanzsignale und reduziert
                    Reibung. Das reduziert Klickpreise und erhöht gleichzeitig die
                    Conversion-Wahrscheinlichkeit auf den Zielseiten.
                </p>
            </article>

            <article class="cs-method-card">
                <p class="cs-method-kicker">Skalierung</p>
                <h3 class="nx-card__title">Basis-Content für Vorqualifizierung</h3>
                <p class="nx-card__text">
                    Basis-Content beantwortet zentrale Entscheidungsfragen vor dem Kontakt,
                    segmentiert Suchintentionen und liefert dem Vertrieb bereits vorbereitete
                    Erstgespräche.
                </p>
            </article>
        </div>

    </div>
</section>

<!-- ========================================
     SECTION 5: VORGEHEN (3 Phasen)
     ======================================== -->
<section class="nx-section cs-section-alt" id="vorgehen">
    <div class="nx-container">

        <div class="nx-section-header">
            <span class="nx-badge nx-badge--gold">Das Vorgehen</span>
            <h2 class="nx-headline-section" style="margin-top:1rem;">
                3 Phasen. Ein System.
            </h2>
            <p class="nx-subheadline" style="margin:1rem auto 0;">
                Kein Big-Bang-Relaunch. Systematischer Aufbau in Monatssprints.
            </p>
        </div>

        <div class="cs-phases" style="margin-top:3rem;">

            <!-- Phase 1 -->
            <div class="cs-phase-item">
                <div class="cs-phase-header">
                    <div class="nx-step__number">1</div>
                    <div>
                        <span class="cs-phase-label">Monat 1–2</span>
                        <h3 class="cs-phase-title">Fundament: Tracking + Performance</h3>
                    </div>
                </div>
                <div class="cs-phase-body">
                    <p class="nx-card__text">
                        Server-Side GTM aufgesetzt, GA4-Conversions sauber definiert,
                        Consent Mode v2 implementiert. PageSpeed Mobile von 34 auf 98 erhöht.
                        Core Web Vitals stabilisiert, LCP unter 0,8s.
                    </p>
                    <ul class="cs-phase-outcomes">
                        <li>✓ 98 PageSpeed Mobile</li>
                        <li>✓ &gt;92% Tracking-Genauigkeit</li>
                        <li>✓ Consent Mode v2 DSGVO-konform</li>
                    </ul>
                </div>
            </div>

            <!-- Phase 2 -->
            <div class="cs-phase-item">
                <div class="cs-phase-header">
                    <div class="nx-step__number">2</div>
                    <div>
                        <span class="cs-phase-label">Monat 3–4</span>
                        <h3 class="cs-phase-title">Technical SEO &amp; Basis-Content</h3>
                    </div>
                </div>
                <div class="cs-phase-body">
                    <p class="nx-card__text">
                        Funnel-Landingpages und Tracking-Setups technisch konsolidiert:
                        Core Web Vitals stabilisiert, mobile Ladezeiten optimiert,
                        Event-Qualität erhöht. Parallel Basis-Content für
                        PV-/Wärmepumpen-Entscheidungsfragen aufgebaut, um Traffic früh zu
                        segmentieren und Anfragen vorzuqualifizieren.
                    </p>
                    <ul class="cs-phase-outcomes">
                        <li>✓ Technisches Ads-Fundament stabilisiert</li>
                        <li>✓ Höhere Landingpage-Relevanz für Kampagnen</li>
                        <li>✓ Basis-Content für Intent-Segmentierung live</li>
                    </ul>
                </div>
            </div>

            <!-- Phase 3 -->
            <div class="cs-phase-item cs-phase-item--highlight">
                <div class="cs-phase-header">
                    <div class="nx-step__number">3</div>
                    <div>
                        <span class="cs-phase-label">Monat 5–9</span>
                        <h3 class="cs-phase-title">Performance-Skalierung: Qualifizierung + Übergabe</h3>
                    </div>
                </div>
                <div class="cs-phase-body">
                    <p class="nx-card__text">
                        Performance-Ads wurden auf das vorbereitete Fundament skaliert.
                        Qualifizierungs-Flows nach Branche, Bedarf und Projektgröße
                        segmentierten Anfragen. Automations- und CRM-Anbindung kamen erst
                        nach sauberer Daten-, Prozess- und Consent-Klärung dazu.
                    </p>
                    <ul class="cs-phase-outcomes">
                        <li>✓ <?php echo esc_html( $e3_lead_count ); ?> Anfragen im 5-Monats-Skalierungsfenster</li>
                        <li>✓ Vom Lead-Einkauf zur eigenen Pipeline</li>
                        <li>✓ <?php echo esc_html( $e3_cpl_reduction ); ?> Cost per Lead vs. Einkaufsleads</li>
                        <li>✓ <?php echo esc_html( $e3_sales_conversion ); ?> Sales-Conversion-Rate</li>
                        <li>✓ Segmentierte, vorqualifizierte Übergabe an Vertrieb</li>
                        <li>✓ Automations- und CRM-Anbindung nach sauberer Consent-Klärung</li>
                    </ul>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- ========================================
     SECTION 6: WAS WIR NICHT GEMACHT HABEN
     ======================================== -->
<section class="nx-section" id="nicht">
    <div class="nx-container">

        <div class="nx-section-header">
            <span class="nx-badge nx-badge--ghost">Ehrliche Einordnung</span>
            <h2 class="nx-headline-section" style="margin-top:1rem;">
                Was wir <em style="color:var(--nx-gold);font-style:normal;">nicht</em> gemacht haben
            </h2>
            <p class="nx-subheadline" style="margin:1rem auto 0;">
                Was andere Agenturen oft verkaufen&nbsp;— und warum wir es weggelassen haben.
            </p>
        </div>

        <div class="nx-grid nx-grid-2" style="margin-top:3rem;gap:1.5rem;">

            <div class="cs-not-item">
                <span class="cs-not-x">✕</span>
                <div>
                    <strong>Kein kompletter Relaunch</strong>
                    <p>Bestehende Domain-Autorität und Rankings schützen. Gezielter Ausbau statt Reset.</p>
                </div>
            </div>

            <div class="cs-not-item">
                <span class="cs-not-x">✕</span>
                <div>
                    <strong>Kein Ads-only-Ansatz</strong>
                    <p>Paid Ads ergänzen das System — ersetzen es nicht. Owned Assets reduzieren die Abhängigkeit von laufendem Budgeteinsatz.</p>
                </div>
            </div>

            <div class="cs-not-item">
                <span class="cs-not-x">✕</span>
                <div>
                    <strong>Kein CMS-Wechsel</strong>
                    <p>WordPress weiterentwickelt statt migriert. Keine Unterbrechung, keine Lernkurve für das Team.</p>
                </div>
            </div>

            <div class="cs-not-item">
                <span class="cs-not-x">✕</span>
                <div>
                    <strong>Volle Ownership</strong>
                    <p>Code, Inhalte, Zugänge und Setups bleiben beim Kunden. Laufende Zusammenarbeit bedeutet Weiterentwicklung, nicht Abhängigkeit.</p>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- ========================================
     SECTION 7: ERGEBNIS
     ======================================== -->
<section class="nx-section cs-section-results" id="ergebnis">
    <div class="nx-container">

        <div class="nx-section-header">
            <span class="nx-badge nx-badge--gold">Die Zahlen</span>
            <h2 class="nx-headline-section" style="margin-top:1rem;">
                Ergebnis nach <?php echo esc_html( $e3_timeframe_dative ); ?>: eigene Pipeline statt Lead-Einkauf
            </h2>
            <p class="nx-subheadline" style="margin:1rem auto 0;">
                Synergie aus technischem Setup und Performance-Skalierung:
                weniger Abhängigkeit, bessere Lead-Qualität, effizientere Vertriebsprozesse.
            </p>
        </div>

        <!-- Big KPI Grid -->
        <div class="cs-results-grid" style="margin-top:3rem;">

            <div class="cs-result-card cs-result-card--primary">
                <span class="cs-result-value"><?php echo esc_html( $e3_lead_count ); ?></span>
                <span class="cs-result-label">Qualifizierte Anfragen</span>
                <span class="cs-result-note">eigene Pipeline statt externer Zukauf</span>
            </div>

            <div class="cs-result-card">
                <span class="cs-result-value"><?php echo esc_html( $e3_cpl_reduction ); ?></span>
                <span class="cs-result-label">Cost per Lead</span>
                <span class="cs-result-note">von ø <?php echo esc_html( $e3_cpl_before ); ?> auf <?php echo esc_html( $e3_cpl_after ); ?></span>
            </div>

            <div class="cs-result-card">
                <span class="cs-result-value"><?php echo esc_html( $e3_sales_conversion ); ?></span>
                <span class="cs-result-label">Sales-Conversion-Rate</span>
                <span class="cs-result-note">Lead → Abschluss</span>
            </div>

            <div class="cs-result-card">
                <span class="cs-result-value">20.000&nbsp;€</span>
                <span class="cs-result-label">Gesamtbudget</span>
                <span class="cs-result-note">für Setup, Skalierung und Automationslogik</span>
            </div>

            <div class="cs-result-card">
                <span class="cs-result-value">Eigene Pipeline</span>
                <span class="cs-result-label">Eigene Pipeline</span>
                <span class="cs-result-note">deutlich weniger Abhängigkeit von Drittanbietern</span>
            </div>

            <div class="cs-result-card">
                <span class="cs-result-value">Saubere Übergabe</span>
                <span class="cs-result-label">Qualifizierte Lead-Übergabe</span>
                <span class="cs-result-note">erst nach geklärtem Daten- und Prozess-Setup</span>
            </div>

            <div class="cs-result-card">
                <span class="cs-result-value">98</span>
                <span class="cs-result-label">PageSpeed Mobile</span>
                <span class="cs-result-note">Ausgangswert: 34</span>
            </div>

            <div class="cs-result-card">
                <span class="cs-result-value">Quality Score ↑</span>
                <span class="cs-result-label">Ads-Effizienz</span>
                <span class="cs-result-note">höhere Relevanz, geringerer CPC-Druck</span>
            </div>

            <div class="cs-result-card">
                <span class="cs-result-value">Premium-Fit</span>
                <span class="cs-result-label">Marktpositionierung</span>
                <span class="cs-result-note">Viessmann-Partnerschaft + starkes Ausführungsteam</span>
            </div>

        </div>

        <article class="cs-budget-proof">
            <p class="cs-budget-proof__eyebrow">Strategischer Kern</p>
            <h3 class="cs-budget-proof__title">
                Technik zuerst. Skalierung danach.
            </h3>
            <p class="cs-budget-proof__intro">
                Die Reihenfolge war entscheidend: erst technische Stabilität und
                Basis-Content, dann Performance-Skalierung mit sauberer Segmentierung
                und Automation.
            </p>
            <ul class="cs-budget-proof__list">
                <li>
                    <strong>Technik als Umsatzhebel:</strong> Technical SEO verbesserte
                    Tracking-Integrität, Ladezeit und mobile Nutzbarkeit&nbsp;— die Grundlage
                    für präzise Kampagnensteuerung.
                </li>
                <li>
                    <strong>Ads-Effizienz:</strong> Höhere Landingpage-Qualität und Relevanz
                    stärkten den Quality Score, reduzierten CPC-Druck und erhöhten die
                    Wahrscheinlichkeit qualifizierter Abschlüsse.
                </li>
                <li>
                    <strong>Vertriebseffekt:</strong> Leads werden heute segmentiert,
                    vorqualifiziert und strukturiert an den Vertrieb übergeben&nbsp;—
                    mit <?php echo esc_html( $e3_sales_conversion ); ?> Sales-Conversion-Rate im Closing.
                </li>
            </ul>
        </article>

    </div>
</section>

<!-- ========================================
     SECTION 8: FAQ
     ======================================== -->
<section class="nx-section" id="faq">
    <div class="nx-container">

        <div class="nx-section-header">
            <h2 class="nx-headline-section">Häufige Fragen</h2>
        </div>

        <div class="nx-faq" style="margin-top:2rem;">

            <details class="nx-faq__item">
                <summary>Ist das auf unser Unternehmen übertragbar?</summary>
                <div class="nx-faq__content">
                    Die Ergebnisse entstanden in einem Setup für KMU + B2C mit
                    klarer Leistungsfähigkeit und belastbaren internen Prozessen.
                    Besonders relevant wird das Modell ab etwa &gt;5 Mio.&nbsp;€ Jahresumsatz.
                    Ob ähnliche Hebel bei Ihnen vorhanden sind, zeigt die Anfrage-System-Analyse
                    klar priorisiert und ohne Pitch.
                </div>
            </details>

            <details class="nx-faq__item">
                <summary>Warum wirkt diese Case Study trotz starkem Ads-Fokus nicht wie "Ads-only"?</summary>
                <div class="nx-faq__content">
                    Weil Ads hier nicht isoliert eingesetzt wurden. Erst durch Technical SEO,
                    Basis-Content und sauberes Tracking wurde Kampagnenbudget effizient und
                    langfristig skalierbar. Das reduziert Abhängigkeit von kurzfristigem Spend.
                </div>
            </details>

            <details class="nx-faq__item">
                <summary>Wie lange dauert es bis zu ersten Leads?</summary>
                <div class="nx-faq__content">
                    Das Fundament war in 4–6 Wochen stabil. Erste qualifizierte Inbound-Leads
                    kamen bei E3 ab Woche 14. Das Hauptwachstum (<?php echo esc_html( $e3_lead_count ); ?> Anfragen) entstand im
                    anschließenden 5-Monats-Skalierungsfenster.
                </div>
            </details>

            <details class="nx-faq__item">
                <summary>Warum zuerst Technical SEO, wenn der Fokus auf Ads lag?</summary>
                <div class="nx-faq__content">
                    Weil Ads ohne stabiles technisches Fundament teuer werden.
                    Tracking-Integrität, Ladezeit und Mobile-UX verbessern den Quality Score,
                    senken die Klickpreise und erhöhen die Conversion-Qualität der Landingpages.
                </div>
            </details>

            <details class="nx-faq__item">
                <summary>Was kostet das WordPress Growth Operating System?</summary>
                <div class="nx-faq__content">
                    Das WGOS arbeitet mit planbarer Growth-Kapazität über Credits: Sie buchen ein Kontingent,
                    wir setzen Module nach Priorität um. Code, Inhalte, Zugänge und Setups bleiben bei Ihnen.
                    Details und Pakete finden Sie auf der
                    <a href="<?php echo esc_url( $wgos_url ); ?>">WGOS-Seite</a>.
                </div>
            </details>

            <details class="nx-faq__item">
                <summary>Bleibt alles bei mir, wenn wir aufhören zusammenzuarbeiten?</summary>
                <div class="nx-faq__content">
                    Ja, vollständig. Code, Content, Tracking-Setup, Zugänge&nbsp;— alles gehört
                    Ihnen. Das ist kein Versprechen, das ist die technische Realität
                    von WordPress und Open Source.
                </div>
            </details>

            <details class="nx-faq__item">
                <summary>Warum wird Viessmann als Partner in der Case Study erwähnt?</summary>
                <div class="nx-faq__content">
                    Weil die Qualität der Ausführung direkt auf Lead- und Sales-Qualität wirkt.
                    Die Viessmann-Partnerschaft war ein zentraler Vertrauens- und
                    Positionierungsfaktor im gesamten Funnel.
                </div>
            </details>

        </div>

    </div>
</section>

<!-- ========================================
     SECTION 9: NEXT STEP CTA
     ======================================== -->
<section class="nx-section cs-section-cta" id="next-step">
    <div class="nx-container">

        <div class="nx-cta-box">
            <span class="nx-badge nx-badge--gold" style="margin-bottom:1.5rem;display:inline-block;">Nächster Schritt</span>
            <h2 style="font-size:clamp(1.8rem,3.5vw,2.8rem);margin-bottom:1rem;">
                Welche Hebel liegen bei Ihnen brach?
            </h2>
            <p style="color:var(--nx-text-muted);max-width:560px;margin:0 auto 2rem;line-height:1.6;">
                In der Anfrage-System-Analyse prüfen wir, ob Ihr Markt, Ihre Website
                und Ihr Anfrageprozess genug Substanz für ein eigenes System haben.
            </p>

            <div class="cs-cta-buttons">
                <a href="<?php echo esc_url( $analysis_url ); ?>"
                   class="nx-btn nx-btn--primary"
                   data-track-action="cta_case_nextstep_analysis"
                   data-track-category="lead_gen">
                    Analyse starten
                </a>
            </div>

            <p style="font-size:0.75rem;color:#555;margin-top:1rem;">
                Ergebnisse hängen von Ausgangslage ab&nbsp;— die Analyse zeigt den nächsten sinnvollen Schritt.
            </p>

            <!-- 3 Internal Links -->
            <div class="cs-internal-links">
                <a href="<?php echo esc_url( $wgos_url ); ?>" class="cs-internal-link">
                    WGOS: Die Systemlogik dahinter →
                </a>
                <a href="<?php echo esc_url( $demo_url ); ?>" class="cs-internal-link">
                    EnergieFahrplan-Demo ansehen →
                </a>
                <a href="<?php echo esc_url( $local_wp_url ); ?>" class="cs-internal-link">
                    WordPress Agentur Hannover →
                </a>
                <a href="<?php echo esc_url( $energy_url ); ?>" class="cs-internal-link">
                    Für Solar- und Wärmepumpen-Anbieter →
                </a>
            </div>

        </div>

    </div>
</section>

</div><!-- .cs-case-wrapper -->

<?php get_footer(); ?>
