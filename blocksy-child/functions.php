<?php
/**
 * Blocksy Child – Growth Architect Edition
 *
 * Nur Modul-Bootstrap: jede Funktion und jeder Hook lebt in inc/.
 * Structure Layer im Repo → Content Layer im Editor.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── 1. MODULE LADEN (inc/) ────────────────────────────────────────
$inc_dir = get_stylesheet_directory() . '/inc/';

$modules = [
	'helpers.php',        // Utility-Funktionen (muss zuerst geladen werden)
	'theme-setup.php',    // Menue-Slot, Fonts, Marke/Favicons, Blocksy-Overrides, Share-Buttons
	'affiliate-links.php', // Affiliate-URL-Registry und Disclosure-Helper
	'feature-flags.php',  // Staged Rollout-Schalter fuer neue Funnel-Routen und Submits
	'article-content-hygiene.php',             // Einmalige Editor-Migrationen für Alt-Artikel
	'article-content-hygiene-ttfb.php',        // … TTFB-Artikel
	'article-content-hygiene-landingpage.php', // … Landingpage-Artikel
	'article-b2b-inquiry-system.php',          // … Artikel zum B2B-Anfragesystem
	'article-agency-outsourcing.php',          // … Auslagerungs-Leitfaden für Agenturen
	'article-agency-outsourcing-hero.php',     // … Hero des Auslagerungs-Leitfadens
	'article-reader-toc.php',                  // Inhaltsverzeichnis im Artikel-Reader
	'editorial-bausteine.php',                 // Artikel-Bausteine: Kurzantwort, Notiz, Abbildung, Fall, Prüfliste, Abschluss, Quellen
	'article-website-relaunch.php',            // … Cornerstone Website-Relaunch
	'canon/e3-proof-canon.php', // Kanonische E3-Proof-Zahlen und Displaywerte
	'canon/diagnose-canon.php', // Kanonische Diagnose-Stufen, Preise und Scope-Grenzen
	'canon/pricing-canon.php', // Kanonische Foundation-, Performance- und Premium-Preise
	'canon/messaging-canon.php', // Zentrale Wertanker, Abgrenzungen und Begriffsschutz
	'canon/reference-canon.php', // Kanonische, oeffentlich pruefbare Referenzprojekte
	'canon/market-canon.php', // Fremde Marktzahlen mit Quelle — streng getrennt von eigenen Ergebnissen
	'mail.php',           // Zentraler Brevo-Mail-Router für Transaktionsmails
	'crm.php',            // Gemeinsame CRM-Grundlage für Kontakte, Blog-Abos und Projektanfragen
	'wgos/wgos-access.php',    // Interne WGOS-Clientrolle, Dashboard-Capability und Backend-Sperre
	'wgos/wgos-assets.php',    // CPT + Helper für WGOS Asset-Spokes
	'wgos/wgos-asset-registry.php', // Versionierte WGOS Asset-Registry + Sync
	'glossary/glossary.php',       // Glossar-Hub + CPT für definitorische Begriffe
	'glossary/glossary-registry.php', // Versionierte Glossar-Registry + Sync
	'glossary/glossary-autolink.php', // Auto-Linking: Glossar-Begriffe in Blog-Posts verlinken
	'wgos/wgos-cluster-pages.php', // Versionierte Cluster-/Pillar-Pages und Blog-Asset-Bridges
	'acf.php',            // ACF Feldgruppen-Registrierung (SEO, KPI, Comparison)
	'header.php',         // Eigener globaler Header + Navigation
	'review-crm.php',     // Marktcheck-Intake + WordPress CRM
	'contact-page.php',   // Kontakt-Route, schlanke Kontaktform und Mailversand
	'whitelabel-request.php', // Vierfeldriges Agentur-Formular der White-Label-Route
	'system-diagnose-page.php', // Deutsche Analyse-Route plus Legacy-Redirect
	'analysis-intake.php', // REST-Endpoint der früheren Analyse; standardmäßig aus (HU_FEATURE_READINESS_SUBMIT)
	'blog-notify.php',    // Blog-Benachrichtigungen, DOI und Artikel-Mails
	'post-rating.php',    // Artikel-Bewertung (Hilfreich/Nicht hilfreich) + Admin-Spalte
	'cpo-calculator.php', // CPO-Rechner für Photovoltaik-Anfragen
	'blog-provider-posts.php', // Einmalige Live-Anlage der Lead-Anbieter-Markteinordnungen
	'blog-pillar-posts.php', // Einmalige Live-Anlage strategischer Pillar-Beiträge
	'robots-txt.php',     // Dynamische /robots.txt-Route für Search- und KI-Crawler
	'llms-txt.php',       // Dynamische /llms.txt-Route für KI-Agenten und Entitätskontext
	'seo-cockpit/seo-cockpit.php',    // Search Console basiertes SEO-Cockpit mit optionaler Koko-Erkennung
	'enqueue.php',        // CSS/JS Asset-Management
	'homepage-wow.php',   // Noindex-Testseite fuer visuelle Homepage-Variante
	'seo-meta.php',       // OG Tags, Canonical, Indexierungssteuerung
	'sitemap.php',        // Native Sitemap: Legacy-Redirect, noindex-Ausschluss, keine Users-Sitemap
	'positioning-meta.php', // Repositioning-Overrides für globale Homepage-/Blog-Metadaten
	'seo-subpage-cluster-links.php', // Kontextuelle Querverlinkung des Solar/B2B-Clusters
	'post-permalink-author-redirect.php', // 301 von /beitrag/<autor>/ auf den Beitrag
	'org-schema.php',     // JSON-LD Structured Data
	'commercial-routing.php',  // Kanonische Routen für Direkt, White-Label und Energie
	'schema-positioning.php', // Repositioning-Normalisierung der kanonischen Schema-Entitäten
	'shortcodes.php',     // Startseiten-Shortcodes
	'client-portal.php',  // Client Portal Dashboard
	'admin-manager.php',  // Backend-Felder für Portal
	'crm-sales.php',      // Vertrieb, Aktivitäten, Follow-ups und Antwortfrist-Wächter (inc/crm-sales/)
	'api-telemetry.php',  // Observability Light für Nexus API-Fehler
	'accessibility-navigation.php', // Skip-Link, Fokus und Tastaturnavigation
	'snippets.php',       // Nav Button, Security, Login-Redirect
	'menu-setup.php',     // Hauptmenü-Struktur (einmalig)
];

foreach ( $modules as $module ) {
	$path = $inc_dir . $module;
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}
