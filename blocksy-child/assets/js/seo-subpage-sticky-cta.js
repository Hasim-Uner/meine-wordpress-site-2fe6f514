/**
 * Mobile Sticky CTA for SEO subpages plus the Server-Side-Tracking funnel
 * enhancement. No external dependencies; all route changes are progressive.
 */
( function () {
	'use strict';

	var MOBILE_MAX_WIDTH = 760;
	var SHOW_AFTER_SCROLL_PX = 240;
	// Wegklicken gilt fuer diesen Seitenaufruf; im Browser wird nichts gespeichert.
	var closed = false;

	function isMobile() {
		return window.matchMedia( '(max-width: ' + MOBILE_MAX_WIDTH + 'px)' ).matches;
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
		if ( ! bar || ! isMobile() ) return;

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
			closed = true;
			hide( bar );
		} );

		if ( target && typeof window.IntersectionObserver === 'function' ) {
			new window.IntersectionObserver( function ( entries ) {
				targetVisible = Boolean( entries[0] && entries[0].isIntersecting );
				if ( targetVisible ) hide( bar );
				else if ( shown && isMobile() && ! closed ) show( bar );
			}, { threshold: 0.08 } ).observe( target );
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', function () {
			if ( ! isMobile() ) hide( bar );
			else if ( shown && ! targetVisible && ! closed ) show( bar );
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

	function protocolRow( label, value, meta, modifier ) {
		var row = el( 'article', 'hu-sst-verify__lane ' + modifier );
		var head = el( 'div', 'hu-sst-verify__lane-head' );
		head.appendChild( el( 'span', 'hu-sst-verify__lane-label', label ) );
		head.appendChild( el( 'span', 'hu-sst-verify__lane-state', modifier === 'is-reality' ? 'Referenz' : 'Messung' ) );
		row.appendChild( head );
		var metric = el( 'div', 'hu-sst-verify__metric' );
		metric.appendChild( el( 'strong', '', value ) );
		metric.appendChild( el( 'span', '', 'Leads' ) );
		row.appendChild( metric );
		row.appendChild( el( 'p', 'hu-sst-verify__lane-meta', meta ) );
		return row;
	}

	function buildHeroProtocol( root ) {
		var figure = root.querySelector( '#hero .hu-sst__decision' );
		if ( ! figure || figure.getAttribute( 'data-verification-console' ) === 'true' ) return;
		figure.setAttribute( 'data-verification-console', 'true' );
		figure.classList.add( 'hu-sst-verify' );
		figure.textContent = '';
		figure.setAttribute( 'aria-label', 'Beispiel eines Messprotokolls: tatsächliche CRM-Leads im Vergleich mit bestehendem Browser-Tracking und neuer serverseitiger Messung.' );

		var topbar = el( 'div', 'hu-sst-verify__topbar' );
		var brand = el( 'div', 'hu-sst-verify__brand' );
		brand.appendChild( el( 'span', 'hu-sst-verify__pulse', '' ) );
		brand.appendChild( el( 'span', '', 'MESSPROTOKOLL / LEAD-RECONCILIATION' ) );
		topbar.appendChild( brand );
		topbar.appendChild( el( 'span', 'hu-sst-verify__mode', 'BEISPIEL' ) );
		figure.appendChild( topbar );

		var intro = el( 'div', 'hu-sst-verify__intro' );
		intro.appendChild( el( 'p', 'hu-sst-verify__kicker', 'Gegenprobe vor Umschaltung' ) );
		intro.appendChild( el( 'h2', 'hu-sst-verify__title', 'Ein Lead. Drei Schienen. Eine Referenz.' ) );
		figure.appendChild( intro );

		var event = el( 'div', 'hu-sst-verify__event' );
		event.appendChild( el( 'span', 'hu-sst-verify__event-dot', '' ) );
		event.appendChild( el( 'code', '', 'lead_submit' ) );
		event.appendChild( el( 'span', 'hu-sst-verify__event-id', 'correlation_id · 8F29-C7' ) );
		figure.appendChild( event );

		var lanes = el( 'div', 'hu-sst-verify__lanes' );
		lanes.appendChild( protocolRow( 'Realität / CRM', '31', '100 % · tatsächlich eingegangen', 'is-reality' ) );
		lanes.appendChild( protocolRow( 'Bestand / Browser', '27', '87 % · vier Leads fehlen', 'is-legacy' ) );
		lanes.appendChild( protocolRow( 'Neue Server-Strecke', '30', '97 % · ein Lead offen', 'is-server' ) );
		figure.appendChild( lanes );

		var footer = el( 'div', 'hu-sst-verify__footer' );
		var result = el( 'div', 'hu-sst-verify__result' );
		result.appendChild( el( 'span', '', 'Differenz zur Referenz' ) );
		result.appendChild( el( 'strong', '', 'Bestand −4 · Server −1' ) );
		footer.appendChild( result );
		var gate = el( 'div', 'hu-sst-verify__gate' );
		gate.appendChild( el( 'span', 'hu-sst-verify__gate-dot', '' ) );
		gate.appendChild( el( 'strong', '', 'PRÜFBAR' ) );
		footer.appendChild( gate );
		figure.appendChild( footer );

		figure.appendChild( el( 'figcaption', 'hu-sst-verify__caption', 'Beispieldaten zur Darstellung des Prüfprinzips. Im Projekt werden ausschließlich reale Kundendaten und technisch erklärbare Abweichungen dokumentiert.' ) );
	}

	function hero( root ) {
		var eyebrow = root.querySelector( '#hero .hu-sst__eyebrow' );
		var h1 = root.querySelector( '#hero .hu-sst__h1' );
		var lead = root.querySelector( '#hero .hu-sst__lead' );
		var sub = root.querySelector( '#hero .hu-sst__lead-sub' );
		if ( eyebrow ) eyebrow.textContent = 'Tracking Verification · Server-Side Tracking';
		if ( h1 ) h1.textContent = 'Server-Side Tracking, das sich an echten Leads beweisen muss.';
		if ( lead ) lead.textContent = 'Formular oder CRM ist die Referenz. Bestehendes Browser-Tracking und neue Server-Strecke laufen im Testzeitraum daneben. Erst wenn die Abweichungen erklärt sind, wird umgestellt.';
		if ( sub ) sub.textContent = 'Sie erhalten nicht nur Container und Tags, sondern Messplan, Parallelprüfung, Abnahmeprotokoll und dokumentierte Übergabe.';

		var proofItems = root.querySelectorAll( '#hero .hu-sst__proof-strip > div' );
		if ( proofItems.length >= 3 ) {
			var terms = [
				[ 'Referenz', 'Formular / CRM' ],
				[ 'Gegenprobe', 'Bestand ↔ Server' ],
				[ 'Abnahme', 'erst prüfen, dann umstellen' ]
			];
			Array.prototype.forEach.call( proofItems, function ( item, index ) {
				var dt = item.querySelector( 'dt' );
				var dd = item.querySelector( 'dd' );
				if ( terms[index] && dt && dd ) {
					dt.textContent = terms[index][0];
					dd.textContent = terms[index][1];
				}
			} );
		}

		var cta = root.querySelector( '#hero .hu-sst__cta' );
		if ( cta && ! root.querySelector( '.hu-sst__hero-microcopy' ) ) {
			cta.insertAdjacentElement( 'afterend', el( 'p', 'hu-sst__cta-note hu-sst__hero-microcopy', 'Erste Einordnung ohne Zugangsdaten. Technische Zugänge werden erst nach geklärtem Scope benötigt.' ) );
		}
		buildHeroProtocol( root );
	}

	function proof( root ) {
		if ( root.querySelector( '#proof' ) ) return;
		var anchor = root.querySelector( '#symptome' );
		if ( ! anchor ) return;

		var section = el( 'section', 'hu-sst__band hu-sst__band--light hu-sst__band--white hu-sst__proof-zone hu-sst-reconcile' );
		section.id = 'proof';
		section.setAttribute( 'data-nx-theme', 'light' );
		section.setAttribute( 'aria-labelledby', 'hu-sst-proof-title' );
		var container = el( 'div', 'hu-sst__container' );
		var head = el( 'div', 'hu-sst__section-head' );
		head.appendChild( el( 'p', 'hu-sst__eyebrow', '02 · Gegenprobe' ) );
		var heading = el( 'h2', 'hu-sst__h2', 'Nicht Tracking gegen Tracking. Messung gegen Realität.' );
		heading.id = 'hu-sst-proof-title';
		head.appendChild( heading );
		head.appendChild( el( 'p', 'hu-sst__section-lead', 'Der tatsächliche Formular- oder CRM-Eingang ist die Referenz. Bestand und neue Server-Strecke werden gegen denselben Lead und denselben Zeitraum geprüft.' ) );
		container.appendChild( head );

		var layout = el( 'div', 'hu-sst-reconcile__layout' );
		var report = el( 'article', 'hu-sst-reconcile__report' );
		var reportTop = el( 'div', 'hu-sst-reconcile__report-top' );
		reportTop.appendChild( el( 'span', 'hu-sst-reconcile__report-label', 'ABNAHMEPROTOKOLL / BEISPIEL' ) );
		reportTop.appendChild( el( 'span', 'hu-sst-reconcile__report-state', 'Lead-Reconciliation' ) );
		report.appendChild( reportTop );
		report.appendChild( el( 'h3', 'hu-sst-reconcile__title', 'Gleicher Lead. Gleiche Referenz.' ) );
		report.appendChild( el( 'p', 'hu-sst-reconcile__period', 'Beispielzeitraum · lead_submit · identische correlation_id' ) );

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
			rows.appendChild( row );
		} );
		report.appendChild( rows );

		var delta = el( 'div', 'hu-sst-reconcile__delta' );
		delta.appendChild( el( 'span', '', 'Abweichung zur Realität' ) );
		delta.appendChild( el( 'strong', '', 'Alt −4 / Neu −1' ) );
		report.appendChild( delta );
		report.appendChild( el( 'p', 'hu-sst-reconcile__disclaimer', 'Beispieldaten. Entscheidend ist im Projekt nicht eine pauschale Prozentzahl, sondern ob jede Abweichung technisch erklärbar ist.' ) );
		layout.appendChild( report );

		var explanation = el( 'div', 'hu-sst-reconcile__explain' );
		[
			[ '01', 'Referenz', 'Tatsächlich eingegangene Leads aus Formular oder CRM.' ],
			[ '02', 'Bestand', 'Das bisherige Tracking bleibt während der Gegenprobe unangetastet.' ],
			[ '03', 'Neue Strecke', 'Server-Signale werden daneben gemessen und erst nach der Prüfung freigegeben.' ]
		].forEach( function ( item ) {
			var row = el( 'article', 'hu-sst-reconcile__explain-card' );
			row.appendChild( el( 'span', 'hu-sst-reconcile__num', item[0] ) );
			row.appendChild( el( 'h3', '', item[1] ) );
			row.appendChild( el( 'p', '', item[2] ) );
			explanation.appendChild( row );
		} );
		layout.appendChild( explanation );
		container.appendChild( layout );
		section.appendChild( container );
		anchor.insertAdjacentElement( 'afterend', section );
	}

	function decisionPath( root ) {
		var principle = root.querySelector( '#unterschied' );
		var architecture = root.querySelector( '#architektur' );
		var flow = architecture ? architecture.querySelector( '.hu-sst__flow' ) : null;
		var container = principle ? principle.querySelector( '.hu-sst__container' ) : null;

		if ( flow && container ) {
			var head = el( 'div', 'hu-sst__section-head hu-sst__mechanism-head' );
			head.appendChild( el( 'p', 'hu-sst__eyebrow', 'Technischer Datenfluss' ) );
			head.appendChild( el( 'h2', 'hu-sst__h2', 'Browser → eigener Endpunkt → Server → Plattform.' ) );
			container.appendChild( head );
			container.appendChild( flow );
			architecture.remove();
		}

		var symptomHead = root.querySelector( '#symptome .hu-sst__section-head' );
		if ( symptomHead ) {
			var sEyebrow = symptomHead.querySelector( '.hu-sst__eyebrow' );
			var sTitle = symptomHead.querySelector( '.hu-sst__h2' );
			if ( sEyebrow ) sEyebrow.textContent = '01 · Befund';
			if ( sTitle ) sTitle.textContent = 'Wenn CRM und Werbeplattformen verschiedene Wahrheiten erzählen.';
		}

		var compareHead = root.querySelector( '#unterschied .hu-sst__section-head' );
		if ( compareHead ) {
			var eyebrow = compareHead.querySelector( '.hu-sst__eyebrow' );
			var h2 = compareHead.querySelector( '.hu-sst__h2' );
			if ( eyebrow ) eyebrow.textContent = '03 · System';
			if ( h2 ) h2.textContent = 'Browser bleibt wichtig. Der Datenweg wird kontrollierbar.';
		}

		var scopeHead = root.querySelector( '#umfang .hu-sst__section-head' );
		if ( scopeHead ) {
			var scopeEyebrow = scopeHead.querySelector( '.hu-sst__eyebrow' );
			var scopeTitle = scopeHead.querySelector( '.hu-sst__h2' );
			var scopeLead = scopeHead.querySelector( '.hu-sst__section-lead' );
			if ( scopeEyebrow ) scopeEyebrow.textContent = 'Lieferumfang';
			if ( scopeTitle ) scopeTitle.textContent = 'Was Sie am Ende tatsächlich besitzen.';
			if ( scopeLead ) scopeLead.textContent = 'Keine lose Tag-Sammlung: sechs nachvollziehbare Arbeitsergebnisse von Messplan und Server-GTM bis Gegenprobe und Übergabe.';
		}

		var fit = root.querySelector( '#fit' );
		var packages = root.querySelector( '#pakete' );
		if ( fit && packages && fit.nextElementSibling !== packages ) packages.parentNode.insertBefore( fit, packages );

		var list = root.querySelector( '.hu-sst__toc-list' );
		if ( list ) {
			list.textContent = '';
			[
				[ 'symptome', 'Befund', 'toc_problem' ],
				[ 'proof', 'Gegenprobe', 'toc_verification' ],
				[ 'unterschied', 'System', 'toc_system' ],
				[ 'pakete', 'Leistung & Preis', 'toc_packages' ],
				[ 'anfrage', 'Anfrage', 'toc_request' ]
			].forEach( function ( item ) {
				var li = el( 'li' );
				var a = el( 'a', '', item[1] );
				a.href = '#' + item[0];
				track( a, item[2], 'toc' );
				li.appendChild( a );
				list.appendChild( li );
			} );
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
				if ( head ) head.insertAdjacentElement( 'afterend', el( 'p', 'hu-sst__section-lead hu-sst__care-summary', 'Optional nach Go-live: Funktionstest, kleinere Korrekturen und Meldung bei Auffälligkeiten · ab ' + ( firstPrice ? firstPrice.textContent.trim() : '99 € / Monat' ) ) );
				var details = el( 'details', 'hu-sst__faq-item' );
				details.appendChild( el( 'summary', 'hu-sst__faq-q', 'Tracking Care im Detail' ) );
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
			var eyebrow = packagesHead.querySelector( '.hu-sst__eyebrow' );
			var title = packagesHead.querySelector( '.hu-sst__h2' );
			var lead = packagesHead.querySelector( '.hu-sst__section-lead' );
			if ( eyebrow ) eyebrow.textContent = '04 · Leistung & Preis';
			if ( title ) title.textContent = 'Drei Ausbaustufen. Ein Prüfprinzip.';
			if ( lead ) lead.textContent = 'Der Unterschied liegt im Umfang der angeschlossenen Systeme. Messplan, Server-Strecke, Gegenprobe und dokumentierte Übergabe bleiben das gemeinsame Fundament.';
		}

		var cards = root.querySelectorAll( '#pakete .hu-sst__price-card' );
		if ( cards.length >= 3 ) {
			var individualName = cards[2].querySelector( '.hu-sst__price-name' );
			var individualLead = cards[2].querySelector( '.hu-sst__price-lead' );
			if ( individualName ) individualName.textContent = 'Lead-to-Revenue · CRM';
			if ( individualLead ) individualLead.textContent = 'Für B2B-Leadstrecken, bei denen qualifizierte Leads, Termine oder gewonnene Aufträge zurück in die Werbeplattformen sollen.';
		}

		Array.prototype.slice.call( cards ).forEach( function ( card ) {
			var lead = card.querySelector( '.hu-sst__price-lead' );
			if ( lead && ! lead.querySelector( 'strong' ) ) lead.insertBefore( el( 'strong', '', 'Geeignet für' ), lead.firstChild );
		} );

		var requestHead = root.querySelector( '#anfrage .hu-sst__section-head' );
		if ( requestHead ) {
			var requestEyebrow = requestHead.querySelector( '.hu-sst__eyebrow' );
			if ( requestEyebrow ) requestEyebrow.textContent = '05 · Anfrage';
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

		var status = el( 'p', 'hu-sst__package-selection' );
		status.hidden = true;
		if ( details ) form.insertBefore( status, details );

		Object.keys( labels ).forEach( function ( action ) {
			var button = root.querySelector( '[data-track-action="' + action + '"]' );
			if ( ! button ) return;
			button.addEventListener( 'click', function () {
				var label = labels[action];
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
		if ( firstRow && ! form.querySelector( '.hu-sst__form-intro-hint' ) ) firstRow.insertAdjacentElement( 'beforebegin', el( 'p', 'hu-sst__form-intro-hint', 'Keine Zugangsdaten erforderlich. Für die erste Einordnung reichen Website und Messproblem.' ) );
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
		add( 'Was bedeutet „Realität“ im Vergleich?', 'Als Referenz dienen die tatsächlich im Formular oder CRM eingegangenen Leads. Diese Zahl wird nicht aus GA4, Google Ads oder Meta übernommen.' );
		add( 'Muss ich mein bestehendes Tracking abschalten?', 'Nein. Während der Gegenprobe bleibt die bisherige Messung aktiv. Die neue Server-Strecke läuft daneben. Erst nach der Prüfung wird entschieden, welche direkte Altstrecke abgeschaltet werden kann.' );
		add( 'Was passiert nach dem Setup?', 'Sie erhalten Messplan, benannte GTM-Versionen, dokumentierten Datenfluss und die vereinbarten Zugänge. Der Vergleich von Referenz, Bestand und neuer Strecke wird dokumentiert.' );

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
			if ( target ) map[id] = { target: target, link: link };
		} );
		var observer = new window.IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting || ! map[entry.target.id] ) return;
				links.forEach( function ( link ) { link.removeAttribute( 'aria-current' ); } );
				map[entry.target.id].link.setAttribute( 'aria-current', 'location' );
			} );
		}, { rootMargin: '-28% 0px -62% 0px', threshold: 0 } );
		Object.keys( map ).forEach( function ( id ) { observer.observe( map[id].target ); } );
	}

	function init() {
		var root = document.querySelector( ROOT );
		if ( ! root || root.getAttribute( 'data-sst-funnel-ready' ) === 'true' ) return;
		root.setAttribute( 'data-sst-funnel-ready', 'true' );
		root.setAttribute( 'data-sst-design', 'protocol' );
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
