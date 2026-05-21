/**
 * About-Page — Blueprint-Animation und Scroll-Reveal.
 *
 * Verhalten:
 * - SVG-Leitungen im Infrastruktur-Blueprint bauen sich scrollbasiert auf.
 * - Labels erscheinen nacheinander, sobald der jeweilige Stack-Schwellwert erreicht ist.
 * - [data-reveal]-Elemente fade-in bei IntersectionObserver.
 * - prefers-reduced-motion: Leitungen und Labels werden sofort aktiv, keine Scroll-/Fade-Animationen.
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

	function initBlueprintAnimation() {
		var hero   = document.getElementById( 'about-hero' );
		var lines  = document.querySelectorAll( '.nexus-about .about-blueprint__line[data-depth]' );
		var labels = document.querySelectorAll( '.nexus-about .about-well__label[data-depth]' );

		if ( ! hero || ( ! lines.length && ! labels.length ) ) {
			return;
		}

		var reducedMotion = window.matchMedia
			? window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches
			: false;

		if ( reducedMotion ) {
			lines.forEach( function ( line ) {
				line.classList.add( 'is-active' );
				line.style.strokeDashoffset = 0;
			} );

			labels.forEach( function ( label ) {
				label.classList.add( 'is-active' );
			} );
			return;
		}

		lines.forEach( function ( line ) {
			if ( 'function' !== typeof line.getTotalLength ) {
				line.classList.add( 'is-active' );
				return;
			}

			var length = line.getTotalLength();
			line.dataset.length = length;
			line.style.strokeDasharray = length;
			line.style.strokeDashoffset = length;
		} );

		var pendingFrame = null;

		function clamp( value, min, max ) {
			return Math.min( Math.max( value, min ), max );
		}

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

			var activation = progress * 100;

			lines.forEach( function ( line ) {
				var depth = parseInt( line.dataset.depth, 10 );
				var length = parseFloat( line.dataset.length || '0' );

				if ( isNaN( depth ) || ! length ) {
					return;
				}

				var lineProgress = clamp( ( activation - ( depth - 24 ) ) / 24, 0, 1 );
				line.style.strokeDashoffset = length * ( 1 - lineProgress );

				if ( lineProgress >= 0.82 ) {
					line.classList.add( 'is-active' );
				}
			} );

			labels.forEach( function ( label ) {
				var depth = parseInt( label.dataset.depth, 10 );

				if ( ! isNaN( depth ) && activation >= depth ) {
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
		initBlueprintAnimation();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
