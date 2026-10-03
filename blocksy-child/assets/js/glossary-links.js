/* Definitions stay in server HTML. Native top layer, fixed-position fallback. */
(function () {
	'use strict';
	const links = document.querySelectorAll('[data-glossary-term] > a[aria-describedby]');
	if (!links.length) return;
	const hover = window.matchMedia('(any-hover: hover)');
	const nativePopover = 'popover' in HTMLElement.prototype;
	let active = null;
	let pending = null;
	let openTimer = 0;
	let closeTimer = 0;
	let frame = 0;

	function close() {
		clearTimeout(openTimer);
		clearTimeout(closeTimer);
		pending = null;
		if (!active) return;
		const tip = active.tip;
		if (nativePopover && tip.matches(':popover-open')) tip.hidePopover();
		tip.hidden = true;
		active = null;
	}
	function position() {
		frame = 0;
		if (!active) return;
		const { link, tip, pointer } = active;
		const rects = Array.from(link.getClientRects());
		const anchor = rects.find(rect => pointer && pointer.x >= rect.left && pointer.x <= rect.right && pointer.y >= rect.top && pointer.y <= rect.bottom) || rects[0];
		if (!anchor || !link.isConnected) return close();
		const viewport = window.visualViewport;
		const left = viewport ? viewport.offsetLeft : 0;
		const top = viewport ? viewport.offsetTop : 0;
		const width = viewport ? viewport.width : document.documentElement.clientWidth;
		const height = viewport ? viewport.height : window.innerHeight;
		if (anchor.bottom < top || anchor.top > top + height) return close();
		const gap = parseFloat(getComputedStyle(tip).paddingTop) / 2;
		const edge = gap * 2;
		tip.style.maxWidth = `${Math.max(0, width - edge * 2)}px`;
		tip.style.maxHeight = `${Math.max(0, height - edge * 2)}px`;
		const box = tip.getBoundingClientRect();
		const x = Math.max(left + edge, Math.min(anchor.left, left + width - box.width - edge));
		const above = anchor.top - box.height - gap;
		const y = above >= top + edge ? above : Math.max(top + edge, Math.min(anchor.bottom + gap, top + height - box.height - edge));
		tip.style.left = `${x}px`;
		tip.style.top = `${y}px`;
	}
	function open(record) {
		clearTimeout(openTimer);
		clearTimeout(closeTimer);
		if (record.dismissed || (!record.hovered && !record.focused)) return;
		if (active === record) return;
		close();
		active = record;
		// Moving to body avoids clipped/translated route containers in the fallback.
		document.body.append(record.tip);
		if (nativePopover) record.tip.setAttribute('popover', 'manual');
		record.tip.hidden = false;
		if (nativePopover) record.tip.showPopover();
		position();
	}
	function release(record) {
		clearTimeout(openTimer);
		clearTimeout(closeTimer);
		if (!record.hovered && !record.focused && !record.tipHovered) {
			record.dismissed = false;
			closeTimer = window.setTimeout(() => { if (active === record) close(); }, 180);
		}
	}
	links.forEach(link => {
		const tip = document.getElementById(link.getAttribute('aria-describedby'));
		if (!tip) return;
		const record = { link, tip, hovered: false, focused: false, tipHovered: false, dismissed: false, pointer: null };
		link.addEventListener('pointerenter', event => {
			if (!hover.matches || event.pointerType === 'touch') return;
			record.hovered = true;
			record.pointer = { x: event.clientX, y: event.clientY };
			clearTimeout(closeTimer);
			clearTimeout(openTimer);
			pending = record;
			openTimer = window.setTimeout(() => open(record), 160);
		});
		link.addEventListener('pointerleave', () => { record.hovered = false; release(record); });
		link.addEventListener('focus', () => {
			// Touch keeps the ordinary link action; keyboard focus shows the preview.
			if (!link.matches(':focus-visible')) return;
			record.focused = true;
			record.pointer = null;
			open(record);
		});
		link.addEventListener('blur', () => { record.focused = false; release(record); });
		tip.addEventListener('pointerenter', () => { record.tipHovered = true; clearTimeout(closeTimer); });
		tip.addEventListener('pointerleave', () => { record.tipHovered = false; release(record); });
	});
	document.addEventListener('keydown', event => {
		if (event.key === 'Escape' && (active || pending)) {
			if (active) active.dismissed = true;
			if (pending) pending.dismissed = true;
			close();
		}
	});
	document.addEventListener('pointerdown', event => {
		if (!active || !active.tip.contains(event.target)) close();
	}, { passive: true });
	function schedulePosition() {
		if (active && !frame) frame = requestAnimationFrame(position);
	}
	window.addEventListener('scroll', schedulePosition, { capture: true, passive: true });
	window.addEventListener('resize', schedulePosition, { passive: true });
	if (window.visualViewport) {
		window.visualViewport.addEventListener('resize', schedulePosition, { passive: true });
		window.visualViewport.addEventListener('scroll', schedulePosition, { passive: true });
	}
	window.addEventListener('pagehide', close);
}());
