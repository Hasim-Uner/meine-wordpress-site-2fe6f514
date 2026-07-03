<?php
/**
 * Native page template for slug: impressum
 *
 * Replaces editor-managed legal content with a maintained imprint page
 * so accidental navigation blocks or stray page content cannot leak into
 * the public legal page.
 *
 * @package Blocksy_Child
 */

get_header();

while ( have_posts() ) :
	the_post();

	$privacy_url = function_exists( 'nexus_get_page_url' )
		? nexus_get_page_url( [ 'datenschutz' ], home_url( '/datenschutz/' ) )
		: home_url( '/datenschutz/' );
	$contact_url = function_exists( 'nexus_get_contact_url' )
		? nexus_get_contact_url()
		: home_url( '/kontakt/' );
	$mail_link   = 'mailto:kontakt@hasimuener.de';
	$phone_link  = 'tel:+4917681407134';
	?>
	<main id="main" class="site-main imprint-page" data-track-section="imprint_page">
		<style>
			.imprint-page {
				--imprint-surface: rgba(255, 255, 255, 0.04);
				--imprint-surface-strong: rgba(255, 255, 255, 0.06);
				--imprint-border: rgba(255, 255, 255, 0.1);
				--imprint-border-strong: rgba(255, 255, 255, 0.16);
				--imprint-accent: var(--theme-palette-color-1, #b46a3c);
				--imprint-accent-soft: rgba(180, 106, 60, 0.14);
				--imprint-text: var(--theme-text-color, #f5f1eb);
				--imprint-muted: rgba(255, 255, 255, 0.74);
				position: relative;
				padding: clamp(7rem, 12vw, 9rem) 1.25rem 5rem;
				background:
					radial-gradient(circle at top left, rgba(180, 106, 60, 0.14), transparent 30%),
					radial-gradient(circle at top right, rgba(255, 255, 255, 0.05), transparent 22%),
					var(--theme-palette-color-5, #0a0a0a);
				overflow: hidden;
			}

			.imprint-page::before {
				content: "";
				position: absolute;
				inset: 0;
				background:
					linear-gradient(180deg, rgba(255, 255, 255, 0.02), transparent 28%),
					repeating-linear-gradient(
						90deg,
						rgba(255, 255, 255, 0.018) 0,
						rgba(255, 255, 255, 0.018) 1px,
						transparent 1px,
						transparent 48px
					);
				opacity: 0.3;
				pointer-events: none;
			}

			.imprint-shell {
				position: relative;
				z-index: 1;
				max-width: 1120px;
				margin: 0 auto;
			}

			.imprint-hero {
				padding: clamp(1.5rem, 3vw, 2.4rem);
				border: 1px solid var(--imprint-border);
				border-radius: 28px;
				background: linear-gradient(180deg, rgba(255, 255, 255, 0.045), rgba(255, 255, 255, 0.02));
				box-shadow: 0 18px 48px rgba(0, 0, 0, 0.18);
				backdrop-filter: blur(14px);
				-webkit-backdrop-filter: blur(14px);
			}

			.imprint-kicker {
				display: inline-flex;
				align-items: center;
				padding: 0.38rem 0.72rem;
				border-radius: 999px;
				border: 1px solid rgba(255, 255, 255, 0.12);
				background: rgba(255, 255, 255, 0.05);
				color: #f1dcc0;
				font-size: 0.78rem;
				font-weight: 700;
				letter-spacing: 0.05em;
				text-transform: uppercase;
			}

			.imprint-title {
				margin: 1rem 0 0.65rem;
				font-size: clamp(2rem, 4vw, 3.15rem);
				line-height: 1.08;
				color: #fff;
			}

			.imprint-lead {
				max-width: 52rem;
				margin: 0;
				color: var(--imprint-muted);
				font-size: 1rem;
				line-height: 1.72;
			}

			.imprint-badges {
				display: flex;
				flex-wrap: wrap;
				gap: 0.7rem;
				margin-top: 1.25rem;
			}

			.imprint-badge {
				display: inline-flex;
				align-items: center;
				padding: 0.52rem 0.8rem;
				border-radius: 999px;
				border: 1px solid rgba(255, 255, 255, 0.08);
				background: rgba(255, 255, 255, 0.04);
				color: #fff6ea;
				font-size: 0.9rem;
				line-height: 1.2;
			}

			.imprint-actions {
				display: flex;
				flex-wrap: wrap;
				gap: 0.75rem;
				margin-top: 1.25rem;
			}

			.imprint-button {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				padding: 0.82rem 1rem;
				border-radius: 14px;
				border: 1px solid var(--imprint-border);
				background: var(--imprint-surface);
				color: #fff;
				font-weight: 600;
				text-decoration: none;
				transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
			}

			.imprint-button:hover,
			.imprint-button:focus-visible {
				transform: translateY(-1px);
				border-color: rgba(180, 106, 60, 0.35);
				background: var(--imprint-surface-strong);
			}

			.imprint-button--primary {
				background: linear-gradient(180deg, rgba(180, 106, 60, 0.28), rgba(180, 106, 60, 0.18));
				border-color: rgba(180, 106, 60, 0.34);
				color: #fff;
			}

			.imprint-grid {
				display: grid;
				grid-template-columns: minmax(0, 0.94fr) minmax(0, 1.36fr);
				gap: 1.1rem;
				margin-top: 1.1rem;
			}

			.imprint-card,
			.imprint-section {
				border: 1px solid var(--imprint-border);
				border-radius: 22px;
				background: var(--imprint-surface);
				box-shadow: 0 16px 34px rgba(0, 0, 0, 0.12);
			}

			.imprint-card {
				padding: 1.2rem;
			}

			.imprint-card h2,
			.imprint-section h2 {
				margin: 0 0 0.8rem;
				font-size: 1.12rem;
				line-height: 1.3;
				color: #fff;
			}

			.imprint-card p,
			.imprint-card li,
			.imprint-section p,
			.imprint-section li {
				color: var(--imprint-muted);
				line-height: 1.72;
			}

			.imprint-card ul {
				margin: 0.85rem 0 0;
				padding-left: 1.15rem;
			}

			.imprint-quickfacts {
				display: grid;
				gap: 0.75rem;
				margin-top: 1rem;
			}

			.imprint-quickfact {
				padding: 0.95rem 1rem;
				border-radius: 18px;
				border: 1px solid rgba(255, 255, 255, 0.08);
				background: rgba(255, 255, 255, 0.03);
			}

			.imprint-quickfact__label {
				display: block;
				margin-bottom: 0.32rem;
				color: #ffd39e;
				font-size: 0.78rem;
				font-weight: 700;
				letter-spacing: 0.04em;
				text-transform: uppercase;
			}

			.imprint-quickfact__value,
			.imprint-address {
				display: block;
				font-style: normal;
				color: #fff;
				line-height: 1.6;
			}

			.imprint-sections {
				display: grid;
				gap: 1rem;
			}

			.imprint-section {
				padding: 1.2rem;
			}

			.imprint-section a,
			.imprint-card a {
				color: #ffd39e;
				text-underline-offset: 0.18em;
			}

			.imprint-copy-chip {
				display: inline-flex;
				align-items: center;
				padding: 0.4rem 0.72rem;
				border-radius: 999px;
				background: var(--imprint-accent-soft);
				border: 1px solid rgba(180, 106, 60, 0.24);
				color: #f8e7d4;
				font-size: 0.82rem;
				font-weight: 700;
				letter-spacing: 0.02em;
			}

			.imprint-section + .imprint-section {
				position: relative;
			}

			.imprint-note {
				margin-top: 0.85rem;
				padding: 0.92rem 1rem;
				border-radius: 16px;
				border: 1px solid rgba(255, 255, 255, 0.08);
				background: rgba(255, 255, 255, 0.03);
			}

			.imprint-note strong {
				color: #fff;
			}

			:root[data-theme='light'] .imprint-page,
			:root[data-nx-theme='light'] .imprint-page {
				--imprint-surface: rgba(255, 251, 246, 0.92);
				--imprint-surface-strong: rgba(255, 248, 239, 0.98);
				--imprint-border: rgba(56, 36, 24, 0.08);
				--imprint-border-strong: rgba(56, 36, 24, 0.16);
				--imprint-muted: rgba(32, 24, 20, 0.78);
				background:
					radial-gradient(circle at top left, rgba(180, 106, 60, 0.12), transparent 28%),
					linear-gradient(180deg, #fffdfa, #f4eee8);
			}

			:root[data-theme='light'] .imprint-page::before,
			:root[data-nx-theme='light'] .imprint-page::before {
				background:
					linear-gradient(180deg, rgba(180, 106, 60, 0.05), transparent 26%),
					repeating-linear-gradient(
						90deg,
						rgba(90, 64, 46, 0.03) 0,
						rgba(90, 64, 46, 0.03) 1px,
						transparent 1px,
						transparent 48px
					);
				opacity: 0.45;
			}

			:root[data-theme='light'] .imprint-title,
			:root[data-theme='light'] .imprint-card h2,
			:root[data-theme='light'] .imprint-section h2,
			:root[data-theme='light'] .imprint-quickfact__value,
			:root[data-theme='light'] .imprint-address,
			:root[data-nx-theme='light'] .imprint-title,
			:root[data-nx-theme='light'] .imprint-card h2,
			:root[data-nx-theme='light'] .imprint-section h2,
			:root[data-nx-theme='light'] .imprint-quickfact__value,
			:root[data-nx-theme='light'] .imprint-address {
				color: #1d1714;
			}

			:root[data-theme='light'] .imprint-button,
			:root[data-nx-theme='light'] .imprint-button {
				color: #1d1714;
			}

			@media (max-width: 920px) {
				.imprint-grid {
					grid-template-columns: 1fr;
				}
			}

			@media (max-width: 640px) {
				.imprint-page {
					padding-top: 6.5rem;
				}

				.imprint-hero,
				.imprint-card,
				.imprint-section {
					border-radius: 20px;
				}

				.imprint-actions,
				.imprint-badges {
					flex-direction: column;
				}

				.imprint-button,
				.imprint-badge {
					width: 100%;
					justify-content: center;
				}
			}
		</style>

		<div class="imprint-shell">
			<section class="imprint-hero" aria-labelledby="imprint-title">
				<span class="imprint-kicker">Impressum</span>
				<h1 id="imprint-title" class="imprint-title">Pflichtangaben für hasimuener.de</h1>
				<p class="imprint-lead">
					Diese Seite bündelt die Anbieterangaben gemäß § 5 DDG sowie die
					Verantwortlichkeit nach § 18 Abs. 2 MStV. Für Rückfragen zu diesen Angaben
					oder zu den angebotenen Leistungen erreichen Sie Haşim Üner direkt per
					E-Mail oder Telefon.
				</p>

				<div class="imprint-badges" aria-label="Rechtsgrundlagen und Kontaktwege">
					<span class="imprint-badge">§ 5 DDG</span>
					<span class="imprint-badge">§ 18 Abs. 2 MStV</span>
					<span class="imprint-badge">E-Mail und Telefon direkt erreichbar</span>
				</div>

				<div class="imprint-actions">
					<a class="imprint-button imprint-button--primary" href="<?php echo esc_url( $mail_link ); ?>">E-Mail schreiben</a>
					<a class="imprint-button" href="<?php echo esc_url( $privacy_url ); ?>">Datenschutz</a>
					<a class="imprint-button" href="<?php echo esc_url( $contact_url ); ?>">Kontakt</a>
				</div>
			</section>

			<div class="imprint-grid">
				<aside class="imprint-card" aria-labelledby="imprint-overview">
					<h2 id="imprint-overview">Schnellüberblick</h2>
					<p>
						Haşim Üner betreibt diese Website als geschäftliches Informations- und
						Angebotsmedium für WordPress-, SEO- und Growth-Themen im B2B-Kontext.
					</p>

					<ul>
						<li>direkte Kontaktaufnahme per E-Mail und Telefon</li>
						<li>Anschrift des Anbieters vollständig angegeben</li>
						<li>verantwortliche Person für redaktionelle Inhalte benannt</li>
						<li>Datenschutzhinweise separat unter <a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutz</a></li>
					</ul>

					<div class="imprint-quickfacts" aria-label="Anbieter und Kontakt">
						<div class="imprint-quickfact">
							<span class="imprint-quickfact__label">Anbieter</span>
							<span class="imprint-quickfact__value">Hasim Üner</span>
						</div>

						<div class="imprint-quickfact">
							<span class="imprint-quickfact__label">Anschrift</span>
							<address class="imprint-address">
								Warschauer Str. 5<br>
								30982 Pattensen<br>
								Deutschland
							</address>
						</div>

						<div class="imprint-quickfact">
							<span class="imprint-quickfact__label">Kontakt</span>
							<span class="imprint-quickfact__value">
								E-Mail: <a href="<?php echo esc_url( $mail_link ); ?>">kontakt@hasimuener.de</a><br>
								Telefon: <a href="<?php echo esc_url( $phone_link ); ?>">0176 81407134</a>
							</span>
						</div>
					</div>
				</aside>

				<div class="imprint-sections">
					<section class="imprint-section" aria-labelledby="imprint-ddg">
						<span class="imprint-copy-chip">Angaben gemäß § 5 DDG</span>
						<h2 id="imprint-ddg">1. Diensteanbieter</h2>
						<p>
							Hasim Üner<br>
							Warschauer Str. 5<br>
							30982 Pattensen<br>
							Deutschland
						</p>
						<p>
							Die Namensangabe folgt der Schreibweise im Ausweisdokument.
							Die Eigenschreibweise lautet Haşim Üner.
						</p>
						<div class="imprint-note">
							<strong>Geschäftlicher Zweck der Website:</strong>
							Information über Beratungs-, Konzeptions- und Umsetzungsleistungen rund um
							WordPress, SEO, Tracking, CRO und Growth-Systeme.
						</div>
					</section>

					<section class="imprint-section" aria-labelledby="imprint-contact">
						<span class="imprint-copy-chip">Direkte Kontaktaufnahme</span>
						<h2 id="imprint-contact">2. Kontakt</h2>
						<p>
							E-Mail: <a href="<?php echo esc_url( $mail_link ); ?>">kontakt@hasimuener.de</a><br>
							Telefon: <a href="<?php echo esc_url( $phone_link ); ?>">0176 81407134</a>
						</p>
						<p>
							Für allgemeine Anfragen, Projektanfragen und Rückfragen zu Inhalten dieser
							Website kann die Kontaktaufnahme über die oben genannten Wege erfolgen.
						</p>
					</section>

					<section class="imprint-section" aria-labelledby="imprint-editorial">
						<span class="imprint-copy-chip">Redaktionelle Verantwortung</span>
						<h2 id="imprint-editorial">3. Verantwortlich i.S.d. § 18 Abs. 2 MStV</h2>
						<p>
							Hasim Üner<br>
							Warschauer Str. 5<br>
							30982 Pattensen<br>
							Deutschland
						</p>
					</section>

					<section class="imprint-section" aria-labelledby="imprint-legal-links">
						<span class="imprint-copy-chip">Rechtliche Ergänzungen</span>
						<h2 id="imprint-legal-links">4. Weitere rechtliche Hinweise</h2>
						<p>
							Die Informationen zum Umgang mit personenbezogenen Daten finden Sie in der
							separaten <a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutzerklärung</a>.
							Wenn Sie eine direkte Anfrage stellen möchten, können Sie außerdem die
							<a href="<?php echo esc_url( $contact_url ); ?>">Kontaktseite</a> nutzen.
						</p>
					</section>
				</div>
			</div>
		</div>
	</main>
	<?php
endwhile;

get_footer();
