<?php
/**
 * Native page template for slug: datenschutz
 *
 * Replaces the editor-managed privacy text with a maintained legal page
 * that reflects the current setup of the public site: no cookies, no
 * browser storage, cookieless Koko Analytics on the own server.
 *
 * @package Blocksy_Child
 */

get_header();

while ( have_posts() ) :
	the_post();

	$imprint_url = function_exists( 'nexus_get_page_url' )
		? nexus_get_page_url( [ 'impressum' ], home_url( '/impressum/' ) )
		: home_url( '/impressum/' );
	$contact_url = function_exists( 'nexus_get_contact_url' )
		? nexus_get_contact_url()
		: home_url( '/kontakt/' );
	$rights_url = '#rechte';
	?>
	<div class="site-main privacy-page legal-page doku" data-track-section="privacy_page">
		<div class="privacy-shell">
			<section class="privacy-hero" aria-labelledby="privacy-title">
				<div class="legal-hero-grid">
					<div class="legal-hero-copy">
						<span class="privacy-kicker">Datenschutz</span>
						<h1 id="privacy-title" class="privacy-title">Datenschutz auf einen Blick</h1>
						<p class="privacy-lead">
							Diese öffentlich zugängliche Website setzt bei normalen Besuchen keine Cookies,
							speichert nichts in Ihrem Browser und verwendet weder Google Tag Manager noch
							Google Analytics oder Marketing-Tracker. Seitenaufrufe zählen wir cookielos auf
							unserem eigenen Server.
						</p>

						<div class="privacy-statement">
							<strong>Kein Cookie-Banner auf öffentlichen Seiten:</strong>
							Beim normalen Besuch wird nichts in Ihrem Browser gespeichert, und es laufen
							keine Tracking- oder Marketing-Dienste Dritter.
						</div>

						<div class="privacy-actions">
							<a class="privacy-button privacy-button--primary" href="<?php echo esc_url( $imprint_url ); ?>">Zum Impressum</a>
							<a class="privacy-button" href="<?php echo esc_url( $rights_url ); ?>">Ihre Rechte</a>
							<a class="privacy-button" href="<?php echo esc_url( $contact_url ); ?>">Kontakt</a>
						</div>
					</div>

					<aside class="legal-hero-visual" aria-label="Datenfluss dieser Website">
						<span class="legal-visual__eyebrow">Datenfluss · öffentlich</span>
						<span class="legal-visual__index" aria-hidden="true">00</span>
						<div class="legal-visual__track">
							<div class="legal-visual__row">
								<span class="legal-visual__label">Browser</span>
								<span class="legal-visual__value">kein Speicher</span>
								<span class="legal-visual__signal" aria-hidden="true"></span>
							</div>
							<div class="legal-visual__row">
								<span class="legal-visual__label">Analyse</span>
								<span class="legal-visual__value">cookielos · eigener Server</span>
								<span class="legal-visual__signal" aria-hidden="true"></span>
							</div>
							<div class="legal-visual__row">
								<span class="legal-visual__label">Kontakt</span>
								<span class="legal-visual__value">Daten erst bei Ihrer Anfrage</span>
								<span class="legal-visual__signal" aria-hidden="true"></span>
							</div>
						</div>
						<p class="legal-visual__foot">Keine versteckte dritte Ebene. Die drei Stationen entsprechen dem tatsächlichen Setup.</p>
					</aside>
				</div>

				<div class="privacy-facts" aria-label="Datenschutz-Kurzüberblick">
					<div class="privacy-fact">
						<span class="privacy-fact__value">Keine Cookies</span>
						<span class="privacy-fact__label">und kein Browser-Speicher bei normalen Seitenaufrufen</span>
					</div>
					<div class="privacy-fact">
						<span class="privacy-fact__value">Kein GTM, kein Google Analytics</span>
						<span class="privacy-fact__label">Statistik nur cookielos auf dem eigenen Server</span>
					</div>
					<div class="privacy-fact">
						<span class="privacy-fact__value">Daten nur bei Kontakt</span>
						<span class="privacy-fact__label">wenn Sie uns aktiv schreiben oder ein Formular senden</span>
					</div>
				</div>
			</section>

			<div class="privacy-grid">
				<aside class="privacy-card" aria-labelledby="privacy-overview">
					<h2 id="privacy-overview">Kurzüberblick</h2>
					<p>
						Die Seite ist bewusst schlank aufgebaut. Es gibt aktuell keine öffentlichen
						Tracking-Cookies, keine Consent-Plattform, kein GTM, kein Google Analytics
						und keine Marketing-Pixel.
					</p>

					<ul>
						<li>keine Cookies und kein Browser-Speicher für normale Seitenaufrufe</li>
						<li>keine Werbetracker; Seitenaufrufe nur cookielos gezählt (Koko Analytics)</li>
						<li>keine automatischen Social-Media-Embeds</li>
						<li>keine extern geladenen Webfonts von Google</li>
						<li>personenbezogene Daten nur bei aktiver Kontaktaufnahme</li>
					</ul>

					<div class="privacy-meta" aria-label="Verantwortlicher und Stand">
						<div class="privacy-meta__item">
							<span class="privacy-meta__label">Verantwortlicher</span>
							<span class="privacy-meta__value">
								Haşim Üner<br>
								Warschauer Str. 5<br>
								30982 Pattensen<br>
								Deutschland
							</span>
						</div>
						<div class="privacy-meta__item">
							<span class="privacy-meta__label">Kontakt</span>
							<span class="privacy-meta__value">
								E-Mail: <a href="<?php echo esc_url( hu_get_contact_mailto() ); ?>"><?php echo esc_html( hu_get_contact_email() ); ?></a><br>
								Telefon: <a href="<?php echo esc_url( hu_get_contact_phone( 'link' ) ); ?>"><?php echo esc_html( hu_get_contact_phone() ); ?></a>
							</span>
						</div>
						<div class="privacy-meta__item">
							<span class="privacy-meta__label">Stand</span>
							<span class="privacy-meta__value"><time datetime="2026-09-23">23. September 2026</time></span>
						</div>
					</div>
				</aside>

				<div class="privacy-sections">
					<section class="privacy-section" aria-labelledby="privacy-controller">
						<h2 id="privacy-controller">1. Verantwortlicher</h2>
						<p>
							Verantwortlich für die Datenverarbeitung auf dieser Website ist Haşim Üner,
							Warschauer Str. 5, 30982 Pattensen, Deutschland.
							Die vollständigen Anbieterangaben finden Sie im
							<a href="<?php echo esc_url( $imprint_url ); ?>">Impressum</a>.
						</p>
					</section>

					<section class="privacy-section" aria-labelledby="privacy-cookies">
						<h2 id="privacy-cookies">2. Cookies, Tracking und Browser-Speicher</h2>
						<p>
							Bei der rein informatorischen Nutzung dieser öffentlich zugänglichen Website
							setzen wir keine Cookies und speichern auch sonst nichts in Ihrem Browser,
							weder im lokalen Speicher (localStorage) noch im Sitzungsspeicher
							(sessionStorage). Remarketing- oder Marketing-Skripte werden nicht geladen.
						</p>
						<ul>
							<li>kein Google Tag Manager</li>
							<li>kein Google Analytics</li>
							<li>kein Google Ads Conversion Tracking</li>
							<li>keine Retargeting-Pixel</li>
							<li>keine Speicherung im Browser für Statistik, Tracking oder Komfortfunktionen</li>
							<li>deshalb kein Cookie-Banner für öffentliche Besuche</li>
						</ul>
						<div class="privacy-note">
							Diese Aussage bezieht sich auf die öffentliche Nutzung der Website. Technisch
							notwendige WordPress-Cookies können nur im geschützten Administrationsbereich
							für eingeloggte Nutzer entstehen.
						</div>

						<h3>Besucherstatistik mit Koko Analytics</h3>
						<p>
							Um zu sehen, welche Seiten gelesen werden, nutzen wir Koko Analytics, eine
							Erweiterung für WordPress, die auf unserem eigenen Server läuft. Beim
							Seitenaufruf übermittelt ein kleines Skript die aufgerufene Seite, die
							verweisende Website und gegebenenfalls Kampagnenangaben aus dem Link
							(utm-Parameter) an unseren Server. Es setzt keine Cookies und speichert nichts
							in Ihrem Browser. Die Daten werden nicht an Dritte übermittelt.
						</p>
						<p>
							Um mehrfache Aufrufe am selben Tag zu erkennen, bildet die Erweiterung aus
							IP-Adresse und Browserkennung (User-Agent) zusammen mit einem täglich
							wechselnden Geheimwert einen Einwegwert (Hash). IP-Adresse und Browserkennung
							selbst werden dabei nicht gespeichert, die Hashwerte werden täglich gelöscht;
							Besuche an verschiedenen Tagen lassen sich so nicht verknüpfen. Dauerhaft
							gespeichert werden nur zusammengefasste Zahlen, etwa Aufrufe je Seite und
							verweisende Websites.
						</p>
						<p>
							Rechtsgrundlage ist Art. 6 Abs. 1 lit. f DSGVO. Das berechtigte Interesse liegt
							darin, in zusammengefasster Form zu verstehen, welche Inhalte genutzt werden.
						</p>
					</section>

					<section class="privacy-section" aria-labelledby="privacy-logs">
						<h2 id="privacy-logs">3. Hosting und Server-Logfiles</h2>
						<p>
							Beim Aufruf der Website verarbeitet der Hosting-Dienstleister technisch
							erforderliche Verbindungsdaten, damit die Seite ausgeliefert und vor Missbrauch
							geschützt werden kann. Dazu können insbesondere IP-Adresse, Datum und Uhrzeit,
							angeforderte URL, Referrer, Browsertyp und Betriebssystem gehören.
						</p>
						<p>
							Die Verarbeitung erfolgt auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO.
							Das berechtigte Interesse liegt in der sicheren, stabilen und performanten
							Bereitstellung der Website. Die konkrete Speicherdauer richtet sich nach dem
							jeweiligen Hosting- und Sicherheits-Setup.
						</p>
					</section>

					<section class="privacy-section" aria-labelledby="privacy-contact">
						<h2 id="privacy-contact">4. Kontaktaufnahme und Formulare</h2>
						<p>
							Wenn Sie uns per E-Mail kontaktieren oder ein Formular absenden, verarbeiten wir
							die von Ihnen übermittelten Daten ausschließlich zur Bearbeitung Ihrer Anfrage.
							Das betrifft insbesondere Kontakt- und Projektangaben, die Sie selbst eingeben.
						</p>

						<h3>Speicherung von Anfragen und Herkunft</h3>
						<p>
							Anfragen aus den Formularen dieser Website (Projekt-, White-Label- und
							Marktcheck-Anfragen) werden intern im WordPress-Backend gespeichert, damit
							wir sie bearbeiten und den Verlauf nachvollziehen können. Zusammen mit der
							Anfrage speichern wir, von wo aus Sie das Formular abgeschickt haben: die Seite
							mit dem Formular, die zuvor aufgerufene Seite dieser Website oder die
							verweisende Website, soweit Ihr Browser sie übermittelt, sowie
							Kampagnenangaben aus dem Link der Formularseite, etwa utm-Parameter. Diese
							Angaben werden erst beim Absenden gelesen; in Ihrem Browser wird dafür nichts
							gespeichert. Die Angabe, wie Sie auf uns aufmerksam geworden sind, ist
							freiwillig.
						</p>
						<p>
							Rechtsgrundlage für die Herkunftsangaben ist Art. 6 Abs. 1 lit. f DSGVO.
							Das berechtigte Interesse liegt darin, zu verstehen, welche Inhalte zu
							Anfragen führen.
						</p>

						<h3>E-Mail-Zustellung über Brevo</h3>
						<p>
							Für die technische Zustellung unserer E-Mails, Formularbestätigungen und sonstigen
							Transaktionsmails nutzen wir Brevo, einen Dienst der Sendinblue SAS,
							106 boulevard Haussmann, 75008 Paris, Frankreich.
						</p>
						<p>
							Dabei werden insbesondere E-Mail-Adresse, Absender- und Empfängerinformationen,
							Versandzeitpunkte, technische Zustellinformationen sowie die für die jeweilige
							Nachricht erforderlichen Inhalte über Brevo verarbeitet. Soweit Brevo für uns
							als technischer Versanddienstleister tätig wird, erfolgt dies im Rahmen einer
							Auftragsverarbeitung gemäß Art. 28 DSGVO.
						</p>
						<p>
							Weitere Informationen finden Sie in den
							<a href="https://www.brevo.com/blog/legal/privacypolicy/" target="_blank" rel="noopener noreferrer">Datenschutzhinweisen von Brevo</a>.
						</p>

							<h3>Marktcheck-Anfrage</h3>
						<p>
								Auf der Seite zum Marktcheck können Sie eine Anfrage stellen. Dabei werden
							insbesondere Name, geschäftliche E-Mail-Adresse, Unternehmen, URL der zu
							prüfenden Seite sowie Ihre inhaltlichen Angaben zur Anfrage verarbeitet.
						</p>
						<p>
							Die Daten werden intern im WordPress-Backend gespeichert und für die
							Bearbeitung der Anfrage und für die Zustellung der Transaktionsmails
							über Brevo weiterverarbeitet. Rechtsgrundlage ist
							Art. 6 Abs. 1 lit. b DSGVO, soweit es um vorvertragliche Kommunikation geht,
							und im Uebrigen Art. 6 Abs. 1 lit. f DSGVO.
						</p>

						<h3>Feedback zu Beiträgen</h3>
						<p>
							Unter Blogbeiträgen können Sie angeben, ob Ihnen ein Beitrag geholfen hat, und
							freiwillig einen kurzen Text schreiben. Wir zählen die Bewertung am Beitrag und
							speichern einen Text zusammen mit Datum und einem gekürzten Hashwert Ihrer
							IP-Adresse; je Beitrag bleiben höchstens die letzten 25 Texte erhalten. Der
							Hashwert dient dazu, Mehrfachabgaben zu begrenzen, die IP-Adresse selbst wird
							nicht gespeichert. In Ihrem Browser wird dafür nichts gespeichert.
							Rechtsgrundlage ist Art. 6 Abs. 1 lit. f DSGVO. Das berechtigte Interesse liegt
							darin, Beiträge anhand der Rückmeldungen zu verbessern.
						</p>

						<h3>Missbrauchsschutz</h3>
						<p>
							Bei Formularanfragen kann die IP-Adresse kurzfristig verarbeitet werden, um
							Spam und missbraeuchliche Serienanfragen zu begrenzen. Rechtsgrundlage ist
							Art. 6 Abs. 1 lit. f DSGVO. Das berechtigte Interesse liegt im Schutz der
							Website und der Kommunikationskanaele.
						</p>

						<h3>Speicherdauer</h3>
						<p>
							Anfragedaten speichern wir nur so lange, wie es für die Bearbeitung, für
							Anschlusskommunikation oder zur Erfüllung gesetzlicher Pflichten erforderlich ist.
						</p>
					</section>

					<section class="privacy-section" aria-labelledby="privacy-links">
						<h2 id="privacy-links">5. Externe Links und Drittseiten</h2>
						<p>
							Diese Website verlinkt an einzelnen Stellen auf externe Angebote, zum Beispiel
							auf Cal.com für Terminbuchungen sowie auf Profile bei LinkedIn oder
							GitHub. Solche Inhalte werden nicht automatisch eingebettet. Eine Datenübertragung
							an den jeweiligen Anbieter findet daher regelmäßig erst statt, wenn Sie den
							Link aktiv anklicken.
						</p>
						<p>
							Für die Datenverarbeitung auf den verlinkten Drittseiten sind ausschließlich
							deren Betreiber verantwortlich. Bitte beachten Sie die jeweiligen
							Datenschutzhinweise der Anbieter.
						</p>
					</section>

					<section class="privacy-section" aria-labelledby="privacy-security">
						<h2 id="privacy-security">6. Sicherheit</h2>
						<p>
							Diese Website nutzt eine verschlüsselte Verbindung per HTTPS, damit übermittelte
							Daten während der Übertragung geschützt sind. Vollständige Sicherheit kann
							bei Internetkommunikation dennoch nie garantiert werden.
						</p>
					</section>

					<section class="privacy-section" id="rechte" aria-labelledby="privacy-rights">
						<h2 id="privacy-rights">7. Ihre Rechte</h2>
						<p>
							Sie haben nach Maßgabe der DSGVO insbesondere folgende Rechte:
						</p>
						<ul>
							<li>Auskunft über die verarbeiteten personenbezogenen Daten</li>
							<li>Berichtigung unrichtiger Daten</li>
							<li>Loeschung, soweit keine gesetzlichen Pflichten entgegenstehen</li>
							<li>Einschraenkung der Verarbeitung</li>
							<li>Widerspruch gegen Verarbeitungen auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO</li>
							<li>Datenübertragbarkeit, soweit anwendbar</li>
							<li>Beschwerde bei einer Datenschutz-Aufsichtsbehörde</li>
						</ul>
						<p>
							Zuständig ist insbesondere die Aufsichtsbehörde Ihres üblichen Aufenthaltsorts
							oder die für Niedersachsen zuständige Datenschutzaufsicht.
						</p>
					</section>
				</div>
			</div>
		</div>
	</div>
	<?php
endwhile;

get_footer();
