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

	function hero( root ) {
		var cta = root.querySelector( '#hero .hu-sst__cta' );
		if ( cta && ! root.querySelector( '.hu-sst__hero-microcopy' ) ) {
			cta.insertAdjacentElement( 'afterend', el(
				'p',
				'hu-sst__cta-note hu-sst__hero-microcopy',
				'Erste Einordnung ohne Zugangsdaten. Sie erhalten zuerst eine Fit- und Scope-Einschätzung.'
			) );
		}

		if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches || typeof Element === 'undefined' || ! Element.prototype.animate ) return;
		Array.prototype.slice.call( root.querySelectorAll( '#hero .hu-sst__decision svg path[marker-end]' ) ).forEach( function ( path, index ) {
			path.style.strokeDasharray = '10 8';
			var animation = path.animate(
				[ { opacity: 0.42, strokeDashoffset: '24' }, { opacity: 1, strokeDashoffset: '0' } ],
				{ duration: 880, delay: 180 + index * 180, easing: 'cubic-bezier(0.23, 1, 0.32, 1)', fill: 'both' }
			);
			if ( animation.finished ) animation.finished.then( function () {
				path.style.strokeDasharray = '';
				path.style.strokeDashoffset = '';
				path.style.opacity = '';
			} ).catch( function () {} );
		} );
	}

	function proof( root ) {
		if ( root.querySelector( '#proof' ) ) return;
		var anchor = root.querySelector( '.hu-sst__toc-band' ) || root.querySelector( '#hero' );
		if ( ! anchor ) return;

		var section = el( 'section', 'hu-sst__band hu-sst__band--light hu-sst__band--white hu-sst__proof-zone' );
		section.id = 'proof';
		section.setAttribute( 'data-nx-theme', 'light' );
		var container = el( 'div', 'hu-sst__container' );
		var head = el( 'div', 'hu-sst__section-head' );
		head.appendChild( el( 'p', 'hu-sst__eyebrow', 'Nachweis vor Umschaltung' ) );
		head.appendChild( el( 'h2', 'hu-sst__h2', 'Nicht einfach umschalten. Erst gegentesten.' ) );
		head.appendChild( el( 'p', 'hu-sst__section-lead', 'Die neue Messstrecke muss sich in Ihren Konten gegen den Bestand beweisen, bevor sie produktiv die Verantwortung übernimmt.' ) );
		container.appendChild( head );

		var grid = el( 'div', 'hu-sst__grid hu-sst__grid--3' );
		[
			[ '01 · Bestand bleibt aktiv', 'Die vorhandene Messung bleibt während des Tests erhalten. Es gibt keinen Blindflug durch eine harte Sofort-Umschaltung.' ],
			[ '02 · Neue Strecke läuft parallel', 'Browser- und Server-Signale werden auf Event, Consent, Parameter und Deduplizierung gegeneinander geprüft.' ],
			[ '03 · Umschaltung nach QA', 'Erst wenn fehlende, doppelte oder falsch zugeordnete Events geklärt sind, wird die neue Messstrecke zum produktiven Standard.' ]
		].forEach( function ( item ) {
			var card = el( 'article', 'hu-sst__card' );
			card.appendChild( el( 'h3', 'hu-sst__card-title', item[0] ) );
			card.appendChild( el( 'p', 'hu-sst__card-text', item[1] ) );
			grid.appendChild( card );
		} );
		container.appendChild( grid );

		var caseSource = root.querySelector( 'a[data-track-action="internal_sst_solar_case"]' );
		if ( caseSource ) {
			var callout = el( 'aside', 'hu-sst__callout' );
			callout.appendChild( el( 'h3', 'hu-sst__callout-title', 'Tracking als Teil eines realen Lead-Systems' ) );
			callout.appendChild( el( 'p', '', 'Tracking, Formulare und CRM-Übergaben waren Teil eines dokumentierten Lead-Funnels. Die Case-Ergebnisse sind ein Systemergebnis — kein isoliertes Versprechen, das allein Server-Side Tracking zugeschrieben wird.' ) );
			var line = el( 'p', 'hu-sst__callout-links' );
			var link = el( 'a', '', 'Dokumentierten Case ansehen' );
			link.href = caseSource.href;
			track( link, 'case_tracking_proof', 'proof' );
			line.appendChild( link );
			callout.appendChild( line );
			container.appendChild( callout );
		}
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
				[ 'symptome', 'Problem', 'toc_problem' ],
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
		var form = root.querySelector( '#anfrage form[data-contact-form]' );
		if ( ! form ) return;
		var details = form.querySelector( '.hu-sst__form-details' );
		var setup = form.querySelector( '#contact-tracking-setup' );
		var labels = {
			cta_package_standard: 'Basis · GA4 + Google Ads',
			cta_package_pro: 'Performance · Google + Meta',
			cta_package_individual: 'Tracking & CRM · Individuell'
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
		add( 'Muss ich mein bestehendes Tracking abschalten?', 'Nein. Die bisherige Messung bleibt während des Paralleltests aktiv. Erst nach der QA auf fehlende, doppelte oder falsch zugeordnete Events wird die neue Messstrecke zum produktiven Standard.' );
		add( 'Was passiert nach dem Setup?', 'Sie erhalten Dokumentation, benannte GTM-Versionen und die vereinbarten Zugänge. Danach kann das Setup bei Ihnen bleiben oder über Tracking Care regelmäßig kontrolliert werden; neue Plattformen und größere Umbauten werden separat kalkuliert.' );

		var order = [ 'Was kostet Server-Side Tracking', 'Wann lohnt es sich nicht', 'Wie lange dauert die Einrichtung', 'Muss ich mein bestehendes Tracking abschalten', 'Funktioniert das mit WordPress', 'Was passiert nach dem Setup', 'Welches Problem löst Server-Side Tracking', 'Was ist der Unterschied zwischen Client-Side und Server-Side Tracking', 'Ist Server-Side Tracking automatisch DSGVO-konform', 'Wie viele Conversions kommen zusätzlich an', 'Brauche ich eine Server-Side-Tracking-Agentur' ];
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
	}

	if ( document.readyState === 'loading' ) document.addEventListener( 'DOMContentLoaded', init );
	else init();
} )();
