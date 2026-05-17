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
		var onScroll = function () {
			if ( hasShown ) {
				return;
			}

			if ( window.scrollY >= SHOW_AFTER_SCROLL_PX ) {
				hasShown = true;
				showBar( bar );
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

		// Bei Resize: bei Verlassen des Mobile-Viewports versteckt halten.
		var onResize = function () {
			if ( ! isMobileViewport() ) {
				hideBar( bar );
			} else if ( hasShown && bar.hasAttribute( 'hidden' ) ) {
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
