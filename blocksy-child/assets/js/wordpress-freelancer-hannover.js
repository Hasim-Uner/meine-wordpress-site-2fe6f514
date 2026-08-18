(() => {
	'use strict';

	const accordion = document.querySelector('[data-fr-accordion]');

	if (accordion) {
		const items = Array.from(accordion.querySelectorAll('details.hu-fr__faq-item'));

		items.forEach((item) => {
			item.addEventListener('toggle', () => {
				if (!item.open) {
					return;
				}

				items.forEach((otherItem) => {
					if (otherItem !== item && otherItem.open) {
						otherItem.open = false;
					}
				});
			});
		});
	}

	const system = document.querySelector('.hu-fr-system');
	const signal = system ? system.querySelector('.hu-fr-system__signal') : null;

	if (!system || !signal || !('IntersectionObserver' in window) || typeof signal.animate !== 'function') {
		return;
	}

	const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
	let signalAnimation = null;
	let systemVisible = false;

	const stopSignal = (reset = false) => {
		if (!signalAnimation) {
			return;
		}

		if (reset) {
			signalAnimation.cancel();
			signalAnimation = null;
			return;
		}

		signalAnimation.pause();
	};

	const startSignal = () => {
		if (motionPreference.matches || document.hidden || !systemVisible) {
			return;
		}

		if (signalAnimation) {
			signalAnimation.play();
			return;
		}

		// Eine komplette Dash-Periode: nahtloser Loop ohne Layout-Aenderung.
		// Der Effekt wird erst im Viewport erzeugt und offscreen sofort pausiert.
		signalAnimation = signal.animate(
			[
				{ strokeDashoffset: '0px' },
				{ strokeDashoffset: '-384px' },
			],
			{
				duration: 3200,
				iterations: Infinity,
				easing: 'linear',
			}
		);
	};

	const syncSignalMotion = () => {
		if (motionPreference.matches) {
			stopSignal(true);
			return;
		}

		if (document.hidden || !systemVisible) {
			stopSignal();
			return;
		}

		startSignal();
	};

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.target !== system) {
					return;
				}

				systemVisible = entry.isIntersecting && entry.intersectionRatio >= 0.2;
				syncSignalMotion();
			});
		},
		{
			threshold: [0, 0.2],
			rootMargin: '0px 0px -8% 0px',
		}
	);

	observer.observe(system);
	document.addEventListener('visibilitychange', syncSignalMotion);

	if (typeof motionPreference.addEventListener === 'function') {
		motionPreference.addEventListener('change', syncSignalMotion);
	} else if (typeof motionPreference.addListener === 'function') {
		motionPreference.addListener(syncSignalMotion);
	}
})();
