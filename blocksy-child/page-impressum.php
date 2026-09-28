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
	// Rechtlich benannter Kontaktweg, aber derselbe wie der beworbene. Seit
	// 2026-09-18 liest auch diese Seite den Canon: zwei getrennt gepflegte
	// Staende derselben Adresse waren nur eine Gelegenheit, einen davon
	// stehen zu lassen.
	$mail_address = hu_get_contact_email();
	$mail_link    = hu_get_contact_mailto();
	$phone_link   = hu_get_contact_phone( 'link' );
	$phone_number = hu_get_contact_phone();
	?>
	<div class="site-main imprint-page legal-page doku" data-track-section="imprint_page">
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
								E-Mail: <a href="<?php echo esc_url( $mail_link ); ?>"><?php echo esc_html( $mail_address ); ?></a><br>
								Telefon: <a href="<?php echo esc_url( $phone_link ); ?>"><?php echo esc_html( $phone_number ); ?></a>
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
							E-Mail: <a href="<?php echo esc_url( $mail_link ); ?>"><?php echo esc_html( $mail_address ); ?></a><br>
							Telefon: <a href="<?php echo esc_url( $phone_link ); ?>"><?php echo esc_html( $phone_number ); ?></a>
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
	</div>
	<?php
endwhile;

get_footer();
