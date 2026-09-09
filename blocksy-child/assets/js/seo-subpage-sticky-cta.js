/**
 * Sticky-CTA-Bar fuer SEO-Sub-Pages — mobil-only, barrierefrei.
 *
 * Verhalten:
 * - Auf Mobile (Viewport <= 760px) wird die Bar nach kurzem Scroll sichtbar.
 * - Auf Desktop bleibt sie permanent hidden.
 * - Dismiss-Button merkt sich Entscheidung pro Page-Slug fuer 24 Stunden.
 * - prefers-reduced-motion deaktiviert die Slide-In-Animation.
 * - Body-Padding wird dynamisch gesetzt, damit die Bar nicht den letzten
 *   Inhalt verdeckt.
 */
( function () {
	'use strict';

	var STORAGE_PREFIX = 'huStickyCta_dismissed_';
	var DISMISS_WINDOW_MS = 24 * 60 * 60 * 1000;
	var MOBILE_MAX_WIDTH = 760;
	var SHOW_AFTER_SCROLL_PX = 240;

	function isMobileViewport() {
		return window.matchMedia( '(max-width: ' + MOBILE_MAX_WIDTH + 'px)' ).matches;
	}

	function getPageDismissKey() {
		try {
			return STORAGE_PREFIX + ( window.location.pathname || '/' );
		} catch ( e ) {
			return STORAGE_PREFIX + 'default';
		}
	}

	function isRecentlyDismissed() {
		try {
			var raw = window.localStorage.getItem( getPageDismissKey() );

			if ( ! raw ) {
				return false;
			}

			var ts = parseInt( raw, 10 );

			if ( isNaN( ts ) ) {
				return false;
			}

			return ( Date.now() - ts ) < DISMISS_WINDOW_MS;
		} catch ( e ) {
			return false;
		}
	}

	function markDismissed() {
		try {
			window.localStorage.setItem( getPageDismissKey(), String( Date.now() ) );
		} catch ( e ) {
			// Stille fallen lassen — localStorage kann blockiert sein.
		}
	}

	function setBodyPadding( bar ) {
		if ( ! bar || ! document.body ) {
			return;
		}

		var height = bar.getBoundingClientRect().height;

		if ( height > 0 ) {
			document.body.style.setProperty( '--hu-sticky-cta-h', height + 'px' );
		}
	}

	function clearBodyPadding() {
		if ( document.body ) {
			document.body.style.removeProperty( '--hu-sticky-cta-h' );
		}
	}

	function showBar( bar ) {
		if ( ! bar.hasAttribute( 'hidden' ) ) {
			return;
		}

		bar.removeAttribute( 'hidden' );
		// Forcierter Reflow, damit die Transition triggert.
		void bar.offsetWidth;
		bar.classList.add( 'is-visible' );
		setBodyPadding( bar );
	}

	function hideBar( bar ) {
		bar.classList.remove( 'is-visible' );
		bar.setAttribute( 'hidden', '' );
		clearBodyPadding();
	}

	function onCloseClick( bar, evt ) {
		if ( evt && typeof evt.preventDefault === 'function' ) {
			evt.preventDefault();
		}

		markDismissed();
		hideBar( bar );
	}

	function init() {
		var bar = document.getElementById( 'hu-sticky-cta' );

		if ( ! bar ) {
			return;
		}

		// Desktop oder bereits dismissed: nichts tun.
		if ( ! isMobileViewport() || isRecentlyDismissed() ) {
			return;
		}

		var hasShown = false;
		var hideTargetVisible = false;
		var hideSelector = bar.getAttribute( 'data-sticky-hide-when-visible' );
		var hideTarget = null;

		if ( hideSelector ) {
			try {
				hideTarget = document.querySelector( hideSelector );
			} catch ( e ) {
				hideTarget = null;
			}

		if ( hideTarget ) {
			var initialTargetRect = hideTarget.getBoundingClientRect();
			hideTargetVisible = initialTargetRect.bottom > 0 && initialTargetRect.top < window.innerHeight;
		}

		var onScroll = function () {
			if ( hasShown ) {
				return;
			}

			if ( window.scrollY >= SHOW_AFTER_SCROLL_PX ) {
				hasShown = true;

				if ( ! hideTargetVisible ) {
					showBar( bar );
				}
			}
		};

		window.addEventListener( 'scroll', onScroll, { passive: true } );

		// Falls Seite bereits weit gescrollt geladen wird (z. B. mit Hash):
		onScroll();

		var closeBtn = bar.querySelector( '.hu-sticky-cta__close' );

		if ( closeBtn ) {
			closeBtn.addEventListener( 'click', function ( evt ) {
				onCloseClick( bar, evt );
			} );
		}

		if ( hideTarget && typeof window.IntersectionObserver === 'function' ) {
			new window.IntersectionObserver( function ( entries ) {
				entries.forEach( function ( entry ) {
					hideTargetVisible = entry.isIntersecting;

					if ( hideTargetVisible ) {
						hideBar( bar );
					} else if ( hasShown && isMobileViewport() && ! isRecentlyDismissed() ) {
						showBar( bar );
					}
				} );
			}, { threshold: 0.08 } ).observe( hideTarget );
		}

		// Bei Resize: bei Verlassen des Mobile-Viewports versteckt halten.
		var onResize = function () {
			if ( ! isMobileViewport() ) {
				hideBar( bar );
			} else if ( hasShown && ! hideTargetVisible && ! isRecentlyDismissed() && bar.hasAttribute( 'hidden' ) ) {
				bar.removeAttribute( 'hidden' );
				bar.classList.add( 'is-visible' );
				setBodyPadding( bar );
			}
		};

		window.addEventListener( 'resize', onResize );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();

/**
 * Money-Page Rails — progressive enhancement for long commercial pages.
 *
 * The Server-Side-Tracking route is the pilot. Future money pages can opt in
 * with data-money-page on their root and data-money-section on the chapters.
 */
( function () {
	'use strict';

	var TRACKING_ROOT_SELECTOR = '.hu-sst[data-track-page="server-side-tracking-b2b"]';
	var TRACKING_SECTION_IDS = [
		'symptome',
		'unterschied',
		'fit',
		'architektur',
		'umfang',
		'pakete',
		'ablauf',
		'sicherheit',
		'faq',
		'anfrage'
	];
	var TRACKING_SECTION_LABELS = {
		symptome: 'Ausgangslage',
		unterschied: 'Messprinzip',
		fit: 'Für wen?',
		architektur: 'Architektur',
		umfang: 'Einrichtung',
		pakete: 'Pakete',
		ablauf: 'Ablauf',
		sicherheit: 'Sicherheit',
		faq: 'FAQ',
		anfrage: 'Anfrage'
	};

	function createElement( tag, className, text ) {
		var element = document.createElement( tag );

		if ( className ) {
			element.className = className;
		}

		if ( typeof text === 'string' ) {
			element.textContent = text;
		}

		return element;
	}

	function collectSections( root ) {
		var declaredSections = Array.prototype.slice.call( root.querySelectorAll( '[data-money-section]' ) );

		if ( declaredSections.length ) {
			return declaredSections.filter( function ( section ) {
				return Boolean( section.id );
			} );
		}

		return TRACKING_SECTION_IDS.map( function ( id ) {
			return root.querySelector( '#' + id );
		} ).filter( function ( section ) {
			return Boolean( section );
		} );
	}

	function getSectionLabel( section ) {
		if ( ! section ) {
			return '';
		}

		var explicitLabel = section.getAttribute( 'data-money-label' );

		if ( explicitLabel ) {
			return explicitLabel;
		}

		if ( TRACKING_SECTION_LABELS[ section.id ] ) {
			return TRACKING_SECTION_LABELS[ section.id ];
		}

		var eyebrow = section.querySelector( '.hu-sst__eyebrow, [data-money-eyebrow]' );
		var heading = section.querySelector( 'h2' );

		return ( eyebrow && eyebrow.textContent.trim() ) || ( heading && heading.textContent.trim() ) || section.id;
	}

	function buildTocList( sections, linkClass ) {
		var list = createElement( 'ol', 'hu-money-toc__list' );

		sections.forEach( function ( section, index ) {
			var item = createElement( 'li', 'hu-money-toc__item' );
			var link = createElement( 'a', linkClass || '', '' );
			var number = createElement( 'span', 'hu-money-toc__num', String( index + 1 ).padStart( 2, '0' ) );
			var label = createElement( 'span', 'hu-money-toc__label', getSectionLabel( section ) );
			var marker = createElement( 'span', 'hu-money-toc__marker' );

			link.href = '#' + section.id;
			link.setAttribute( 'data-money-toc-link', section.id );
			link.appendChild( number );
			link.appendChild( marker );
			link.appendChild( label );
			item.appendChild( link );
			list.appendChild( item );
		} );

		return list;
	}

	function buildLeftRail( root, sections ) {
		var nav = createElement( 'nav', 'hu-money-rail hu-money-rail--toc' );
		var kicker = createElement( 'p', 'hu-money-rail__kicker', 'Inhalt · ' + String( sections.length ).padStart( 2, '0' ) );
		var progress = createElement( 'span', 'hu-money-rail__progress' );
		var fill = createElement( 'span', 'hu-money-rail__progress-fill' );

		nav.setAttribute( 'aria-label', 'Inhaltsverzeichnis' );
		progress.setAttribute( 'aria-hidden', 'true' );
		fill.setAttribute( 'data-money-progress', '' );
		progress.appendChild( fill );
		nav.appendChild( kicker );
		nav.appendChild( progress );
		nav.appendChild( buildTocList( sections, 'hu-money-toc__link' ) );
		root.insertBefore( nav, root.firstChild );

		return nav;
	}

	function buildRightRail( root ) {
		var rail = createElement( 'aside', 'hu-money-rail hu-money-rail--context' );
		var kicker = createElement( 'p', 'hu-money-rail__kicker', 'Projekt-Rahmen' );
		var current = createElement( 'p', 'hu-money-context__current', 'Ausgangslage' );
		var proofList = createElement( 'dl', 'hu-money-context__proof' );
		var proofRows = root.querySelectorAll( '.hu-sst__proof-strip > div, [data-money-proof-row]' );
		var primaryCta = root.querySelector( '.hu-sst__cta .hu-sst__btn--primary, [data-money-primary-cta]' );
		var cta = createElement( 'a', 'hu-money-context__cta', primaryCta ? primaryCta.textContent.trim() : 'Projekt anfragen' );

		rail.setAttribute( 'aria-label', 'Projekt-Rahmen und Schnellzugang' );
		current.setAttribute( 'data-money-current-label', '' );
		rail.appendChild( kicker );
		rail.appendChild( current );

		Array.prototype.slice.call( proofRows, 0, 3 ).forEach( function ( row ) {
			var sourceTerm = row.querySelector( 'dt, [data-money-proof-term]' );
			var sourceValue = row.querySelector( 'dd, [data-money-proof-value]' );

			if ( ! sourceTerm || ! sourceValue ) {
				return;
			}

			var wrapper = createElement( 'div', 'hu-money-context__proof-row' );
			wrapper.appendChild( createElement( 'dt', '', sourceTerm.textContent.trim() ) );
			wrapper.appendChild( createElement( 'dd', '', sourceValue.textContent.trim() ) );
			proofList.appendChild( wrapper );
		} );

		if ( proofList.children.length ) {
			rail.appendChild( proofList );
		}

		cta.href = primaryCta ? primaryCta.getAttribute( 'href' ) || '#anfrage' : '#anfrage';
		cta.setAttribute( 'data-track-action', 'cta_money_rail_tracking' );
		cta.setAttribute( 'data-track-category', root.getAttribute( 'data-track-page' ) || 'money_page' );
		cta.setAttribute( 'data-track-section', 'money_rail' );
		rail.appendChild( cta );
		root.insertBefore( rail, root.firstChild );

		return rail;
	}

	function buildMobileToc( root, sections ) {
		var firstSection = sections[0];
		var container = firstSection ? firstSection.querySelector( '.hu-sst__container, [data-money-container]' ) : null;

		if ( ! container ) {
			return null;
		}

		var wrapper = createElement( 'div', 'hu-money-mobile-toc' );
		var details = createElement( 'details', 'hu-money-mobile-toc__details' );
		var summary = createElement( 'summary', 'hu-money-mobile-toc__summary' );
		var summaryTitle = createElement( 'span', '', 'Auf dieser Seite' );
		var summaryMeta = createElement( 'small', '', sections.length + ' Abschnitte' );
		var list = buildTocList( sections, 'hu-money-mobile-toc__link' );

		summary.appendChild( summaryTitle );
		summary.appendChild( summaryMeta );
		details.appendChild( summary );
		details.appendChild( list );
		wrapper.appendChild( details );
		container.insertBefore( wrapper, container.firstChild );

		list.addEventListener( 'click', function ( event ) {
			var target = event.target;
			var link = target && typeof target.closest === 'function' ? target.closest( 'a[href^="#"]' ) : null;

			if ( link ) {
				details.removeAttribute( 'open' );
			}
		} );

		return wrapper;
	}

	function initMoneyPageRails() {
		var root = document.querySelector( '[data-money-page]' ) || document.querySelector( TRACKING_ROOT_SELECTOR );

		if ( ! root || root.getAttribute( 'data-money-rails-ready' ) === 'true' ) {
			return;
		}

		var sections = collectSections( root );

		if ( sections.length < 4 ) {
			return;
		}

		root.setAttribute( 'data-money-rails-ready', 'true' );
		root.classList.add( 'hu-money-page-active' );

		var hero = root.querySelector( '#hero, [data-money-hero]' );
		var formSection = root.querySelector( '#anfrage, [data-money-final]' );
		var leftRail = buildLeftRail( root, sections );
		var rightRail = buildRightRail( root );
		buildMobileToc( root, sections );

		var links = Array.prototype.slice.call( root.querySelectorAll( '[data-money-toc-link]' ) );
		var progressFill = leftRail.querySelector( '[data-money-progress]' );
		var currentLabel = rightRail.querySelector( '[data-money-current-label]' );
		var sectionTops = [];
		var firstTop = 0;
		var lastTop = 1;
		var heroEnd = 0;
		var formTop = Number.POSITIVE_INFINITY;
		var activeId = '';
		var ticking = false;

		function measure() {
			sectionTops = sections.map( function ( section ) {
				return {
					id: section.id,
					top: section.getBoundingClientRect().top + window.scrollY
				};
			} );

			firstTop = sectionTops[0].top;
			lastTop = sectionTops[ sectionTops.length - 1 ].top;
			heroEnd = hero ? hero.getBoundingClientRect().bottom + window.scrollY : firstTop;
			formTop = formSection ? formSection.getBoundingClientRect().top + window.scrollY : Number.POSITIVE_INFINITY;
		}

		function setActiveSection( id ) {
			if ( ! id || id === activeId ) {
				return;
			}

			activeId = id;

			links.forEach( function ( link ) {
				if ( link.getAttribute( 'data-money-toc-link' ) === id ) {
					link.setAttribute( 'aria-current', 'true' );
				} else {
					link.removeAttribute( 'aria-current' );
				}
			} );

			if ( currentLabel ) {
				var section = root.querySelector( '#' + id );
				currentLabel.textContent = getSectionLabel( section );
			}
		}

		function update() {
			var scrollY = window.scrollY || window.pageYOffset || 0;
			var viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
			var probe = scrollY + Math.min( viewportHeight * 0.34, 320 );
			var nextActiveId = sectionTops.length ? sectionTops[0].id : '';
			var denominator = Math.max( 1, lastTop - firstTop );
			var progress = Math.max( 0, Math.min( 1, ( probe - firstTop ) / denominator ) );

			sectionTops.forEach( function ( section ) {
				if ( section.top <= probe ) {
					nextActiveId = section.id;
				}
			} );

			setActiveSection( nextActiveId );

			if ( progressFill ) {
				progressFill.style.transform = 'scaleY(' + progress.toFixed( 4 ) + ')';
			}

			root.classList.toggle( 'is-money-rails-active', scrollY > Math.max( 0, heroEnd - 120 ) );
			root.classList.toggle( 'is-money-form-visible', scrollY + viewportHeight * 0.46 >= formTop );
			ticking = false;
		}

		function requestUpdate() {
			if ( ticking ) {
				return;
			}

			ticking = true;
			window.requestAnimationFrame( update );
		}

		measure();
		update();
		window.addEventListener( 'scroll', requestUpdate, { passive: true } );
		window.addEventListener( 'resize', function () {
			measure();
			requestUpdate();
		} );
		window.addEventListener( 'load', function () {
			measure();
			requestUpdate();
		}, { once: true } );

		if ( typeof window.ResizeObserver === 'function' ) {
			var resizeObserver = new window.ResizeObserver( function () {
				measure();
				requestUpdate();
			} );
			resizeObserver.observe( root );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initMoneyPageRails );
	} else {
		initMoneyPageRails();
	}
} )();
