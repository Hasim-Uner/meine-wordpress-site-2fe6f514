<?php
/**
 * Template Name: WordPress Agentur Hannover
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact_url    = home_url( '/kontakt/' );
$e3_url         = home_url( '/e3-new-energy/' );
$marktcheck_url = home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );

get_header();
?>

    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 2rem;
        }

        :focus-visible {
            outline: 3px solid var(--primary);
            outline-offset: 3px;
            border-radius: 4px;
        }

        :root {
            --primary: #D97757;
            --primary-dark: #C05D3F;
            --primary-light: #F4D4C8;
            --secondary: #29261B;
            --secondary-light: #3D3A2E;
            --background: #F6F4EF;
            --surface: #FFFFFF;
            --text-primary: #1F1D14;
            --text-secondary: #3E3B30;
            --text-tertiary: #6B6856;
            --border: #DCD7C8;
            --accent-green: #1F8A5B;
            --accent-blue: #2A6FDB;
            --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;
            --font-display: Georgia, "Times New Roman", serif;
            --max-width: 1200px;
            --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-smooth: 400ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: var(--font-sans);
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            color-scheme: light;
        }

        /* ─── Hero ─── */
        .hero {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
            color: var(--background);
            padding: 6rem 1.5rem;
            min-height: 85vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 50%, var(--primary) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, var(--accent-blue) 0%, transparent 50%);
            opacity: 0.08;
            pointer-events: none;
        }

        .hero-content {
            max-width: var(--max-width);
            width: 100%;
            position: relative;
            z-index: 1;
        }

        .hero-eyebrow {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--primary-light);
            font-weight: 600;
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: fadeInUp 0.6s ease 0s forwards;
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            line-height: 1.05;
            margin-bottom: 2rem;
            font-weight: 600;
            letter-spacing: -0.015em;
            color: #FFFFFF;
            opacity: 0;
            animation: fadeInUp 0.6s ease 0.1s forwards;
        }

        .hero-subtitle {
            font-size: clamp(1.125rem, 2vw, 1.5rem);
            line-height: 1.5;
            max-width: 800px;
            color: var(--primary-light);
            margin-bottom: 3rem;
            opacity: 0;
            animation: fadeInUp 0.6s ease 0.2s forwards;
        }

        .hero-ctas {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            opacity: 0;
            animation: fadeInUp 0.6s ease 0.3s forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ─── Buttons ─── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all var(--transition-base);
            cursor: pointer;
            border: none;
            font-family: inherit;
            line-height: 1;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(217, 119, 87, 0.3);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(217, 119, 87, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: var(--background);
            border: 2px solid var(--primary-light);
        }

        .btn-secondary:hover {
            background: var(--primary-light);
            color: var(--secondary);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        /* ─── Stats ─── */
        .stats {
            background: var(--surface);
            padding: 3rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .stats-grid {
            max-width: var(--max-width);
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 3rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-family: var(--font-display);
            font-size: clamp(2.25rem, 4.5vw, 3.25rem);
            color: var(--primary);
            font-weight: 700;
            letter-spacing: -0.015em;
            margin-bottom: 0.5rem;
            line-height: 1.05;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
        }

        /* ─── Sections ─── */
        .section {
            padding: 6rem 1.5rem;
        }

        .section-inner {
            max-width: var(--max-width);
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-eyebrow {
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1.15;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
            font-weight: 700;
            letter-spacing: -0.015em;
        }

        .section-subtitle {
            font-size: 1.1875rem;
            color: var(--text-secondary);
            max-width: 720px;
            margin: 0 auto;
            line-height: 1.65;
            font-weight: 450;
        }

        /* ─── Accordion (Asset Library + FAQ) ─── */
        .accordion {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .acc-item {
            background: var(--surface);
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 1px 2px rgba(31, 29, 20, 0.04);
            transition: border-color var(--transition-smooth), box-shadow var(--transition-smooth);
        }

        .acc-item:hover {
            border-color: var(--primary-light);
            box-shadow: 0 12px 32px rgba(217, 119, 87, 0.12), 0 2px 4px rgba(31, 29, 20, 0.06);
        }

        .acc-trigger {
            width: 100%;
            padding: 2rem 3rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            background: none;
            border: none;
            text-align: left;
            font-family: inherit;
            transition: background var(--transition-base);
            user-select: none;
        }

        .acc-trigger:hover {
            background: var(--background);
        }

        .acc-trigger-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex: 1;
            min-width: 0;
        }

        .acc-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            transition: transform var(--transition-base);
        }

        .acc-item:hover .acc-icon {
            transform: scale(1.1);
        }

        .acc-info { flex: 1; min-width: 0; }

        .acc-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .acc-desc {
            font-size: 0.9375rem;
            color: var(--text-secondary);
        }

        .acc-meta {
            display: flex;
            align-items: center;
            gap: 2rem;
            flex-shrink: 0;
        }

        .acc-count {
            font-size: 0.875rem;
            color: var(--text-tertiary);
            font-weight: 500;
            white-space: nowrap;
        }

        .acc-chevron {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--background);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all var(--transition-base);
        }

        .acc-item.is-open .acc-chevron {
            background: var(--primary);
            color: white;
            transform: rotate(180deg);
        }

        .acc-body {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows var(--transition-smooth);
        }

        .acc-item.is-open .acc-body {
            grid-template-rows: 1fr;
        }

        .acc-body-inner {
            overflow: hidden;
        }

        /* ─── Asset Cards Grid ─── */
        .assets-grid {
            padding: 0 3rem 3rem;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .asset-card {
            background: var(--background);
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid var(--border);
            transition: all var(--transition-base);
            cursor: default;
        }

        .asset-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(217, 119, 87, 0.12);
        }

        .asset-header {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .asset-icon {
            width: 36px;
            height: 36px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
            flex-shrink: 0;
            background: var(--surface);
        }

        .asset-title {
            font-size: 1.0625rem;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.35;
            flex: 1;
        }

        .asset-desc {
            font-size: 0.9375rem;
            color: var(--text-secondary);
            line-height: 1.55;
        }

        /* ─── FAQ Accordion ─── */
        .faq-list {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .faq-item {
            background: var(--background);
            border-radius: 0.75rem;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(31, 29, 20, 0.03);
            transition: border-color var(--transition-base), box-shadow var(--transition-base);
        }

        .faq-item:hover {
            border-color: var(--primary-light);
            box-shadow: 0 6px 20px rgba(217, 119, 87, 0.10), 0 2px 4px rgba(31, 29, 20, 0.05);
        }

        .faq-trigger {
            width: 100%;
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
            background: none;
            border: none;
            text-align: left;
            font-family: inherit;
            user-select: none;
        }

        .faq-question {
            font-size: 1.0625rem;
            font-weight: 600;
            color: var(--text-primary);
            flex: 1;
            line-height: 1.4;
        }

        .faq-chevron {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--surface);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all var(--transition-base);
        }

        .faq-item.is-open .faq-chevron {
            background: var(--primary);
            color: white;
            transform: rotate(180deg);
        }

        .faq-body {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows var(--transition-smooth);
        }

        .faq-item.is-open .faq-body {
            grid-template-rows: 1fr;
        }

        .faq-body-inner {
            overflow: hidden;
        }

        .faq-answer {
            padding: 0 1.5rem 1.5rem;
            color: var(--text-secondary);
            line-height: 1.7;
            font-size: 1rem;
        }

        /* ─── Proof ─── */
        .proof {
            background: var(--surface);
        }

        .proof-card {
            max-width: 900px;
            margin: 0 auto;
            background: var(--background);
            border-radius: 1.5rem;
            padding: 4rem;
            border: 1px solid var(--border);
            box-shadow: 0 1px 2px rgba(31, 29, 20, 0.04), 0 12px 40px rgba(31, 29, 20, 0.06);
        }

        .proof-card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }

        .proof-card-text {
            font-size: 1.125rem;
            color: var(--text-secondary);
            line-height: 1.7;
        }

        .proof-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
        }

        .proof-stat {
            text-align: center;
            padding: 1.5rem;
            background: var(--surface);
            border-radius: 0.75rem;
        }

        /* ─── Cards Grid (Spezialisierung, Projektprüfung) ─── */
        .cards-grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
        }

        .cards-grid-4 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }

        .info-card {
            background: var(--background);
            padding: 2.5rem;
            border-radius: 1rem;
            border: 1px solid var(--border);
            transition: all var(--transition-base);
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(41, 38, 27, 0.08);
        }

        .info-card-num {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .info-card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .info-card-text {
            color: var(--text-secondary);
            font-size: 0.9375rem;
            line-height: 1.6;
        }

        .signal-card {
            background: var(--surface);
            padding: 1.5rem;
            border-radius: 1rem;
            border: 1px solid var(--border);
            transition: all var(--transition-base);
        }

        .signal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(41, 38, 27, 0.06);
        }

        .signal-num {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .signal-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .signal-text {
            color: var(--text-secondary);
            font-size: 0.9375rem;
            line-height: 1.6;
        }

        /* ─── Abgrenzung ─── */
        .abgrenzung-list {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .abgrenzung-item {
            display: flex;
            gap: 1.5rem;
            padding: 1.5rem;
            background: var(--surface);
            border-radius: 0.75rem;
            border: 1px solid var(--border);
            align-items: flex-start;
        }

        .abgrenzung-x {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--text-tertiary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .abgrenzung-text {
            color: var(--text-secondary);
            margin: 0;
            line-height: 1.6;
        }

        /* ─── Standort ─── */
        .standort-box {
            max-width: 700px;
            margin: 0 auto;
            padding: 3rem;
            background: var(--surface);
            border-radius: 1rem;
            border: 1px solid var(--border);
            text-align: center;
            box-shadow: 0 1px 2px rgba(31, 29, 20, 0.04), 0 8px 24px rgba(31, 29, 20, 0.05);
        }

        .standort-text {
            font-size: 1rem;
            color: var(--text-secondary);
            line-height: 1.7;
        }

        /* ─── Fokusmarkt Energie ─── */
        .fokus-cta {
            text-align: center;
        }

        /* ─── CTA Section ─── */
        .cta-section {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
            color: var(--background);
            padding: 6rem 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 80% 20%, var(--primary) 0%, transparent 50%),
                radial-gradient(circle at 20% 80%, var(--accent-blue) 0%, transparent 50%);
            opacity: 0.06;
            pointer-events: none;
        }

        .cta-inner {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-title {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 3rem);
            margin-bottom: 1.5rem;
            line-height: 1.15;
            font-weight: 600;
            letter-spacing: -0.015em;
            color: #FFFFFF;
        }

        .cta-subtitle {
            font-size: 1.25rem;
            color: #F4E8DE;
            margin-bottom: 3rem;
            line-height: 1.6;
        }

        .cta-btns {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ─── Alt sections bg ─── */
        .bg-surface { background: var(--surface); }
        .bg-background { background: var(--background); }

        /* ─── Center CTA ─── */
        .center-cta {
            text-align: center;
            margin-top: 3rem;
        }

        /* ─── Mobile ─── */
        @media (max-width: 768px) {
            .hero {
                padding: 4rem 1rem;
                min-height: 70vh;
            }

            .section {
                padding: 3rem 1rem;
            }

            .hero-ctas, .cta-btns {
                flex-direction: column;
            }

            .btn { width: 100%; justify-content: center; }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }

            .acc-trigger {
                padding: 1.5rem 1rem;
            }

            .acc-trigger-left {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .acc-meta {
                width: 100%;
                justify-content: space-between;
            }

            .assets-grid {
                grid-template-columns: 1fr;
                padding: 0 1rem 1.5rem;
            }

            .proof-card {
                padding: 2rem 1.25rem;
            }

            .cards-grid-3, .cards-grid-4 {
                grid-template-columns: 1fr;
            }

            .standort-box {
                padding: 2rem 1.25rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .acc-icon { display: none; }
        }
    </style>

<!-- ═══════════════════════════════════════
     HERO
═══════════════════════════════════════ -->
<section class="hero">
    <div class="hero-content">
        <p class="hero-eyebrow">WordPress · SEO · Tracking · Conversion · Hannover</p>
        <h1 class="hero-title">WordPress-Wachstumssystem für anspruchsvolle B2B-Angebote.</h1>
        <p class="hero-subtitle">
            Aus Hannover für den DACH-Raum. Ich verbinde WordPress-Entwicklung mit SEO, Tracking und Conversion-Führung — damit die Website nachvollziehbar qualifizierte Anfragen erzeugt, statt nur sichtbar zu sein.
        </p>
        <div class="hero-ctas">
            <a href="#projekt-pruefen" class="btn btn-primary">
                Projekt prüfen
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <a href="#proof" class="btn btn-secondary">E3-Case ansehen</a>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════
     STATS
═══════════════════════════════════════ -->
<section class="stats">
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-value">150€ → 22€</div>
            <div class="stat-label">CPL gesenkt</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">1.750+</div>
            <div class="stat-label">Qualifizierte Anfragen in 9 Monaten</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">12%</div>
            <div class="stat-label">Abschlussquote</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">85%</div>
            <div class="stat-label">Niedrigere Kosten pro Anfrage</div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════
     WGOS METHODE
═══════════════════════════════════════ -->
<section class="section">
    <div class="section-inner">
        <div class="section-header">
            <p class="section-eyebrow">WGOS · Methode</p>
            <h2 class="section-title">WordPress, SEO, Tracking und CRO in der richtigen Reihenfolge.</h2>
            <p class="section-subtitle">
                WGOS ist die Methode hinter dem eigenen Anfrage-System. Nicht die Asset-Liste entscheidet, sondern die Frage, welcher Eingriff zuerst mehr kaufnahe Klarheit erzeugt.
            </p>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════
     ASSET BIBLIOTHEK
═══════════════════════════════════════ -->
<section class="section bg-background" style="padding-top: 0;">
    <div class="section-inner">
        <div class="section-header">
            <p class="section-eyebrow">Methodenbibliothek</p>
            <h2 class="section-title">Die Bausteine hinter der WGOS-Methode.</h2>
            <p class="section-subtitle">
                Diese Bausteine bilden die WGOS-Methode. Welche zuerst gebaut werden, entscheidet die Analyse — nicht der Katalog.
            </p>
        </div>

        <div class="accordion" id="asset-accordion">

            <!-- Strategie -->
            <div class="acc-item" data-acc="strategie">
                <button class="acc-trigger" aria-expanded="false" aria-controls="body-strategie">
                    <div class="acc-trigger-left">
                        <div class="acc-icon" style="background: rgba(217,119,87,0.1); color: #D97757;">🎯</div>
                        <div class="acc-info">
                            <div class="acc-title">Strategie</div>
                            <div class="acc-desc">Welche Seite trägt welche Anfrage — und welche nicht.</div>
                        </div>
                    </div>
                    <div class="acc-meta">
                        <span class="acc-count">5 Bausteine</span>
                        <div class="acc-chevron" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </button>
                <div class="acc-body" id="body-strategie" role="region">
                    <div class="acc-body-inner">
                        <div class="assets-grid">
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #D97757;">🔍</div>
                                    <div class="asset-title">System-Diagnose</div>
                                </div>
                                <p class="asset-desc">Die System-Diagnose zeigt, welche Bremsen Ihre WordPress-Seite zuerst lösen muss.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #D97757;">🎪</div>
                                    <div class="asset-title">Positionierungs-Check</div>
                                </div>
                                <p class="asset-desc">Der Positionierungs-Check prüft, ob Angebot, Zielgruppe und Nutzenversprechen auf der Website klar genug sind.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #D97757;">🗺️</div>
                                    <div class="asset-title">Seitenrollen-Mapping</div>
                                </div>
                                <p class="asset-desc">Seitenrollen-Mapping gibt jeder wichtigen Seite eine klare Aufgabe im Funnel.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #D97757;">📊</div>
                                    <div class="asset-title">Wettbewerbs-Analyse (Digital)</div>
                                </div>
                                <p class="asset-desc">Die digitale Wettbewerbs-Analyse zeigt, wie andere Anbieter Angebot, Sichtbarkeit und Conversion aufbauen.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #D97757;">🗓️</div>
                                    <div class="asset-title">Roadmap &amp; Priorisierung</div>
                                </div>
                                <p class="asset-desc">Roadmap &amp; Priorisierung bringt WGOS-Assets in eine Reihenfolge, die wirklich wirkt.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fundament -->
            <div class="acc-item" data-acc="fundament">
                <button class="acc-trigger" aria-expanded="false" aria-controls="body-fundament">
                    <div class="acc-trigger-left">
                        <div class="acc-icon" style="background: rgba(31,138,91,0.1); color: #1F8A5B;">⚡</div>
                        <div class="acc-info">
                            <div class="acc-title">Fundament</div>
                            <div class="acc-desc">Schnell, stabil, wartbar — ohne dass jedes Plugin-Update zur Krise wird.</div>
                        </div>
                    </div>
                    <div class="acc-meta">
                        <span class="acc-count">6 Bausteine</span>
                        <div class="acc-chevron" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </button>
                <div class="acc-body" id="body-fundament" role="region">
                    <div class="acc-body-inner">
                        <div class="assets-grid">
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">🏃</div>
                                    <div class="asset-title">CWV Speed Audit</div>
                                </div>
                                <p class="asset-desc">Der CWV Speed Audit zeigt, was Ihre WordPress-Seite langsam macht und wo der Hebel wirklich liegt.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">🚀</div>
                                    <div class="asset-title">CWV Optimierung</div>
                                </div>
                                <p class="asset-desc">CWV Optimierung beseitigt die Bremsen, die im Speed Audit sichtbar geworden sind.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">🖥️</div>
                                    <div class="asset-title">Server-Tuning</div>
                                </div>
                                <p class="asset-desc">Server-Tuning verbessert die technische Reaktionszeit dort, wo Theme- und Plugin-Fixes nicht ausreichen.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">🔒</div>
                                    <div class="asset-title">Security Hardening</div>
                                </div>
                                <p class="asset-desc">Security Hardening reduziert konkrete Angriffs- und Ausfallrisiken in Ihrem WordPress-Stack.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">🧩</div>
                                    <div class="asset-title">Plugin Audit &amp; Bereinigung</div>
                                </div>
                                <p class="asset-desc">Der Plugin Audit zeigt, welche Erweiterungen WordPress langsam, fragil oder unsicher machen.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">🔄</div>
                                    <div class="asset-title">WordPress Update-Management</div>
                                </div>
                                <p class="asset-desc">WordPress Update-Management sorgt dafür, dass Kern, Plugins und Theme nicht nur reaktiv gepflegt werden.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messbarkeit -->
            <div class="acc-item" data-acc="messbarkeit">
                <button class="acc-trigger" aria-expanded="false" aria-controls="body-messbarkeit">
                    <div class="acc-trigger-left">
                        <div class="acc-icon" style="background: rgba(42,111,219,0.1); color: #2A6FDB;">📈</div>
                        <div class="acc-info">
                            <div class="acc-title">Messbarkeit</div>
                            <div class="acc-desc">Sie wissen, welcher Kanal echte Projekte bringt — nicht nur Klicks.</div>
                        </div>
                    </div>
                    <div class="acc-meta">
                        <span class="acc-count">6 Bausteine</span>
                        <div class="acc-chevron" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </button>
                <div class="acc-body" id="body-messbarkeit" role="region">
                    <div class="acc-body-inner">
                        <div class="assets-grid">
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #2A6FDB;">🔬</div>
                                    <div class="asset-title">Tracking Audit</div>
                                </div>
                                <p class="asset-desc">Der Tracking Audit zeigt, ob Ihre Datenbasis für Entscheidungen überhaupt belastbar ist.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #2A6FDB;">📋</div>
                                    <div class="asset-title">GA4 Event Blueprint</div>
                                </div>
                                <p class="asset-desc">Der GA4 Event Blueprint definiert, welche Signale Ihre Website wirklich messen sollte.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #2A6FDB;">✅</div>
                                    <div class="asset-title">Consent Mode v2</div>
                                </div>
                                <p class="asset-desc">Consent Mode v2 sorgt dafür, dass Datenschutz und Datenqualität sauber zusammenarbeiten.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #2A6FDB;">🛰️</div>
                                    <div class="asset-title">Server-Side Tracking (sGTM &amp; Matomo)</div>
                                </div>
                                <p class="asset-desc">Server-Side Tracking macht Messung robuster, kontrollierbarer und weniger browserabhängig.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #2A6FDB;">📊</div>
                                    <div class="asset-title">KPI-Dashboard Setup</div>
                                </div>
                                <p class="asset-desc">Das KPI-Dashboard Setup übersetzt Rohdaten in eine Management-Sicht mit Entscheidungswert.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #2A6FDB;">🏷️</div>
                                    <div class="asset-title">UTM-Framework &amp; Attribution</div>
                                </div>
                                <p class="asset-desc">Ein UTM-Framework sorgt dafür, dass Kampagnenquellen sauber benannt und vergleichbar bleiben.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sichtbarkeit -->
            <div class="acc-item" data-acc="sichtbarkeit">
                <button class="acc-trigger" aria-expanded="false" aria-controls="body-sichtbarkeit">
                    <div class="acc-trigger-left">
                        <div class="acc-icon" style="background: rgba(192,93,63,0.1); color: #C05D3F;">🔎</div>
                        <div class="acc-info">
                            <div class="acc-title">Sichtbarkeit</div>
                            <div class="acc-desc">Die Suchanfragen, die kaufnahe Besucher liefern.</div>
                        </div>
                    </div>
                    <div class="acc-meta">
                        <span class="acc-count">8 Bausteine</span>
                        <div class="acc-chevron" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </button>
                <div class="acc-body" id="body-sichtbarkeit" role="region">
                    <div class="acc-body-inner">
                        <div class="assets-grid">
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #C05D3F;">🔧</div>
                                    <div class="asset-title">Technical SEO Audit</div>
                                </div>
                                <p class="asset-desc">Der Technical SEO Audit zeigt, welche technischen SEO-Probleme Rankings und Indexierung bremsen.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #C05D3F;">🗝️</div>
                                    <div class="asset-title">Keyword-Strategie &amp; Content-Map</div>
                                </div>
                                <p class="asset-desc">Keyword-Strategie &amp; Content-Map ordnet Nachfrage in Themen, Seiten und Suchintentionen.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #C05D3F;">🏛️</div>
                                    <div class="asset-title">Pillar Page</div>
                                </div>
                                <p class="asset-desc">Eine Pillar Page verdichtet ein strategisches Thema in eine such- und conversionstarke Hauptseite.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #C05D3F;">🌐</div>
                                    <div class="asset-title">Content Hub Aufbau</div>
                                </div>
                                <p class="asset-desc">Content Hub Aufbau verbindet Pillar Page, Cluster-Inhalte und interne Verlinkung zu einem Themen-System.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #C05D3F;">📝</div>
                                    <div class="asset-title">On-Page SEO Optimierung</div>
                                </div>
                                <p class="asset-desc">On-Page SEO Optimierung schärft Seiteninhalt, Snippets und Struktur für Suchintention und Klickqualität.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #C05D3F;">🔗</div>
                                    <div class="asset-title">Interne Verlinkung &amp; Seitenarchitektur</div>
                                </div>
                                <p class="asset-desc">Interne Verlinkung &amp; Seitenarchitektur machen Themenpfade und Prioritäten für Nutzer wie Suchmaschinen klarer.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #C05D3F;">🏷️</div>
                                    <div class="asset-title">Schema Markup &amp; Strukturierte Daten</div>
                                </div>
                                <p class="asset-desc">Schema Markup &amp; strukturierte Daten sorgen dafür, dass Suchmaschinen Inhalte und Angebote sauberer einordnen können.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #C05D3F;">📍</div>
                                    <div class="asset-title">Local SEO Setup</div>
                                </div>
                                <p class="asset-desc">Local SEO Setup macht regionale Relevanz für Suchmaschinen und Interessenten klarer sichtbar.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conversion -->
            <div class="acc-item" data-acc="conversion">
                <button class="acc-trigger" aria-expanded="false" aria-controls="body-conversion">
                    <div class="acc-trigger-left">
                        <div class="acc-icon" style="background: rgba(139,71,137,0.1); color: #8B4789;">💡</div>
                        <div class="acc-info">
                            <div class="acc-title">Conversion</div>
                            <div class="acc-desc">Was auf der Seite passieren muss, damit der Besucher jetzt handelt.</div>
                        </div>
                    </div>
                    <div class="acc-meta">
                        <span class="acc-count">6 Bausteine</span>
                        <div class="acc-chevron" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </button>
                <div class="acc-body" id="body-conversion" role="region">
                    <div class="acc-body-inner">
                        <div class="assets-grid">
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #8B4789;">🎨</div>
                                    <div class="asset-title">Landing Page (Neu)</div>
                                </div>
                                <p class="asset-desc">Landing Page (Neu) baut eine Seite von Grund auf für klares Message-Match und Conversion auf.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #8B4789;">✨</div>
                                    <div class="asset-title">Landing Page Optimierung</div>
                                </div>
                                <p class="asset-desc">Landing Page Optimierung verbessert bestehende Seiten dort, wo Verständnis, Vertrauen oder CTA bremsen.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #8B4789;">🎯</div>
                                    <div class="asset-title">CTA &amp; Formular-Optimierung</div>
                                </div>
                                <p class="asset-desc">CTA &amp; Formular-Optimierung reduziert Reibung zwischen Interesse und Anfrage.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #8B4789;">🏗️</div>
                                    <div class="asset-title">Angebotsseiten-Architektur</div>
                                </div>
                                <p class="asset-desc">Angebotsseiten-Architektur ordnet Leistungsseiten so, dass Angebot, Proof und nächste Schritte konsistent zusammenspielen.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #8B4789;">⭐</div>
                                    <div class="asset-title">Social Proof &amp; Trust-Elemente</div>
                                </div>
                                <p class="asset-desc">Social Proof &amp; Trust-Elemente zeigen glaubwürdig, warum Besucher Ihrer Website vertrauen können.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #8B4789;">🧲</div>
                                    <div class="asset-title">Lead-Magnet Konzeption</div>
                                </div>
                                <p class="asset-desc">Lead-Magnet Konzeption entwickelt einen Einstiegs-Asset, das für die richtige Zielgruppe echten Gegenwert liefert.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weiterentwicklung -->
            <div class="acc-item" data-acc="weiterentwicklung">
                <button class="acc-trigger" aria-expanded="false" aria-controls="body-weiterentwicklung">
                    <div class="acc-trigger-left">
                        <div class="acc-icon" style="background: rgba(31,138,91,0.1); color: #1F8A5B;">🚀</div>
                        <div class="acc-info">
                            <div class="acc-title">Weiterentwicklung</div>
                            <div class="acc-desc">Welche Änderung erzeugt als Nächstes Wirkung — datenbasiert, nicht aus dem Bauch.</div>
                        </div>
                    </div>
                    <div class="acc-meta">
                        <span class="acc-count">8 Bausteine</span>
                        <div class="acc-chevron" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </button>
                <div class="acc-body" id="body-weiterentwicklung" role="region">
                    <div class="acc-body-inner">
                        <div class="assets-grid">
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">📅</div>
                                    <div class="asset-title">Monthly Performance Review</div>
                                </div>
                                <p class="asset-desc">Der Monthly Performance Review übersetzt Daten und Beobachtungen jeden Monat in klare Entscheidungen.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">🗺️</div>
                                    <div class="asset-title">Quarterly Roadmap Update</div>
                                </div>
                                <p class="asset-desc">Das Quarterly Roadmap Update zieht alle Erkenntnisse eines Quartals in eine neue Umsetzungsreihenfolge.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">📊</div>
                                    <div class="asset-title">Reporting Dashboard</div>
                                </div>
                                <p class="asset-desc">Das Reporting Dashboard verbindet operative KPI-Sicht mit Stakeholder- und Verlaufsperspektive.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">🧪</div>
                                    <div class="asset-title">Conversion-Hypothesen &amp; Testing</div>
                                </div>
                                <p class="asset-desc">Conversion-Hypothesen &amp; Testing machen aus Beobachtungen prüfbare Verbesserungsansätze.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">🤖</div>
                                    <div class="asset-title">KI-Assistent / Chatbot (DSGVO-konform)</div>
                                </div>
                                <p class="asset-desc">Ein KI-Assistent, der Besuchern auf Basis Ihrer eigenen Inhalte antwortet – DSGVO-konform, auf Ihrer Infrastruktur.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">🎯</div>
                                    <div class="asset-title">KI-gestützte Lead-Qualifizierung</div>
                                </div>
                                <p class="asset-desc">Formulare und Anfrageprozesse, die mit KI-Unterstützung Leads bewerten und vorsortieren – bevor der Vertrieb übernimmt.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">🔎</div>
                                    <div class="asset-title">RAG-Wissenssuche</div>
                                </div>
                                <p class="asset-desc">Eine KI-gestützte Suche, die Antworten aus Ihren eigenen Dokumenten, Blog-Beiträgen und FAQs findet.</p>
                            </div>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon" style="color: #1F8A5B;">⚙️</div>
                                    <div class="asset-title">LLM-Workflow-Automatisierung (n8n)</div>
                                </div>
                                <p class="asset-desc">Automatisierte Workflows mit KI-Schritten – Zusammenfassungen, Klassifikation, Routing – server-seitig auf n8n, DSGVO-konform.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /accordion -->
    </div>
</section>

<!-- ═══════════════════════════════════════
     PROOF
═══════════════════════════════════════ -->
<section class="section proof" id="proof">
    <div class="section-inner">
        <div class="section-header">
            <p class="section-eyebrow">Proof · E3 New Energy</p>
            <h2 class="section-title">Was passiert, wenn WordPress, Tracking und Anfrageführung zusammenarbeiten.</h2>
        </div>

        <div class="proof-card">
            <h3 class="proof-card-title">Der E3-Case ist Referenz, keine pauschale Übertragbarkeitsgarantie.</h3>
            <p class="proof-card-text">
                Er zeigt, warum Reihenfolge, Datenqualität und eigene Anfragepfade wichtiger sind als ein weiterer Relaunch.
            </p>
            <div class="proof-stats">
                <div class="proof-stat">
                    <div class="stat-value" style="font-size: 2rem;">150€ → 22€</div>
                    <div class="stat-label">CPL gesenkt</div>
                </div>
                <div class="proof-stat">
                    <div class="stat-value" style="font-size: 2rem;">1.750+</div>
                    <div class="stat-label">Qualifizierte Anfragen</div>
                </div>
                <div class="proof-stat">
                    <div class="stat-value" style="font-size: 2rem;">12%</div>
                    <div class="stat-label">Abschlussquote</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════
     SPEZIALISIERUNG
═══════════════════════════════════════ -->
<section class="section bg-surface">
    <div class="section-inner">
        <div class="section-header">
            <p class="section-eyebrow">Spezialisierung</p>
            <h2 class="section-title">Kein lokaler Allrounder. Ein Spezialist mit Hannover-Anker.</h2>
            <p class="section-subtitle">
                Diese Seite ist der SEO-Anker für WordPress Agentur Hannover. WordPress SEO Hannover bleibt als Standortsignal sichtbar; die Zielgruppe wird fachlich bestimmt: komplexe B2B-Angebote, klare Messbarkeit und ein eigenes Anfrage-System.
            </p>
        </div>

        <div class="cards-grid-3">
            <div class="info-card">
                <div class="info-card-num" style="background: rgba(217,119,87,0.1); color: #D97757;">01</div>
                <h3 class="info-card-title">Was</h3>
                <p class="info-card-text">
                    WordPress-Systeme, die nachvollziehbar qualifizierte Anfragen erzeugen: Angebotsseiten, technisches SEO, Tracking, GA4, Server-Side Tracking und Conversion Optimierung — WordPress als ein System.
                </p>
            </div>
            <div class="info-card">
                <div class="info-card-num" style="background: rgba(42,111,219,0.1); color: #2A6FDB;">02</div>
                <h3 class="info-card-title">Für wen</h3>
                <p class="info-card-text">
                    Anspruchsvolle B2B-Unternehmen mit erklärungsbedürftigem Angebot. Der Fokusmarkt ist Solar, Wärmepumpe und Speicher; die Arbeitsweise ist nicht auf Hannover begrenzt.
                </p>
            </div>
            <div class="info-card">
                <div class="info-card-num" style="background: rgba(31,138,91,0.1); color: #1F8A5B;">03</div>
                <h3 class="info-card-title">Womit</h3>
                <p class="info-card-text">
                    Mit der WGOS-Methode, validiert am Referenzkontext E3 New Energy: CPL von 150 € auf 22 € gesenkt, 1.750+ qualifizierte Anfragen in 9 Monaten und 12 % Abschlussquote.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════
     PROJEKTPRÜFUNG
═══════════════════════════════════════ -->
<section class="section bg-background" id="projekt-pruefen">
    <div class="section-inner">
        <div class="section-header">
            <p class="section-eyebrow">Projektprüfung</p>
            <h2 class="section-title">Vor Umsetzung prüfe ich vier Kauf-Signale.</h2>
            <p class="section-subtitle">
                Ich prüfe nicht, ob eine neue Website schöner wäre. Ich prüfe, ob Angebot, Nachfrage, Daten und Anfragepfad als System zusammenpassen.
            </p>
        </div>

        <div class="cards-grid-4">
            <div class="signal-card">
                <div class="signal-num" style="color: #D97757;">01</div>
                <h3 class="signal-title">Angebot</h3>
                <p class="signal-text">Ist der Nutzen so klar, dass ein kaufnaher Besucher den nächsten Schritt versteht?</p>
            </div>
            <div class="signal-card">
                <div class="signal-num" style="color: #C05D3F;">02</div>
                <h3 class="signal-title">Nachfrage</h3>
                <p class="signal-text">Welche Suchanfragen, Kampagnen oder Partnerpfade bringen Besucher mit echter Projektabsicht?</p>
            </div>
            <div class="signal-card">
                <div class="signal-num" style="color: #2A6FDB;">03</div>
                <h3 class="signal-title">Datenlage</h3>
                <p class="signal-text">Sind GA4, Consent, Tracking und Server-Side Tracking belastbar genug für Entscheidungen?</p>
            </div>
            <div class="signal-card">
                <div class="signal-num" style="color: #8B4789;">04</div>
                <h3 class="signal-title">Anfragepfad</h3>
                <p class="signal-text">Welche Information braucht der Besucher jetzt, damit aus Interesse ein qualifizierter Erstkontakt wird?</p>
            </div>
        </div>

        <div class="center-cta">
            <a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn-primary">Projekt prüfen</a>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════
     FAQ
═══════════════════════════════════════ -->
<section class="section bg-surface">
    <div class="section-inner">
        <div class="section-header">
            <p class="section-eyebrow">FAQ</p>
            <h2 class="section-title">Häufige Fragen</h2>
        </div>

        <div class="faq-list" id="faq-accordion">

            <div class="faq-item">
                <button class="faq-trigger" aria-expanded="false">
                    <span class="faq-question">Welche WordPress Agentur in Hannover passt für anspruchsvolle B2B-Angebote?</span>
                    <div class="faq-chevron" aria-hidden="true">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </button>
                <div class="faq-body">
                    <div class="faq-body-inner">
                        <p class="faq-answer">Eine passende WordPress Agentur Hannover verbindet WordPress-Entwicklung, SEO, Tracking, GA4, Server-Side Tracking und Conversion-Führung. Genau darauf ist diese Seite ausgerichtet: WordPress als Nachfrage-System statt isolierter Einzelleistungen.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-trigger" aria-expanded="false">
                    <span class="faq-question">Arbeiten Sie nur mit Unternehmen aus Hannover?</span>
                    <div class="faq-chevron" aria-hidden="true">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </button>
                <div class="faq-body">
                    <div class="faq-body-inner">
                        <p class="faq-answer">Nein. Der Standort ist Pattensen bei Hannover, persönliche Termine sind in der Region möglich. Die Umsetzung ist auf den DACH-Raum ausgelegt; Hannover ist Standortanker, keine Zielgruppen-Grenze.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-trigger" aria-expanded="false">
                    <span class="faq-question">Ist Solar oder Wärmepumpe Voraussetzung?</span>
                    <div class="faq-chevron" aria-hidden="true">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </button>
                <div class="faq-body">
                    <div class="faq-body-inner">
                        <p class="faq-answer">Nein. Solar, Wärmepumpe und Speicher sind der Fokusmarkt, weil dort Anfragequalität, Vertriebsanschluss und Datenlage besonders schnell entscheidend werden. Die Arbeitsweise passt auch für anspruchsvolle B2B-Angebote mit ähnlicher Erklärungstiefe.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-trigger" aria-expanded="false">
                    <span class="faq-question">Was passiert nach der Projektprüfung?</span>
                    <div class="faq-chevron" aria-hidden="true">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </button>
                <div class="faq-body">
                    <div class="faq-body-inner">
                        <p class="faq-answer">Ich prüfe Angebot, Website-Rolle, Messbarkeit und naheliegende Priorität. Wenn der Fit passt, folgt daraus eine saubere Empfehlung: Korrektur, Umsetzung, Weiterentwicklung oder bewusst kein gemeinsamer nächster Schritt.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-trigger" aria-expanded="false">
                    <span class="faq-question">Brauche ich dafür einen Relaunch?</span>
                    <div class="faq-chevron" aria-hidden="true">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </button>
                <div class="faq-body">
                    <div class="faq-body-inner">
                        <p class="faq-answer">Nicht automatisch. Oft fehlt nicht der neue Look, sondern die richtige Reihenfolge zwischen Fundament, Daten, Sichtbarkeit und Conversion. Ein Relaunch ist nur sinnvoll, wenn die bestehende Struktur nicht mehr trägt.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-trigger" aria-expanded="false">
                    <span class="faq-question">Was unterscheidet WGOS von einem klassischen Agentur-Projekt?</span>
                    <div class="faq-chevron" aria-hidden="true">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </button>
                <div class="faq-body">
                    <div class="faq-body-inner">
                        <p class="faq-answer">WGOS ordnet Strategie, Fundament, Messbarkeit, Sichtbarkeit, Conversion und Weiterentwicklung als zusammenhängende Methode. Entscheidend ist die Reihenfolge: Welche Seite trägt welche Anfrage, welcher Kanal liefert echte Projekte und welche Änderung erzeugt als Nächstes Wirkung.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-trigger" aria-expanded="false">
                    <span class="faq-question">Für wen passt diese Seite nicht?</span>
                    <div class="faq-chevron" aria-hidden="true">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </button>
                <div class="faq-body">
                    <div class="faq-body-inner">
                        <p class="faq-answer">Nicht passend sind kleine One-Page-Visitenkarten, reine Design-Relaunches ohne Lead-Logik und E-Commerce-Projekte mit Shopify- oder WooCommerce-Fokus.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-trigger" aria-expanded="false">
                    <span class="faq-question">Warum rankt eine WordPress-Seite, liefert aber keine Anfragen?</span>
                    <div class="faq-chevron" aria-hidden="true">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </button>
                <div class="faq-body">
                    <div class="faq-body-inner">
                        <p class="faq-answer">Ranking und Anfragequalität sind zwei verschiedene Probleme. Oft rankt eine Seite für Informationssuchen, während Proof, CTA-Führung und kaufnahe Argumentation fehlen. Genau dort verbindet diese Arbeit SEO, Struktur, Tracking und Conversion.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════
     ABGRENZUNG
═══════════════════════════════════════ -->
<section class="section bg-background">
    <div class="section-inner">
        <div class="section-header">
            <p class="section-eyebrow">Abgrenzung</p>
            <h2 class="section-title">Für wen diese Seite nicht passt.</h2>
            <p class="section-subtitle">
                Die Seite ist kein Sammelpunkt für kleine Standard-Websites. Sie passt, wenn WordPress ein relevanter B2B-Kanal werden oder bleiben soll.
            </p>
        </div>

        <div class="abgrenzung-list">
            <div class="abgrenzung-item">
                <div class="abgrenzung-x" aria-hidden="true">×</div>
                <p class="abgrenzung-text">One-Page-Visitenkarten und Standard-Websites ohne relevanten Projektumfang.</p>
            </div>
            <div class="abgrenzung-item">
                <div class="abgrenzung-x" aria-hidden="true">×</div>
                <p class="abgrenzung-text">E-Commerce-Projekte mit Shopify- oder WooCommerce-Fokus.</p>
            </div>
            <div class="abgrenzung-item">
                <div class="abgrenzung-x" aria-hidden="true">×</div>
                <p class="abgrenzung-text">Reine Design-Relaunches ohne Lead-Logik, Tracking und kaufnahe Angebotsstruktur.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════
     FOKUSMARKT ENERGIE
═══════════════════════════════════════ -->
<section class="section bg-surface">
    <div class="section-inner">
        <div class="section-header">
            <p class="section-eyebrow">Fokusmarkt Energie</p>
            <h2 class="section-title">Solar, Wärmepumpe oder Speicher?</h2>
            <p class="section-subtitle">
                Wenn Ihr Angebot Solar, Wärmepumpe oder Speicher ist, gibt es einen präziseren Einstieg: den Marktcheck, der gezielt auf Lead-Generierung in dieser Branche zugeschnitten ist.
            </p>
        </div>
        <div class="fokus-cta">
            <a href="<?php echo esc_url( $marktcheck_url ); ?>" class="btn btn-primary">
                Zum Marktcheck für Solar &amp; Wärmepumpe
            </a>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════
     STANDORT
═══════════════════════════════════════ -->
<section class="section bg-background">
    <div class="section-inner">
        <div class="section-header">
            <p class="section-eyebrow">Standort</p>
            <h2 class="section-title">Aus Hannover für den DACH-Raum.</h2>
            <p class="section-subtitle">
                Persönliche Termine, Workshops und Reviews sind in Hannover, Pattensen und der Region Hannover möglich. Die Umsetzung funktioniert genauso sauber remote.
            </p>
        </div>
        <div class="standort-box">
            <p class="standort-text">
                Bestandskunden mit etabliertem WordPress-System: WordPress Wartung Hannover und Weiterentwicklung im Rahmen laufender Mandate.
            </p>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════
     CTA
═══════════════════════════════════════ -->
<section class="cta-section">
    <div class="cta-inner">
        <h2 class="cta-title">Ich prüfe, ob Ihr WordPress-System als Anfrage-System tragfähig ist.</h2>
        <p class="cta-subtitle">
            Wenn Ihr Projekt zu dieser Arbeitsweise passt, ist der nächste Schritt kein Standard-Pitch, sondern eine klare Einordnung von Angebot, Website, Messbarkeit und nächster Priorität.
        </p>
        <div class="cta-btns">
            <a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn-primary">Projekt prüfen</a>
            <a href="<?php echo esc_url( $e3_url ); ?>" class="btn btn-secondary">E3-Case ansehen</a>
        </div>
    </div>
</section>

<script>
    // ─── Generic accordion factory ───
    function initAccordion(containerSelector, itemSelector, triggerSelector, bodySelector) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        container.querySelectorAll(itemSelector).forEach(item => {
            const trigger = item.querySelector(triggerSelector);
            const body    = item.querySelector(bodySelector);
            if (!trigger || !body) return;

            trigger.addEventListener('click', () => {
                const isOpen = item.classList.contains('is-open');
                item.classList.toggle('is-open', !isOpen);
                trigger.setAttribute('aria-expanded', String(!isOpen));
            });
        });
    }

    initAccordion('#asset-accordion', '.acc-item',  '.acc-trigger',  '.acc-body');
    initAccordion('#faq-accordion',   '.faq-item',  '.faq-trigger',  '.faq-body');
</script>

<?php
get_footer();
