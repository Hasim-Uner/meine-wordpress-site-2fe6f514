/**
 * About-Page — Brunnen-Animation und Scroll-Reveal.
 *
 * Verhalten:
 * - Wasserspiegel im Brunnen steigt scrollbasiert von 0 % auf 92 % der Schacht-Hoehe.
 * - Labels (Portal-Leads → Tracking → Infrastruktur → Eigene Quelle) erscheinen
 *   nacheinander, sobald der jeweilige Tiefen-Schwellwert erreicht ist.
 * - [data-reveal]-Elemente fade-in bei IntersectionObserver.
 * - prefers-reduced-motion: Wasser wird sofort auf 92 % gesetzt, Labels alle aktiv,
 *   keine Scroll-/Fade-Animationen.
 */
( function () {
	'use strict';

	function initRevealObserver() {
		var about          = document.querySelector( '.nexus-about' );
		var revealElements = document.querySelectorAll( '.nexus-about [data-reveal]' );

		if ( ! about || ! revealElements.length ) {
			return;
		}

		// Falls IntersectionObserver fehlt oder Reduced-Motion gewünscht ist,
		// alle Elemente sofort als sichtbar markieren und Marker NICHT setzen.
		var reducedMotion = window.matchMedia
			? window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches
			: false;

		if ( reducedMotion || ! ( 'IntersectionObserver' in window ) ) {
			revealElements.forEach( function ( el ) {
				el.classList.add( 'is-revealed' );
			} );
			return;
		}

		// JS ist da: jetzt darf das initial-hidden-CSS greifen.
		about.classList.add( 'js-reveal' );

		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-revealed' );
					observer.unobserve( entry.target );
				}
			} );
		}, {
			threshold: 0.1,
			rootMargin: '0px 0px -40px 0px',
		} );

		revealElements.forEach( function ( el ) {
			observer.observe( el );
		} );
	}

	function initWellAnimation() {
		var waterLevel = document.getElementById( 'aboutWaterLevel' );
		var hero       = document.getElementById( 'about-hero' );
		var labels     = document.querySelectorAll( '.nexus-about .about-well__label' );
		var maxWaterHeight = 92;

		if ( ! waterLevel || ! hero ) {
			return;
		}

		var reducedMotion = window.matchMedia
			? window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches
			: false;

		if ( reducedMotion ) {
			waterLevel.style.height = maxWaterHeight + '%';
			labels.forEach( function ( label ) {
				label.classList.add( 'is-active' );
			} );
			return;
		}

		var pendingFrame = null;

		function update() {
			pendingFrame = null;

			var rect = hero.getBoundingClientRect();
			var heroTop = rect.top;
			var heroHeight = rect.height;
			var viewportH = window.innerHeight || document.documentElement.clientHeight;

			var progress = ( viewportH - heroTop ) / ( viewportH + heroHeight );

			if ( progress < 0 ) {
				progress = 0;
			} else if ( progress > 1 ) {
				progress = 1;
			}

			var waterHeight = progress * maxWaterHeight;
			waterLevel.style.height = waterHeight + '%';

			labels.forEach( function ( label ) {
				var depth = parseInt( label.dataset.depth, 10 );

				if ( ! isNaN( depth ) && waterHeight >= depth ) {
					label.classList.add( 'is-active' );
				}
			} );
		}

		function schedule() {
			if ( null !== pendingFrame ) {
				return;
			}

			pendingFrame = window.requestAnimationFrame( update );
		}

		update();
		window.addEventListener( 'scroll', schedule, { passive: true } );
		window.addEventListener( 'resize', schedule );
	}

	function init() {
		if ( ! document.querySelector( '.nexus-about' ) ) {
			return;
		}

		initRevealObserver();
		initWellAnimation();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
