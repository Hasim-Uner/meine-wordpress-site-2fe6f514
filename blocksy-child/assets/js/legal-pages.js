/**
 * Legal pages V2
 *
 * Builds the in-page section index, highlights the current legal section
 * and exposes document scroll progress through --legal-progress.
 *
 * No analytics, no storage, no network requests.
 *
 * @package Blocksy_Child
 */
(function () {
	'use strict';

	function cleanHeading(text) {
		return String(text || '')
			.replace(/^\s*\d+[.)]?\s*/, '')
			.trim();
	}

	function initPage(page) {
		if (!page || page.hasAttribute('data-legal-initialized')) {
			return;
		}

		page.setAttribute('data-legal-initialized', '1');

		var sections = Array.prototype.slice.call(
			page.querySelectorAll('.privacy-section, .imprint-section')
		);
		var aside = page.querySelector('.privacy-card, .imprint-card');

		if (!sections.length || !aside) {
			page.setAttribute('data-legal-ready', '');
			return;
		}

		var nav = document.createElement('nav');
		nav.className = 'legal-index';
		nav.setAttribute('aria-label', 'Inhalt dieser Seite');

		sections.forEach(function (section, index) {
			var nr = String(index + 1).padStart(2, '0');
			var heading = section.querySelector('h2');
			if (!heading) {
				return;
			}

			if (!section.id) {
				section.id = 'legal-section-' + nr;
			}

			section.setAttribute('data-legal-nr', nr);

			var link = document.createElement('a');
			link.className = 'legal-index__link';
			link.href = '#' + section.id;
			link.innerHTML =
				'<span class="legal-index__nr" aria-hidden="true">' + nr + '</span>' +
				'<span class="legal-index__label"></span>';
			link.querySelector('.legal-index__label').textContent = cleanHeading(heading.textContent);
			nav.appendChild(link);
		});

		var existingMeta = aside.querySelector('.privacy-meta, .imprint-quickfacts');
		if (existingMeta) {
			aside.insertBefore(nav, existingMeta);
		} else {
			aside.appendChild(nav);
		}

		var links = Array.prototype.slice.call(nav.querySelectorAll('.legal-index__link'));

		function setActive(section) {
			sections.forEach(function (item) {
				item.classList.toggle('is-active', item === section);
			});

			links.forEach(function (link) {
				var current = link.getAttribute('href') === '#' + section.id;
				if (current) {
					link.setAttribute('aria-current', 'location');
				} else {
					link.removeAttribute('aria-current');
				}
			});
		}

		if ('IntersectionObserver' in window) {
			var observer = new IntersectionObserver(
				function (entries) {
					var visible = entries
						.filter(function (entry) { return entry.isIntersecting; })
						.sort(function (a, b) { return b.intersectionRatio - a.intersectionRatio; });

					if (visible.length) {
						setActive(visible[0].target);
					}
				},
				{
					root: null,
					rootMargin: '-24% 0px -62% 0px',
					threshold: [0, 0.1, 0.35, 0.6]
				}
			);

			sections.forEach(function (section) {
				observer.observe(section);
			});
		} else {
			setActive(sections[0]);
		}

		function updateProgress() {
			var rect = page.getBoundingClientRect();
			var pageTop = window.scrollY + rect.top;
			var max = Math.max(1, page.offsetHeight - window.innerHeight);
			var progress = Math.min(1, Math.max(0, (window.scrollY - pageTop) / max));
			page.style.setProperty('--legal-progress', progress.toFixed(4));
		}

		var ticking = false;
		function requestProgressUpdate() {
			if (ticking) {
				return;
			}
			ticking = true;
			window.requestAnimationFrame(function () {
				updateProgress();
				ticking = false;
			});
		}

		window.addEventListener('scroll', requestProgressUpdate, { passive: true });
		window.addEventListener('resize', requestProgressUpdate, { passive: true });
		updateProgress();

		window.requestAnimationFrame(function () {
			page.setAttribute('data-legal-ready', '');
		});
	}

	function init(root) {
		var context = root && root.querySelectorAll ? root : document;
		var pages = [];

		if (context.matches && context.matches('.legal-page')) {
			pages.push(context);
		}

		Array.prototype.push.apply(
			pages,
			Array.prototype.slice.call(context.querySelectorAll('.legal-page'))
		);

		pages.forEach(initPage);
	}

	window.NexusLegalPageInit = init;

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			init(document);
		});
	} else {
		init(document);
	}
})();
