/**
 * Design essay V4
 *
 * Motion is used for hierarchy and orientation only:
 * hero choreography, restrained pointer depth, figure reveals and a reading
 * rail that mirrors the article's top-level structure.
 *
 * No storage, analytics or network requests.
 *
 * @package Blocksy_Child
 */
(function () {
	'use strict';

	var hero = document.querySelector('.hu-design-hero');
	var essay = document.querySelector('.hu-design-essay');
	var container = document.querySelector('.nexus-single-container--design-essay');

	if (!hero || !essay || !container) {
		return;
	}

	var reduceMotion = window.matchMedia &&
		window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (!reduceMotion) {
		hero.classList.add('has-design-motion');
		essay.classList.add('has-design-motion');
	}

	window.requestAnimationFrame(function () {
		hero.classList.add('is-ready');
	});

	/* Small depth cue: the diagram reacts, but never becomes a toy. */
	if (!reduceMotion && window.matchMedia &&
		window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
		var visual = hero.querySelector('[data-design-hero-visual]');

		if (visual) {
			var pointerRaf = 0;

			visual.addEventListener('pointermove', function (event) {
				var rect = visual.getBoundingClientRect();
				var x = ((event.clientX - rect.left) / rect.width - 0.5) * 8;
				var y = ((event.clientY - rect.top) / rect.height - 0.5) * 8;

				if (pointerRaf) {
					window.cancelAnimationFrame(pointerRaf);
				}

				pointerRaf = window.requestAnimationFrame(function () {
					visual.style.setProperty('--hero-x', x.toFixed(2) + 'px');
					visual.style.setProperty('--hero-y', y.toFixed(2) + 'px');
				});
			}, { passive: true });

			visual.addEventListener('pointerleave', function () {
				visual.style.setProperty('--hero-x', '0px');
				visual.style.setProperty('--hero-y', '0px');
			}, { passive: true });
		}
	}

	/* Top-level TOC = reading architecture. Subsections stay in the document. */
	var toc = document.querySelector('.hu-design-toc');
	var tocList = toc ? toc.querySelector('#toc-list') : null;
	var progressNode = toc ? toc.querySelector('[data-design-toc-progress]') : null;
	var progressFill = toc ? toc.querySelector('[data-design-toc-fill]') : null;
	var tocEnhanced = false;

	function isTopLevelTocItem(item) {
		if (!item || item.tagName !== 'LI' || !item.querySelector('a')) {
			return false;
		}

		if (item.classList.contains('is-subsection')) {
			return false;
		}

		/* NexusCore initially communicates depth with inline margin-left.
		 * The shared TOC hydrator later converts that into is-subsection.
		 * Supporting both states keeps numbering deterministic. */
		var inlineIndent = parseFloat(item.style.marginLeft || '0');
		return !Number.isFinite(inlineIndent) || inlineIndent <= 0;
	}

	function enhanceToc() {
		if (!tocList) {
			return false;
		}

		var allItems = Array.prototype.slice.call(tocList.children).filter(function (item) {
			return item.tagName === 'LI' && item.querySelector('a');
		});
		var items = allItems.filter(isTopLevelTocItem);

		if (!items.length) {
			return false;
		}

		allItems.forEach(function (item) {
			var link = item.querySelector('a');
			if (link) {
				link.removeAttribute('data-design-index');
			}
		});

		items.forEach(function (item, index) {
			var link = item.querySelector('a');
			link.setAttribute('data-design-index', String(index + 1).padStart(2, '0'));
		});

		toc.classList.add('is-design-toc-ready');
		tocEnhanced = true;
		return true;
	}

	if (tocList) {
		enhanceToc();

		var tocObserver = new MutationObserver(function (mutations) {
			var needsRefresh = mutations.some(function (mutation) {
				return mutation.type === 'childList' ||
					(mutation.type === 'attributes' && mutation.attributeName === 'class');
			});

			if (needsRefresh) {
				enhanceToc();
			}
		});

		tocObserver.observe(tocList, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: ['class']
		});
	}

	/* Progress is measured against the reading column, not the full page. */
	var progressRaf = 0;

	function updateReadingProgress() {
		progressRaf = 0;

		var article = document.getElementById('article-content');
		if (!article) {
			return;
		}

		var rect = article.getBoundingClientRect();
		var pageTop = window.scrollY + rect.top;
		var max = Math.max(1, article.offsetHeight - window.innerHeight * 0.42);
		var raw = (window.scrollY - pageTop + window.innerHeight * 0.18) / max;
		var progress = Math.min(1, Math.max(0, raw));
		var percent = Math.round(progress * 100);

		if (progressNode) {
			progressNode.textContent = String(percent).padStart(2, '0') + '%';
		}

		if (progressFill) {
			progressFill.style.transform = 'scaleY(' + progress.toFixed(4) + ')';
		}

		if (tocEnhanced) {
			toc.setAttribute('data-reading-progress', String(percent));
		}
	}

	function requestProgressUpdate() {
		if (progressRaf) {
			return;
		}

		progressRaf = window.requestAnimationFrame(updateReadingProgress);
	}

	window.addEventListener('scroll', requestProgressUpdate, { passive: true });
	window.addEventListener('resize', requestProgressUpdate, { passive: true });
	updateReadingProgress();

	/* Reveal only elements that benefit from staged perception. */
	var revealTargets = Array.prototype.slice.call(
		essay.querySelectorAll(
			'.hu-dv, :scope > h2, :scope > .hu-design-essay__questions, :scope > .hu-design-essay__ethics, :scope > .hu-design-essay__future'
		)
	);

	if (reduceMotion || !('IntersectionObserver' in window)) {
		revealTargets.forEach(function (target) {
			target.classList.add('is-visible');
		});
		return;
	}

	var observer = new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (!entry.isIntersecting) {
				return;
			}

			entry.target.classList.add('is-visible');
			observer.unobserve(entry.target);
		});
	}, {
		root: null,
		rootMargin: '0px 0px -12% 0px',
		threshold: 0.14
	});

	revealTargets.forEach(function (target) {
		observer.observe(target);
	});
})();
