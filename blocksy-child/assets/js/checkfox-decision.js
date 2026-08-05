(function () {
	'use strict';

	function pushTrack(node) {
		var action = node && node.getAttribute('data-track-action');

		if (!action) {
			return;
		}

		try {
			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push({
				event: action,
				event_category: node.getAttribute('data-track-category') || 'engagement',
				event_section: node.getAttribute('data-track-section') || 'checkfox_decision_page'
			});
		} catch (error) {
			// Tracking must never block the decision tools.
		}
	}

	/**
	 * Resolve which element a fragment scrolls to and which one takes focus.
	 *
	 * A <details> target opens first and hands focus to its <summary>, the
	 * natively focusable control. Scrolling stays on the fragment target itself
	 * so its scroll-margin-top keeps it clear of the sticky navigation.
	 */
	function revealTarget(target) {
		if (!target) {
			return null;
		}

		var details = target.tagName === 'DETAILS' ? target : target.closest('details');

		if (details) {
			details.open = true;
		}

		if (target.tagName === 'DETAILS') {
			return { scroll: target, focus: target.querySelector('summary') || target };
		}

		return { scroll: target, focus: target };
	}

	function focusFromHash(root, hash) {
		if (!hash || hash.length < 2) {
			return;
		}

		var target;

		try {
			target = root.querySelector(hash);
		} catch (error) {
			return;
		}

		var resolved = revealTarget(target);

		if (!resolved) {
			return;
		}

		// Let the browser finish its own fragment scroll before taking over,
		// otherwise the position jumps back to the pre-open layout.
		window.setTimeout(function () {
			resolved.focus.focus({ preventScroll: true });
			resolved.scroll.scrollIntoView({ block: 'start' });
		}, 0);
	}

	function initAnchorFocus(root) {
		root.querySelectorAll('a[href^="#"]').forEach(function (link) {
			link.addEventListener('click', function () {
				focusFromHash(root, link.getAttribute('href'));
			});
		});

		window.addEventListener('hashchange', function () {
			focusFromHash(root, window.location.hash);
		});

		if (window.location.hash) {
			focusFromHash(root, window.location.hash);
		}
	}

	function initTableOfContents(root) {
		var toc = root.querySelector('[data-checkfox-toc]');

		if (!toc || typeof window.IntersectionObserver !== 'function') {
			return;
		}

		var links = Array.prototype.slice.call(toc.querySelectorAll('a[href^="#"]'));
		var sections = [];

		links.forEach(function (link) {
			var section = root.querySelector(link.getAttribute('href'));

			if (!section) {
				return;
			}

			// A <details> anchor marks its surrounding section, not the disclosure.
			sections.push({ link: link, node: section.closest('.hu-checkfox__section') || section });
		});

		if (!sections.length) {
			return;
		}

		function setCurrent(entryNode) {
			sections.forEach(function (item) {
				if (item.node === entryNode) {
					item.link.setAttribute('aria-current', 'true');
					return;
				}

				item.link.removeAttribute('aria-current');
			});
		}

		var observer = new window.IntersectionObserver(function (entries) {
			var visible = entries.filter(function (entry) {
				return entry.isIntersecting;
			});

			if (!visible.length) {
				return;
			}

			visible.sort(function (a, b) {
				return a.boundingClientRect.top - b.boundingClientRect.top;
			});

			setCurrent(visible[0].target);
		}, { rootMargin: '-20% 0px -70% 0px' });

		sections.forEach(function (item) {
			observer.observe(item.node);
		});
	}

	/**
	 * Hand orientation back to the global header at the end of the page.
	 *
	 * Inside the article the sticky bar carries navigation and the Marktcheck
	 * CTA, so the header stays out of the way. Once the closing CTA is in view
	 * the bar steps aside and the site menu takes over for "where next".
	 */
	function initEndOfPageHandover(root) {
		var bar = root.querySelector('[data-checkfox-bar]');
		var closing = root.querySelector('.hu-checkfox__final');
		var header = document.querySelector('[data-site-header]');

		if (!bar || !closing || typeof window.IntersectionObserver !== 'function') {
			return;
		}

		function setHandover(active) {
			bar.classList.toggle('is-hidden', active);

			if (!header) {
				return;
			}

			if (active) {
				header.setAttribute('data-site-header-pin', '');
			} else {
				header.removeAttribute('data-site-header-pin');
			}

			// site-header.js owns visibility; tell it the pin state changed.
			header.dispatchEvent(new CustomEvent('nexus:header-pin'));
		}

		var observer = new window.IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				setHandover(entry.isIntersecting);
			});
		}, { rootMargin: '0px 0px -35% 0px' });

		observer.observe(closing);
	}

	function initCalculatorTracking(root) {
		var calculator = root.querySelector('[data-hu-cpo-calculator][data-cpo-mode="portal"]');
		var results = calculator && calculator.querySelector('[data-cpo-results]');

		if (!calculator || !results) {
			return;
		}

		var interacted = false;
		var resultVisible = false;
		var resultTracked = false;

		function trackResultView() {
			if (!interacted || !resultVisible || resultTracked) {
				return;
			}

			resultTracked = true;
			pushTrack(results);
		}

		calculator.addEventListener('input', function () {
			if (!interacted) {
				interacted = true;
				pushTrack(calculator);
			}

			trackResultView();
		});

		if (typeof window.IntersectionObserver === 'function') {
			var observer = new window.IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					resultVisible = true;
					trackResultView();
				});
			}, { threshold: 0.55 });

			observer.observe(results);
			return;
		}

		resultVisible = true;
	}

	function initContract(root) {
		var contract = root.querySelector('[data-checkfox-contract]');

		if (!contract) {
			return;
		}

		var checks = contract.querySelectorAll('[data-contract-check]');
		var result = contract.querySelector('[data-contract-result]');
		var detail = contract.querySelector('[data-contract-detail]');
		var resultBox = result && result.closest('.hu-checkfox__contract-result');
		var completionTracked = false;

		if (!checks.length || !result || !detail) {
			return;
		}

		function updateResult() {
			var clarified = 0;

			checks.forEach(function (check) {
				if (check.checked) {
					clarified += 1;
				}
			});

			result.textContent = clarified + ' von ' + checks.length + ' Punkten geklärt';

			if (clarified === checks.length) {
				detail.textContent = 'Die Grundlagen sind dokumentiert. Prüfen Sie jetzt, ob die Kosten pro Auftrag zu Marge und Vertriebskapazität passen.';
			} else if (clarified >= 5) {
				detail.textContent = 'Schließen Sie die offenen Punkte vor Vertragsabschluss, sonst rechnen Sie mit Annahmen.';
			} else {
				detail.textContent = 'Solange Grundlagen offen sind, rechnet der Kostenrechner mit Annahmen statt mit Ihren Werten.';
			}

			if (!completionTracked && clarified === checks.length) {
				completionTracked = true;
				pushTrack(resultBox);
			}
		}

		contract.addEventListener('change', function (event) {
			if (!event.target.matches('[data-contract-check]')) {
				return;
			}

			updateResult();
		});

		// Browsers restore checkbox state on back-navigation; keep the summary honest.
		updateResult();
	}

	function initCockpit(root) {
		initAnchorFocus(root);
		initTableOfContents(root);
		initEndOfPageHandover(root);
		initCalculatorTracking(root);
		initContract(root);
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('[data-checkfox-cockpit]').forEach(initCockpit);
	});
}());
