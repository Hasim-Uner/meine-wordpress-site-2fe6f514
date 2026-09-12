/**
 * Mobile Sticky CTA for SEO subpages plus the Server-Side-Tracking funnel
 * enhancement. No external dependencies; all route changes are progressive.
 */
( function () {
	'use strict';

	var STORAGE_PREFIX = 'huStickyCta_dismissed_';
	var DISMISS_WINDOW_MS = 24 * 60 * 60 * 1000;
	var MOBILE_MAX_WIDTH = 760;
	var SHOW_AFTER_SCROLL_PX = 240;

	function isMobile() {
		return window.matchMedia( '(max-width: ' + MOBILE_MAX_WIDTH + 'px)' ).matches;
	}

	function dismissKey() {
		return STORAGE_PREFIX + ( window.location.pathname || '/' );
	}

	function dismissed() {
		try {
			var raw = window.localStorage.getItem( dismissKey() );
			return raw ? ( Date.now() - parseInt( raw, 10 ) ) < DISMISS_WINDOW_MS : false;
		} catch ( e ) {
			return false;
		}
	}

	function rememberDismissal() {
		try {
			window.localStorage.setItem( dismissKey(), String( Date.now() ) );
		} catch ( e ) {}
	}

	function setBodyOffset( bar ) {
		if ( ! document.body ) return;
		var height = bar.getBoundingClientRect().height;
		if ( height > 0 ) document.body.style.setProperty( '--hu-sticky-cta-h', height + 'px' );
	}

	function clearBodyOffset() {
		if ( document.body ) document.body.style.removeProperty( '--hu-sticky-cta-h' );
	}

	function show( bar ) {
		if ( ! bar.hasAttribute( 'hidden' ) ) return;
		bar.removeAttribute( 'hidden' );
		void bar.offsetWidth;
		bar.classList.add( 'is-visible' );
		setBodyOffset( bar );
	}

	function hide( bar ) {
		bar.classList.remove( 'is-visible' );
		bar.setAttribute( 'hidden', '' );
		clearBodyOffset();
	}

	function initSticky() {
		var bar = document.getElementById( 'hu-sticky-cta' );
		if ( ! bar || ! isMobile() || dismissed() ) return;

		var shown = false;
		var targetVisible = false;
		var selector = bar.getAttribute( 'data-sticky-hide-when-visible' );
		var target = null;
		try { target = selector ? document.querySelector( selector ) : null; } catch ( e ) {}

		function onScroll() {
			if ( shown || window.scrollY < SHOW_AFTER_SCROLL_PX ) return;
			shown = true;
			if ( ! targetVisible ) show( bar );
		}

		var close = bar.querySelector( '.hu-sticky-cta__close' );
		if ( close ) close.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			rememberDismissal();
			hide( bar );
		} );

		if ( target && typeof window.IntersectionObserver === 'function' ) {
			new window.IntersectionObserver( function ( entries ) {
				targetVisible = Boolean( entries[0] && entries[0].isIntersecting );
				if ( targetVisible ) hide( bar );
				else if ( shown && isMobile() && ! dismissed() ) show( bar );
			}, { threshold: 0.08 } ).observe( target );
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', function () {
			if ( ! isMobile() ) hide( bar );
			else if ( shown && ! targetVisible && ! dismissed() ) show( bar );
		} );
		onScroll();
	}

	if ( document.readyState === 'loading' ) document.addEventListener( 'DOMContentLoaded', initSticky );
	else initSticky();
} )();

( function () {
	'use strict';

	var ROOT = '.hu-sst[data-track-page="server-side-tracking-b2b"]';

	function el( tag, className, text ) {
		var node = document.createElement( tag );
		if ( className ) node.className = className;
		if ( typeof text === 'string' ) node.textContent = text;
		return node;
	}

	function track( node, action, section ) {
		node.setAttribute( 'data-track-action', action );
		node.setAttribute( 'data-track-category', 'server_side_tracking_b2b' );
		node.setAttribute( 'data-track-section', section );
	}

	function stat( label, value, meta, modifier ) {
		var card = el( 'article', 'hu-sst-verify__lane' + ( modifier ? ' ' + modifier : '' ) );
		var head = el( 'div', 'hu-sst-verify__lane-head' );
		head.appendChild( el( 'span', 'hu-sst-verify__lane-label', label ) );
		head.appendChild( el( 'span', 'hu-sst-verify__lane-state', modifier === 'is-reality' ? 'Referenz' : 'gemessen' ) );
		card.appendChild( head );
		var metric = el( 'div', 'hu-sst-verify__metric' );
		metric.appendChild( el( 'strong', '', value ) );
		metric.appendChild( el( 'span', '', 'Leads' ) );
		card.appendChild( metric );
		var bar = el( 'div', 'hu-sst-verify__bar' );
		bar.setAttribute( 'aria-hidden', 'true' );
		bar.appendChild( el( 'span', '' ) );
		card.appendChild( bar );
		card.appendChild( el( 'p', 'hu-sst-verify__lane-meta', meta ) );
		return card;
	}

	function checkRow( label, value, strong ) {
		var row = el( 'div', 'hu-sst-verify__check' + ( strong ? ' is-strong' : '' ) );
		row.appendChild( el( 'span', 'hu-sst-verify__check-dot', '' ) );
		row.appendChild( el( 'span', 'hu-sst-verify__check-label', label ) );
		row.appendChild( el( 'strong', 'hu-sst-verify__check-value', value ) );
		return row;
	}

	function buildHeroConsole( root ) {
		var figure = root.querySelector( '#hero .hu-sst__decision' );
		if ( ! figure || figure.getAttribute( 'data-verification-console' ) === 'true' ) return;
		figure.setAttribute( 'data-verification-console', 'true' );
		figure.classList.add( 'hu-sst-verify' );
		figure.textContent = '';
		figure.setAttribute( 'aria-label', 'Beispielansicht eines Vergleichs zwischen tatsächlichen CRM-Leads, bestehendem Browser-Tracking und neuer serverseitiger Messung.' );

		var topbar = el( 'div', 'hu-sst-verify__topbar' );
		var brand = el( 'div', 'hu-sst-verify__brand' );
		brand.appendChild( el( 'span', 'hu-sst-verify__pulse', '' ) );
		brand.appendChild( el( 'span', '', 'TRACKING VERIFICATION' ) );
		topbar.appendChild( brand );
		topbar.appendChild( el( 'span', 'hu-sst-verify__mode', 'TESTBETRIEB' ) );
		figure.appendChild( topbar );

		var titleWrap = el( 'div', 'hu-sst-verify__intro' );
		titleWrap.appendChild( el( 'p', 'hu-sst-verify__kicker', 'Drei Schienen · ein Vergleich' ) );
		titleWrap.appendChild( el( 'h2', 'hu-sst-verify__title', 'Wie nah liegt die Messung an der Realität?' ) );
		figure.appendChild( titleWrap );

		var event = el( 'div', 'hu-sst-verify__event' );
		event.appendChild( el( 'span', 'hu-sst-verify__event-dot', '' ) );
		event.appendChild( el( 'code', '', 'lead_submit' ) );
		event.appendChild( el( 'span', 'hu-sst-verify__event-id', 'correlation_id · 8F29-C7' ) );
		figure.appendChild( event );

		var lanes = el( 'div', 'hu-sst-verify__lanes' );
		lanes.appendChild( stat( 'Realität · CRM/Formular', '31', '31 echte Eingänge · Referenzwert', 'is-reality' ) );
		lanes.appendChild( stat( 'Bestehendes Tracking', '27', '87 % der tatsächlichen Leads erkannt', 'is-legacy' ) );
		lanes.appendChild( stat( 'Neue Server-Strecke', '30', '97 % der tatsächlichen Leads erkannt', 'is-server' ) );
		figure.appendChild( lanes );

		var checks = el( 'div', 'hu-sst-verify__checks' );
		checks.appendChild( checkRow( 'CRM-Abgleich', '31 / 31', false ) );
		checks.appendChild( checkRow( 'Browser-Signal', '27 / 31', false ) );
		checks.appendChild( checkRow( 'Server-Signal', '30 / 31', true ) );
		checks.appendChild( checkRow( 'Consent & Deduplizierung', 'geprüft', false ) );
		figure.appendChild( checks );

		var footer = el( 'div', 'hu-sst-verify__footer' );
		var result = el( 'div', 'hu-sst-verify__result' );
		result.appendChild( el( 'span', '', 'Messnähe zur Realität' ) );
		result.appendChild( el( 'strong', '', 'Neu +10 Prozentpunkte' ) );
		footer.appendChild( result );
		var gate = el( 'div', 'hu-sst-verify__gate' );
		gate.appendChild( el( 'span', 'hu-sst-verify__gate-dot', '' ) );
		gate.appendChild( el( 'strong', '', 'READY FOR REVIEW' ) );
		footer.appendChild( gate );
		figure.appendChild( footer );

		figure.appendChild( el( 'figcaption', 'hu-sst-verify__caption', 'Beispieldaten zur Darstellung des Prüfprinzips — keine Kundenergebnisse und kein pauschales Uplift-Versprechen.' ) );
	}

	function hero( root ) {
		var eyebrow = root.querySelector( '#hero .hu-sst__eyebrow' );
		var h1 = root.querySelector( '#hero .hu-sst__h1' );
		var lead = root.querySelector( '#hero .hu-sst__lead' );
		var sub = root.querySelector( '#hero .hu-sst__lead-sub' );
		if ( eyebrow ) eyebrow.textContent = 'Server-Side Tracking · B2B-Leadgenerierung';
		if ( h1 ) h1.textContent = 'Server-Side Tracking, das sich gegen Ihre echten Leads prüfen lässt';
		if ( lead ) lead.textContent = 'Ich baue die neue Messstrecke zunächst neben dem Bestand auf. CRM oder Formular bilden die Realität; bestehendes Browser-Tracking und neue Server-Strecke werden dagegen verglichen.';
		if ( sub ) sub.textContent = 'Erst wenn Abweichungen erklärbar sind, wird umgestellt. GA4, Google Ads und optional Meta CAPI laufen in Ihren Konten — mit eigener Tracking-Subdomain und dokumentierter Übergabe.';

		var proofItems = root.querySelectorAll( '#hero .hu-sst__proof-strip > div' );
		if ( proofItems.length >= 3 ) {
			var terms = [
				[ 'Referenz', 'CRM / Formular als Ground Truth' ],
				[ 'Vergleich', 'Realität ↔ Bestand ↔ Server' ],
				[ 'Eigentum', 'Konten und Hosting bleiben bei Ihnen' ]
			];
			Array.prototype.forEach.call( proofItems, function ( item, index ) {
				var dt = item.querySelector( 'dt' );
				var dd = item.querySelector( 'dd' );
				if ( terms[ index ] && dt && dd ) {
					dt.textContent = terms[ index ][0];
					dd.textContent = terms[ index ][1];
				}
			} );
		}

		var cta = root.querySelector( '#hero .hu-sst__cta' );
		if ( cta && ! root.querySelector( '.hu-sst__hero-microcopy' ) ) {
			cta.insertAdjacentElement( 'afterend', el(
				'p',
				'hu-sst__cta-note hu-sst__hero-microcopy',
				'Erste Einordnung ohne Zugangsdaten. Sie erhalten zuerst eine Fit- und Scope-Einschätzung.'
			) );
		}

		buildHeroConsole( root );
	}

	function proof( root ) {
		if ( root.querySelector( '#proof' ) ) return;
		var anchor = root.querySelector( '.hu-sst__toc-band' ) || root.querySelector( '#hero' );
		if ( ! anchor ) return;

		var section = el( 'section', 'hu-sst__band hu-sst__band--light hu-sst__band--white hu-sst__proof-zone hu-sst-reconcile' );
		section.id = 'proof';
		section.setAttribute( 'data-nx-theme', 'light' );
		section.setAttribute( 'aria-labelledby', 'hu-sst-proof-title' );
		var container = el( 'div', 'hu-sst__container' );
		var head = el( 'div', 'hu-sst__section-head' );
		head.appendChild( el( 'p', 'hu-sst__eyebrow', 'Das Prüfprinzip' ) );
		var heading = el( 'h2', 'hu-sst__h2', 'Drei Schienen. Eine Referenz.' );
		heading.id = 'hu-sst-proof-title';
		head.appendChild( heading );
		head.appendChild( el( 'p', 'hu-sst__section-lead', 'Nicht Tracking gegen Tracking vergleichen: Die tatsächlichen Formular- oder CRM-Eingänge sind die Referenz. Dagegen werden Bestand und neue Server-Strecke gemessen.' ) );
		container.appendChild( head );

		var layout = el( 'div', 'hu-sst-reconcile__layout' );
		var report = el( 'article', 'hu-sst-reconcile__report' );
		var reportTop = el( 'div', 'hu-sst-reconcile__report-top' );
		reportTop.appendChild( el( 'span', 'hu-sst-reconcile__report-label', 'VERIFICATION SUMMARY' ) );
		reportTop.appendChild( el( 'span', 'hu-sst-reconcile__report-state', 'Beispiel' ) );
		report.appendChild( reportTop );
		report.appendChild( el( 'h3', 'hu-sst-reconcile__title', 'Lead-Reconciliation' ) );
		report.appendChild( el( 'p', 'hu-sst-reconcile__period', 'Beispielzeitraum · gleicher Lead-Event · identische Referenz' ) );

		var rows = el( 'div', 'hu-sst-reconcile__rows' );
		[
			[ 'Realität / CRM', '31', '100 %', 'is-reality' ],
			[ 'Bestehendes Browser-Tracking', '27', '87 %', 'is-legacy' ],
			[ 'Neue Server-Strecke', '30', '97 %', 'is-server' ]
		].forEach( function ( item ) {
			var row = el( 'div', 'hu-sst-reconcile__row ' + item[3] );
			row.appendChild( el( 'span', 'hu-sst-reconcile__name', item[0] ) );
			row.appendChild( el( 'strong', 'hu-sst-reconcile__count', item[1] ) );
			row.appendChild( el( 'span', 'hu-sst-reconcile__rate', item[2] ) );
			var bar = el( 'span', 'hu-sst-reconcile__mini-bar' );
			bar.appendChild( el( 'i', '' ) );
			row.appendChild( bar );
			rows.appendChild( row );
		} );
		report.appendChild( rows );

		var delta = el( 'div', 'hu-sst-reconcile__delta' );
		delta.appendChild( el( 'span', '', 'Neue Messstrecke näher an der Realität' ) );
		delta.appendChild( el( 'strong', '', '+10 PP' ) );
		report.appendChild( delta );
		report.appendChild( el( 'p', 'hu-sst-reconcile__disclaimer', 'Beispieldaten. Im Projekt werden ausschließlich die realen Kundendaten und technisch erklärbare Abweichungen dokumentiert.' ) );
		layout.appendChild( report );

		var explanation = el( 'div', 'hu-sst-reconcile__explain' );
		[
			[ '01', 'Realität', 'Formular oder CRM zählt den tatsächlich eingegangenen Lead. Das ist die Referenz für den Vergleich.' ],
			[ '02', 'Bestand', 'Das bisherige Browser-Tracking bleibt im Testzeitraum aktiv und wird nur beobachtet.' ],
			[ '03', 'Neue Strecke', 'Die serverseitige Messung läuft parallel. Erst danach wird entschieden, was produktiv bleibt.' ]
		].forEach( function ( item ) {
			var card = el( 'article', 'hu-sst-reconcile__explain-card' );
			card.appendChild( el( 'span', 'hu-sst-reconcile__num', item[0] ) );
			card.appendChild( el( 'h3', '', item[1] ) );
			card.appendChild( el( 'p', '', item[2] ) );
			explanation.appendChild( card );
		} );
		layout.appendChild( explanation );
		container.appendChild( layout );

		var note = el( 'p', 'hu-sst__note hu-sst-reconcile__note' );
		note.appendChild( document.createTextNode( 'Später kann derselbe Vergleich automatisiert in einem Kundenportal laufen. Für das Setup entscheidend ist zuerst die saubere Datenkette — nicht eine zusätzliche Software-Schicht.' ) );
		container.appendChild( note );

		section.appendChild( container );
		anchor.insertAdjacentElement( 'afterend', section );
	}

	function decisionPath( root ) {
		var fit = root.querySelector( '#fit' );
		var principle = root.querySelector( '#unterschied' );
		if ( fit && principle ) principle.parentNode.insertBefore( fit, principle );

		var architecture = root.querySelector( '#architektur' );
		var flow = architecture ? architecture.querySelector( '.hu-sst__flow' ) : null;
		var container = principle ? principle.querySelector( '.hu-sst__container' ) : null;
		if ( flow && container ) {
			var head = el( 'div', 'hu-sst__section-head hu-sst__mechanism-head' );
			head.appendChild( el( 'p', 'hu-sst__eyebrow', 'Kontrollierter Datenfluss' ) );
			head.appendChild( el( 'h2', 'hu-sst__h2', 'So wird aus Browser-Signalen ein nachvollziehbarer Datenfluss' ) );
			container.appendChild( head );
			container.appendChild( flow );
			architecture.remove();
		}

		var list = root.querySelector( '.hu-sst__toc-list' );
		if ( list ) {
			list.textContent = '';
			[
				[ 'proof', 'Vergleich', 'toc_verification' ],
				[ 'unterschied', 'Lösung', 'toc_principle' ],
				[ 'pakete', 'Pakete', 'toc_packages' ],
				[ 'ablauf', 'Ablauf', 'toc_process' ],
				[ 'anfrage', 'Anfrage', 'toc_request' ]
			].forEach( function ( item ) {
				var li = el( 'li' );
				var a = el( 'a', item[0] === 'pakete' ? 'hu-sst__toc-link--strong' : '', item[1] );
				a.href = '#' + item[0];
				track( a, item[2], 'toc' );
				li.appendChild( a );
				list.appendChild( li );
			} );
		}

		var compareHead = root.querySelector( '#unterschied .hu-sst__section-head' );
		if ( compareHead ) {
			var eyebrow = compareHead.querySelector( '.hu-sst__eyebrow' );
			var h2 = compareHead.querySelector( '.hu-sst__h2' );
			if ( eyebrow ) eyebrow.textContent = 'Zielarchitektur';
			if ( h2 ) h2.textContent = 'Browser bleibt wichtig. Der direkte Plattformweg muss es nicht.';
		}
	}

	function collapseLongSections( root ) {
		var care = root.querySelector( '#tracking-care' );
		if ( care && ! care.getAttribute( 'data-cro-compressed' ) ) {
			var careContainer = care.querySelector( '.hu-sst__container' );
			var pricing = care.querySelector( '.hu-sst__care-pricing' );
			var scope = care.querySelector( '.hu-sst__care-scope' );
			var firstPrice = care.querySelector( '.hu-sst__care-price' );
			if ( careContainer && pricing && scope ) {
				care.setAttribute( 'data-cro-compressed', 'true' );
				var head = care.querySelector( '.hu-sst__section-head' );
				if ( head ) head.insertAdjacentElement( 'afterend', el( 'p', 'hu-sst__section-lead hu-sst__care-summary', 'Monatlicher Funktionstest, kleinere Korrekturen und Meldung bei Auffälligkeiten · ab ' + ( firstPrice ? firstPrice.textContent.trim() : '99 € / Monat' ) ) );
				var details = el( 'details', 'hu-sst__faq-item' );
				details.appendChild( el( 'summary', 'hu-sst__faq-q', 'Leistungsumfang Tracking Care ansehen' ) );
				var body = el( 'div', 'hu-sst__faq-a' );
				body.appendChild( pricing );
				body.appendChild( scope );
				details.appendChild( body );
				careContainer.appendChild( details );
			}
		}

		var security = root.querySelector( '#sicherheit' );
		var checklist = security ? security.querySelector( '.hu-sst__checklist' ) : null;
		if ( security && checklist && checklist.children.length > 5 && ! security.getAttribute( 'data-cro-compressed' ) ) {
			security.setAttribute( 'data-cro-compressed', 'true' );
			var detailsSecurity = el( 'details', 'hu-sst__faq-item' );
			detailsSecurity.appendChild( el( 'summary', 'hu-sst__faq-q', 'Weitere technische Sicherheitsmaßnahmen' ) );
			var bodySecurity = el( 'div', 'hu-sst__faq-a' );
			var extra = el( 'ul', 'hu-sst__checklist' );
			while ( checklist.children.length > 5 ) extra.appendChild( checklist.children[5] );
			bodySecurity.appendChild( extra );
			detailsSecurity.appendChild( bodySecurity );
			checklist.insertAdjacentElement( 'afterend', detailsSecurity );
		}
	}

	function packageAndForm( root ) {
		var packagesHead = root.querySelector( '#pakete .hu-sst__section-head' );
		if ( packagesHead ) {
			var title = packagesHead.querySelector( '.hu-sst__h2' );
			var lead = packagesHead.querySelector( '.hu-sst__section-lead' );
			if ( title ) title.textContent = 'Vom verifizierten Setup bis zum CRM-Rücksignal';
			if ( lead ) lead.textContent = 'Server-Side Tracking ist die Infrastruktur. Je nach Paket kommen Meta CAPI, mehrere Conversion-Strecken oder CRM- und Offline-Signale hinzu.';
		}

		var cards = root.querySelectorAll( '#pakete .hu-sst__price-card' );
		if ( cards.length >= 3 ) {
			var individualName = cards[2].querySelector( '.hu-sst__price-name' );
			var individualLead = cards[2].querySelector( '.hu-sst__price-lead' );
			if ( individualName ) individualName.textContent = 'Lead-to-Revenue · CRM';
			if ( individualLead ) individualLead.textContent = 'Für B2B-Leadstrecken, bei denen qualifizierte Leads, Termine oder gewonnene Aufträge zurück in die Werbeplattformen sollen.';
		}

		var form = root.querySelector( '#anfrage form[data-contact-form]' );
		if ( ! form ) return;
		var details = form.querySelector( '.hu-sst__form-details' );
		var setup = form.querySelector( '#contact-tracking-setup' );
		var labels = {
			cta_package_standard: 'Basis · GA4 + Google Ads',
			cta_package_pro: 'Performance · Google + Meta',
			cta_package_individual: 'Lead-to-Revenue · CRM'
		};

		Array.prototype.slice.call( root.querySelectorAll( '#pakete .hu-sst__price-card' ) ).forEach( function ( card ) {
			var lead = card.querySelector( '.hu-sst__price-lead' );
			if ( lead && ! lead.querySelector( 'strong' ) ) lead.insertBefore( el( 'strong', '', 'Ideal für' ), lead.firstChild );
		} );

		var status = el( 'p', 'hu-sst__package-selection' );
		status.hidden = true;
		if ( details ) form.insertBefore( status, details );

		Object.keys( labels ).forEach( function ( action ) {
			var button = root.querySelector( '[data-track-action="' + action + '"]' );
			if ( ! button ) return;
			button.addEventListener( 'click', function () {
				var label = labels[ action ];
				if ( details ) details.open = true;
				status.textContent = '';
				status.appendChild( el( 'strong', '', 'Vorausgewählt: ' ) );
				status.appendChild( document.createTextNode( label ) );
				status.hidden = false;
				if ( setup ) {
					var cleaned = setup.value.replace( /^Paketinteresse:.*(?:\n|$)/, '' );
					setup.value = 'Paketinteresse: ' + label + '\n' + cleaned;
				}
			} );
		} );

		var firstRow = form.querySelector( '.contact-form__row' );
		if ( firstRow && ! form.querySelector( '.hu-sst__form-intro-hint' ) ) firstRow.insertAdjacentElement( 'beforebegin', el( 'p', 'hu-sst__form-intro-hint', 'Keine Zugangsdaten erforderlich. Sie erhalten zunächst eine Fit- und Scope-Einschätzung.' ) );
		if ( details ) {
			var summary = details.querySelector( 'summary' );
			if ( summary ) {
				summary.textContent = 'Technische Angaben ergänzen — optional';
				track( summary, 'form_optional_details_open', 'request_form' );
			}
		}
		var submit = form.querySelector( '[data-contact-submit]' );
		if ( submit ) {
			submit.textContent = 'Setup-Empfehlung anfordern';
			submit.setAttribute( 'data-contact-submit-label', 'Setup-Empfehlung anfordern' );
			submit.setAttribute( 'data-track-action', 'contact_submit_server_side_tracking' );
			submit.setAttribute( 'data-track-funnel-action', 'form_submit_tracking' );
		}
	}

	function faq( root ) {
		var list = root.querySelector( '#faq .hu-sst__faq-list' );
		if ( ! list ) return;
		function add( question, answer ) {
			if ( Array.prototype.some.call( list.querySelectorAll( 'summary' ), function ( node ) { return node.textContent.trim() === question; } ) ) return;
			var item = el( 'details', 'hu-sst__faq-item' );
			item.setAttribute( 'name', 'hu-faq-server-side' );
			item.appendChild( el( 'summary', 'hu-sst__faq-q', question ) );
			var body = el( 'div', 'hu-sst__faq-a' );
			body.appendChild( el( 'p', '', answer ) );
			item.appendChild( body );
			list.appendChild( item );
		}
		add( 'Was bedeutet „Realität“ im Vergleich?', 'Als Referenz dienen die tatsächlich im Formular oder CRM eingegangenen Leads. Diese Zahl wird nicht aus GA4, Google Ads oder Meta übernommen. Bestand und neue Messstrecke werden gegen dieselbe Referenz geprüft.' );
		add( 'Muss ich mein bestehendes Tracking abschalten?', 'Nein. Während des Testbetriebs bleibt die bisherige Messung aktiv. Die neue Server-Strecke läuft daneben. Nach dem Vergleich wird entschieden, welche direkte Altstrecke abgeschaltet werden kann; Browser-Signale bleiben Teil eines hybriden Setups.' );
		add( 'Was passiert nach dem Setup?', 'Sie erhalten Dokumentation, benannte GTM-Versionen und die vereinbarten Zugänge. Der Vergleich von Realität, Bestand und neuer Messstrecke wird dokumentiert. Danach kann das Setup bei Ihnen bleiben oder über Tracking Care kontrolliert werden.' );

		var order = [ 'Was kostet Server-Side Tracking', 'Was bedeutet „Realität“ im Vergleich', 'Muss ich mein bestehendes Tracking abschalten', 'Wann lohnt es sich nicht', 'Wie lange dauert die Einrichtung', 'Funktioniert das mit WordPress', 'Was passiert nach dem Setup', 'Welches Problem löst Server-Side Tracking', 'Was ist der Unterschied zwischen Client-Side und Server-Side Tracking', 'Ist Server-Side Tracking automatisch DSGVO-konform', 'Wie viele Conversions kommen zusätzlich an', 'Brauche ich eine Server-Side-Tracking-Agentur' ];
		var items = Array.prototype.slice.call( list.children );
		items.sort( function ( a, b ) {
			function rank( item ) {
				var text = item.querySelector( 'summary' ) ? item.querySelector( 'summary' ).textContent : '';
				for ( var i = 0; i < order.length; i++ ) if ( text.indexOf( order[i] ) === 0 ) return i;
				return order.length;
			}
			return rank( a ) - rank( b );
		} ).forEach( function ( item ) { list.appendChild( item ); } );
	}

	function activeToc( root ) {
		if ( typeof window.IntersectionObserver !== 'function' ) return;
		var links = Array.prototype.slice.call( root.querySelectorAll( '.hu-sst__toc-list a[href^="#"]' ) );
		if ( ! links.length ) return;
		var map = {};
		links.forEach( function ( link ) {
			var id = link.getAttribute( 'href' ).slice( 1 );
			var target = document.getElementById( id );
			if ( target ) map[ id ] = { target: target, link: link };
		} );
		var observer = new window.IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting || ! map[ entry.target.id ] ) return;
				links.forEach( function ( link ) { link.removeAttribute( 'aria-current' ); } );
				map[ entry.target.id ].link.setAttribute( 'aria-current', 'location' );
			} );
		}, { rootMargin: '-28% 0px -62% 0px', threshold: 0 } );
		Object.keys( map ).forEach( function ( id ) { observer.observe( map[ id ].target ); } );
	}

	function init() {
		var root = document.querySelector( ROOT );
		if ( ! root || root.getAttribute( 'data-sst-funnel-ready' ) === 'true' ) return;
		root.setAttribute( 'data-sst-funnel-ready', 'true' );
		hero( root );
		proof( root );
		decisionPath( root );
		collapseLongSections( root );
		packageAndForm( root );
		faq( root );
		activeToc( root );
	}

	if ( document.readyState === 'loading' ) document.addEventListener( 'DOMContentLoaded', init );
	else init();
} )();
