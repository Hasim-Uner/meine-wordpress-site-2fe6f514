/**
 * Legal Page Modal
 *
 * Intercepts clicks on links to /impressum/ and /datenschutz/,
 * fetches their content via AJAX and displays it in a full-screen
 * overlay instead of navigating away.
 *
 * @package Blocksy_Child
 */
(function () {
	'use strict';

	var SLUGS = ['impressum', 'datenschutz'];
	var overlay = null;
	var panel = null;
	var closeBtn = null;
	var cache = {};
	var isOpen = false;
	var legalStylesRequested = false;

	/**
	 * Load the shared legal-page stylesheet on demand.
	 *
	 * The standalone legal routes enqueue it server-side. Other routes only
	 * need it when the overlay opens; derive the CSS URL from this script so
	 * the same cache-busting query string is reused without global CSS weight.
	 */
	function ensureLegalStyles() {
		if (legalStylesRequested || document.getElementById('nexus-legal-pages-css-lazy')) return;
		legalStylesRequested = true;

		var script = document.querySelector('script[src*="/assets/js/legal-modal.js"]');
		if (!script || !script.src) return;

		var link = document.createElement('link');
		link.id = 'nexus-legal-pages-css-lazy';
		link.rel = 'stylesheet';
		link.href = script.src.replace('/assets/js/legal-modal.js', '/assets/css/legal-pages.css');
		document.head.appendChild(link);
	}

	/**
	 * Check if a URL points to a legal page we should intercept.
	 */
	function getLegalSlug(href) {
		if (!href) return null;
		try {
			var url = new URL(href, location.origin);
			if (url.origin !== location.origin) return null;
			var path = url.pathname.replace(/\/+$/, '');
			for (var i = 0; i < SLUGS.length; i++) {
				if (path === '/' + SLUGS[i]) return SLUGS[i];
			}
		} catch (e) {
			// invalid URL
		}
		return null;
	}

	/**
	 * Build the modal DOM (once).
	 */
	function ensureModal() {
		if (overlay) return;

		overlay = document.createElement('div');
		overlay.className = 'legal-modal';
		overlay.setAttribute('role', 'dialog');
		overlay.setAttribute('aria-modal', 'true');
		overlay.setAttribute('aria-label', 'Rechtliche Informationen');

		var backdrop = document.createElement('div');
		backdrop.className = 'legal-modal__backdrop';
		backdrop.addEventListener('click', close);

		panel = document.createElement('div');
		panel.className = 'legal-modal__panel';

		closeBtn = document.createElement('button');
		closeBtn.className = 'legal-modal__close';
		closeBtn.setAttribute('type', 'button');
		closeBtn.setAttribute('aria-label', 'Schließen');
		closeBtn.innerHTML = '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
		closeBtn.addEventListener('click', close);

		overlay.appendChild(backdrop);
		overlay.appendChild(panel);
		panel.appendChild(closeBtn);

		document.body.appendChild(overlay);
	}

	/**
	 * Fetch legal page content, extract the main shell, and cache it.
	 */
	function fetchContent(slug, callback) {
		if (cache[slug]) {
			callback(null, cache[slug]);
			return;
		}

		fetch('/' + slug + '/', { credentials: 'same-origin' })
			.then(function (res) {
				if (!res.ok) throw new Error(res.status);
				return res.text();
			})
			.then(function (html) {
				var parser = new DOMParser();
				var doc = parser.parseFromString(html, 'text/html');

				// Extract the content shell (.privacy-shell or .imprint-shell)
				var shell = doc.querySelector('.privacy-shell, .imprint-shell');
				if (!shell) {
					// Fallback: grab the whole <main>
					shell = doc.querySelector('main');
				}

				if (!shell) {
					callback(new Error('Content not found'));
					return;
				}

				// Also grab the <style> block inside <main> for self-contained styling
				var mainEl = doc.querySelector('main');
				var styleEl = mainEl ? mainEl.querySelector('style') : null;
				var result = '';
				if (styleEl) {
					result += styleEl.outerHTML;
				}
				result += shell.outerHTML;

				cache[slug] = result;
				callback(null, result);
			})
			.catch(function (err) {
				callback(err);
			});
	}

	/**
	 * Open the modal with content for a given slug.
	 */
	function open(slug) {
		ensureLegalStyles();
		ensureModal();
		isOpen = true;

		// Show loading state
		panel.innerHTML = '';
		panel.appendChild(closeBtn);

		var loader = document.createElement('div');
		loader.className = 'legal-modal__loader';
		loader.textContent = 'Wird geladen\u2026';
		panel.appendChild(loader);

		overlay.classList.add('legal-modal--open');
		document.documentElement.style.overflow = 'hidden';

		// Trap focus
		requestAnimationFrame(function () {
			closeBtn.focus();
		});

		document.addEventListener('keydown', onKeyDown);

		fetchContent(slug, function (err, html) {
			if (!isOpen) return; // closed while loading
			panel.innerHTML = '';
			panel.appendChild(closeBtn);

			if (err) {
				// Fallback: navigate normally
				close();
				location.href = '/' + slug + '/';
				return;
			}

			var body = document.createElement('div');
			body.className = 'legal-modal__body';
			body.innerHTML = html;
			panel.appendChild(body);

			// Re-bind any internal legal links within the modal
			var links = body.querySelectorAll('a');
			for (var i = 0; i < links.length; i++) {
				var innerSlug = getLegalSlug(links[i].href);
				if (innerSlug) {
					links[i].addEventListener('click', createHandler(innerSlug));
				}
			}

			panel.scrollTop = 0;
		});
	}

	/**
	 * Close the modal.
	 */
	function close() {
		if (!overlay) return;
		isOpen = false;
		overlay.classList.remove('legal-modal--open');
		document.documentElement.style.overflow = '';
		document.removeEventListener('keydown', onKeyDown);
	}

	/**
	 * Handle Escape key.
	 */
	function onKeyDown(e) {
		if (e.key === 'Escape') {
			close();
		}
	}

	/**
	 * Create a click handler for a given slug.
	 */
	function createHandler(slug) {
		return function (e) {
			e.preventDefault();
			open(slug);
		};
	}

	/**
	 * Bind click listeners to all matching links on the page.
	 */
	function bindLinks(root) {
		var links = (root || document).querySelectorAll('a[href]');
		for (var i = 0; i < links.length; i++) {
			var link = links[i];
			if (link.hasAttribute('data-legal-modal-bound')) continue;
			var slug = getLegalSlug(link.href);
			if (slug) {
				link.setAttribute('data-legal-modal-bound', '1');
				link.addEventListener('click', createHandler(slug));
			}
		}
	}

	// Init on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () { bindLinks(); });
	} else {
		bindLinks();
	}

	// Watch for dynamically added links
	if (typeof MutationObserver !== 'undefined') {
		new MutationObserver(function (mutations) {
			for (var i = 0; i < mutations.length; i++) {
				var nodes = mutations[i].addedNodes;
				for (var j = 0; j < nodes.length; j++) {
					if (nodes[j].nodeType === 1) {
						bindLinks(nodes[j]);
					}
				}
			}
		}).observe(document.body, { childList: true, subtree: true });
	}
})();
