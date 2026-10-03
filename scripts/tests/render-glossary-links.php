<?php
/** Shared component in clipped, dark and edge containers with the real registry. */
require __DIR__ . '/glossary-links-harness.php';
nav_test_use_context( 'article_cro' );
$theme = '/wp-content/themes/blocksy-child/';
?>
<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Glossar-Verbindungen</title>
<link rel="stylesheet" href="<?php echo esc_attr( $theme ); ?>assets/css/system.css">
<link rel="stylesheet" href="<?php echo esc_attr( $theme ); ?>assets/css/glossary-links.css">
<style>body{margin:0;padding:24px;font-family:var(--serif)}p{margin-block:24px}#clipped{overflow:hidden;transform:translateZ(0);height:64px}#edge{text-align:right;margin-top:100px}#bottom{position:fixed;bottom:8px;left:24px}#scroll{height:900px}#long{width:6ch;font-size:32px}</style>
</head><body>
<a href="#content" id="before">Zum Inhalt</a>
<main id="content">
<section id="clipped"><p><?php echo nexus_glossary_link( 'crm', 'CRM' ); ?> sortiert Anfragen.</p></section>
<section class="tafel" id="dark"><p><?php echo nexus_glossary_link( 'attribution' ); ?> ordnet die Quelle zu.</p></section>
<section id="edge"><p><?php echo nexus_glossary_link( 'consent-mode' ); ?></p></section>
<section id="long"><p><?php echo nexus_glossary_link( 'render-blockierende-ressourcen' ); ?></p></section>
<article id="article"><?php echo nexus_glossary_autolink( '<h2>Conversion ohne Zusatzlink</h2><p>Cost per Lead und CRM in einem Absatz.</p><p>CRM im zweiten Absatz.</p><p>Conversion als Hauptziel.</p>' ); ?></article>
<p id="bottom"><?php echo nexus_glossary_link( 'lcp' ); ?> am unteren Rand.</p>
<div id="scroll"></div>
</main>
<script src="<?php echo esc_attr( $theme ); ?>assets/js/glossary-links.js"></script>
</body></html>
