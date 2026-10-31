/**
 * KI-Assistent: chat window, streaming, safe markdown and handover card.
 *
 * Loaded as a module only after the first click on the facade
 * (inc/chat-assistant/facade.php). The history lives in sessionStorage and is
 * sent with every message; the server keeps nothing. No innerHTML anywhere:
 * model text is rendered through createElement/textContent only.
 */

const STORE_KEY = 'hu-chat-v1';
const MAX_CHARS = 2000;
const MAX_USER_TURNS = 20;
const CARD_NOTE = '[Übergabe-Karte mit Name, E-Mail und Zusammenfassung angezeigt]';

let instance = null;

const SVG = 'http://www.w3.org/2000/svg';
const arrowIcon = () => {
	const svg = document.createElementNS(SVG, 'svg');
	svg.setAttribute('viewBox', '0 0 24 24');
	svg.setAttribute('width', '20');
	svg.setAttribute('height', '20');
	svg.setAttribute('aria-hidden', 'true');
	svg.setAttribute('focusable', 'false');
	const path = document.createElementNS(SVG, 'path');
	path.setAttribute('d', 'M5 12h13M13 6l6 6-6 6');
	path.setAttribute('fill', 'none');
	path.setAttribute('stroke', 'currentColor');
	path.setAttribute('stroke-width', '2.2');
	path.setAttribute('stroke-linecap', 'round');
	path.setAttribute('stroke-linejoin', 'round');
	svg.append(path);
	return svg;
};

const el = (tag, attrs = {}, children = []) => {
	const node = document.createElement(tag);
	for (const [key, value] of Object.entries(attrs)) {
		if (value === false || value === null || value === undefined) continue;
		if (key === 'class') node.className = value;
		else if (key === 'text') node.textContent = value;
		else if (key.startsWith('on')) node.addEventListener(key.slice(2), value);
		else node.setAttribute(key, value === true ? '' : String(value));
	}
	for (const child of [].concat(children)) {
		if (child !== null && child !== undefined && child !== false) node.append(child);
	}
	return node;
};

const storage = {
	read() {
		try {
			const raw = window.sessionStorage.getItem(STORE_KEY);
			const data = raw ? JSON.parse(raw) : null;
			return data && Array.isArray(data.messages) ? data : null;
		} catch (error) {
			return null;
		}
	},
	write(state) {
		try {
			window.sessionStorage.setItem(STORE_KEY, JSON.stringify(state));
		} catch (error) {
			/* Chat keeps working without memory. */
		}
	},
	clear() {
		try {
			window.sessionStorage.removeItem(STORE_KEY);
		} catch (error) {
			/* ignore */
		}
	},
};

/** Internal links and the configured booking links only. Everything else stays text. */
let bookingPrefixes = [];
const safeHref = (href) => {
	try {
		const url = new URL(href, window.location.origin);
		if (url.protocol !== 'https:' && url.protocol !== 'http:') return null;
		if (url.host === window.location.host) return url.href;
		const bare = url.origin + url.pathname;
		return bookingPrefixes.some((prefix) => bare === prefix) ? url.href : null;
	} catch (error) {
		return null;
	}
};
const isExternal = (href) => {
	try {
		return new URL(href, window.location.origin).host !== window.location.host;
	} catch (error) {
		return false;
	}
};
const link = (href, text) => el('a', isExternal(href) ? { href, text, target: '_blank', rel: 'noopener' } : { href, text });

/** Inline markdown: **bold** and [text](url). */
const isBooking = (href) => {
	try {
		const url = new URL(href, window.location.origin);
		return bookingPrefixes.includes(url.origin + url.pathname);
	} catch (error) {
		return false;
	}
};

const calendarIcon = () => {
	const svg = document.createElementNS(SVG, 'svg');
	svg.setAttribute('viewBox', '0 0 24 24');
	svg.setAttribute('width', '18');
	svg.setAttribute('height', '18');
	svg.setAttribute('aria-hidden', 'true');
	svg.setAttribute('focusable', 'false');
	for (const d of ['M4 6.5A1.5 1.5 0 0 1 5.5 5h13A1.5 1.5 0 0 1 20 6.5v12a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 18.5z', 'M4 10h16M8.5 3v4M15.5 3v4']) {
		const path = document.createElementNS(SVG, 'path');
		path.setAttribute('d', d);
		path.setAttribute('fill', 'none');
		path.setAttribute('stroke', 'currentColor');
		path.setAttribute('stroke-width', '1.8');
		path.setAttribute('stroke-linecap', 'round');
		svg.append(path);
	}
	return svg;
};

/** Booking links become a button; cal-embed.js opens it as the Cal.com overlay. */
const bookingButton = (href, label) => el('a', {
	class: 'hu-chat__cta',
	href,
	target: '_blank',
	rel: 'noopener',
	'data-track-action': 'chat_booking',
	'data-track-category': 'lead_gen',
	'data-track-section': 'chat_answer',
}, [calendarIcon(), el('span', { text: label }), arrowIcon()]);

/** Inline markdown: **bold** (may contain links), [text](url) and bare booking URLs. */
const inline = (text) => {
	const out = document.createDocumentFragment();
	const pattern = /\*\*(.+?)\*\*|\[([^\]]+)\]\(([^)\s]+)\)|(https?:\/\/[^\s)<>]+)/g;
	let last = 0;
	let match;
	while ((match = pattern.exec(text))) {
		if (match.index > last) out.append(text.slice(last, match.index));
		if (match[1] !== undefined) {
			const inner = inline(match[1]);
			// A bold wrapper around a lone booking link is decoration, not emphasis.
			const only = inner.childNodes.length === 1 && inner.firstChild.classList?.contains('hu-chat__cta');
			out.append(only ? inner : el('strong', {}, inner));
		} else if (match[2] !== undefined) {
			const href = safeHref(match[3]);
			if (href && isBooking(href)) out.append(bookingButton(href, match[2]));
			else out.append(href ? link(href, match[2]) : match[2]);
		} else {
			const href = safeHref(match[4]);
			if (href && isBooking(href)) out.append(bookingButton(href, 'Termin mit Haşim wählen'));
			else out.append(href ? link(href, match[4]) : match[4]);
		}
		last = pattern.lastIndex;
	}
	if (last < text.length) out.append(text.slice(last));
	return out;
};

/** Block markdown: paragraphs, bullet and numbered lists, headings as bold lines. */
const renderMarkdown = (target, source) => {
	target.replaceChildren();
	const lines = source.replace(/\r/g, '').split('\n');
	let list = null;
	let para = [];
	const flushPara = () => {
		if (para.length) {
			const p = el('p');
			para.forEach((line, i) => {
				if (i) p.append(el('br'));
				p.append(inline(line));
			});
			target.append(p);
			para = [];
		}
	};
	for (const raw of lines) {
		const line = raw.trimEnd();
		const bullet = /^\s*[-*•]\s+(.*)$/.exec(line);
		const numbered = /^\s*\d+[.)]\s+(.*)$/.exec(line);
		const heading = /^#{1,6}\s+(.*)$/.exec(line);
		if (bullet || numbered) {
			flushPara();
			const tag = numbered ? 'ol' : 'ul';
			if (!list || list.tagName.toLowerCase() !== tag) {
				list = el(tag);
				target.append(list);
			}
			list.append(el('li', {}, inline((bullet || numbered)[1])));
			continue;
		}
		list = null;
		if (!line.trim()) {
			flushPara();
			continue;
		}
		if (heading) {
			flushPara();
			target.append(el('p', {}, el('strong', { text: heading[1] })));
			continue;
		}
		para.push(line);
	}
	flushPara();
};

/** Minimal SSE reader over fetch; yields {event, data}. */
async function* readEvents(response) {
	const reader = response.body.getReader();
	const decoder = new TextDecoder();
	let buffer = '';
	for (;;) {
		const { value, done } = await reader.read();
		if (done) break;
		buffer += decoder.decode(value, { stream: true }).replace(/\r\n/g, '\n');
		let boundary;
		while ((boundary = buffer.indexOf('\n\n')) !== -1) {
			const frame = buffer.slice(0, boundary);
			buffer = buffer.slice(boundary + 2);
			const event = /^event: (.+)$/m.exec(frame)?.[1];
			const data = /^data: (.+)$/m.exec(frame)?.[1];
			if (!event || !data) continue; // padding comments
			try {
				yield { event, data: JSON.parse(data) };
			} catch (error) {
				/* skip malformed frame */
			}
		}
	}
}

class Chat {
	constructor(root, cfg) {
		this.root = root;
		this.cfg = cfg;
		this.orb = root.querySelector('.hu-chat__orb');
		this.state = storage.read() || { messages: [] };
		this.busy = false;
		bookingPrefixes = (cfg.booking || []).map((href) => {
			try {
				const url = new URL(href);
				return url.origin + url.pathname;
			} catch (error) {
				return '';
			}
		}).filter(Boolean);
		this.build();
		this.restore();
	}

	build() {
		this.log = el('div', { class: 'hu-chat__log', role: 'log', 'aria-live': 'polite', 'aria-relevant': 'additions', tabindex: '-1' });
		this.greet = el('h2', { class: 'hu-chat__greet', id: 'hu-chat-greet' });
		this.intro = el('div', { class: 'hu-chat__intro' }, [
			el('p', { class: 'hu-chat__kicker', text: 'WordPress · Tracking · Conversion' }),
			this.greet,
			el('p', { class: 'hu-chat__description', text: 'Eine Frage zum Angebot oder zum nächsten Schritt?' }),
		]);
		this.input = el('textarea', {
			class: 'hu-chat__input',
			rows: 1,
			maxlength: MAX_CHARS,
			placeholder: 'Was möchten Sie wissen?',
			'aria-label': 'Ihre Frage an den KI-Assistenten',
			oninput: () => this.autosize(),
			onkeydown: (event) => {
				if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
					event.preventDefault();
					this.submit();
				}
			},
		});
		this.send = el('button', { type: 'submit', class: 'hu-chat__send', 'aria-label': 'Senden' }, arrowIcon());
		this.form = el('form', { class: 'hu-chat__form', onsubmit: (event) => { event.preventDefault(); this.submit(); } }, [this.input, this.send]);
		this.errorStatus = el('p', { class: 'hu-chat__status', role: 'status', hidden: true });
		this.chips = el('div', { class: 'hu-chat__chips' });
		this.resetButton = el('button', { type: 'button', class: 'hu-chat__reset', text: 'Neues Gespräch', hidden: true, onclick: () => this.reset() });
		this.bar = el('section', {
			id: 'hu-chat-bar',
			class: 'hu-chat__bar tafel tafel--chat',
			'aria-label': 'NEXUS · KI-Projektassistent',
			hidden: true,
			onkeydown: (event) => {
				if (event.key === 'Escape') {
					event.stopPropagation();
					this.close();
				}
			},
		}, [
			el('div', { class: 'hu-chat__top' }, [
				el('div', { class: 'hu-chat__identity' }, [
					el('p', { class: 'hu-chat__name' }, ['NEXUS', el('span', { 'aria-hidden': 'true', text: '·' })]),
				]),
				el('button', { type: 'button', class: 'hu-chat__close', 'aria-label': 'KI-Assistent einklappen', onclick: () => this.close() }, el('span', { 'aria-hidden': 'true', text: '−' })),
			]),
			el('div', { class: 'hu-chat__welcome' }, [this.intro, this.chips]),
			this.log,
			this.form,
			this.errorStatus,
			el('p', { class: 'hu-chat__meta' }, [
				'KI-Assistent · bitte keine sensiblen Daten · ',
				el('a', { href: this.cfg.privacy, text: 'Datenschutz' }),
				this.resetButton,
			]),
		]);
		this.root.append(this.bar);
		// VisualViewport also accounts for the on-screen keyboard on mobile.
		const fitViewport = () => {
			const viewport = window.visualViewport;
			const height = viewport ? viewport.height : window.innerHeight;
			const keyboard = viewport ? Math.max(0, window.innerHeight - viewport.height - viewport.offsetTop) : 0;
			this.root.style.setProperty('--hu-chat-viewport', `${height}px`);
			this.root.style.setProperty('--hu-chat-keyboard', `${keyboard}px`);
		};
		fitViewport();
		window.addEventListener('resize', fitViewport, { passive: true });
		window.visualViewport?.addEventListener('resize', fitViewport, { passive: true });
		window.visualViewport?.addEventListener('scroll', fitViewport, { passive: true });
	}

	hasConversation() {
		return this.state.messages.length > 0;
	}

	restore() {
		this.log.replaceChildren();
		for (const message of this.state.messages) {
			if (message.card) {
				this.renderCard(message.card, message.cardDone);
			} else if (message.text && message.text !== CARD_NOTE) {
				this.addBubble(message.role, message.text);
			}
		}
		this.sync();
	}

	/** Log, chips and reset follow the conversation state. */
	sync() {
		const talking = this.hasConversation();
		this.bar.classList.toggle('is-talking', talking);
		this.resetButton.hidden = !talking;
		this.chips.replaceChildren();
		if (talking) {
			return;
		}
		for (const chip of this.cfg.intro?.chips || []) {
			const attrs = {
				class: 'hu-chat__chip',
				'data-track-action': chip.track || 'chat_starter',
				'data-track-category': chip.href ? 'lead_gen' : 'engagement',
				'data-track-section': 'chat_bar',
				text: chip.label,
			};
			this.chips.append(chip.href
				? el('a', { ...attrs, href: chip.href, ...(isExternal(chip.href) ? { target: '_blank', rel: 'noopener' } : {}) })
				: el('button', { ...attrs, type: 'button', onclick: () => this.submit(chip.send) }));
		}
	}

	/** The welcome stays readable immediately, including with reduced motion. */
	typeGreeting() {
		if (this.hasConversation()) return;
		this.greet.textContent = this.cfg.intro?.greeting || 'Was haben Sie vor?';
	}

	cyclePlaceholder() {
		this.input.placeholder = this.hasConversation() ? 'Ihre Antwort …' : 'Was möchten Sie wissen?';
	}

	addBubble(role, text) {
		const body = el('div', { class: 'hu-chat__text' });
		const bubble = el('div', { class: `hu-chat__msg hu-chat__msg--${role}` }, body);
		if (role === 'assistant') renderMarkdown(body, text);
		else body.textContent = text;
		this.log.append(bubble);
		this.scroll();
		return body;
	}

	scroll() {
		this.log.scrollTop = this.log.scrollHeight;
	}

	autosize() {
		this.input.style.height = 'auto';
		this.input.style.height = Math.min(this.input.scrollHeight, 140) + 'px';
		this.form.classList.toggle('has-text', this.input.value.trim() !== '');
	}

	save() {
		storage.write(this.state);
	}

	/** Auto-open (scroll trigger) never moves focus, so no keyboard pops up. */
	open(opts = {}) {
		if (!this.bar.hidden) return this;
		this.typeGreeting();
		this.cyclePlaceholder();
		this.returnTo = opts.returnTo || this.orb;
		this.bar.hidden = false;
		this.bar.classList.toggle('is-auto', !!opts.auto);
		this.root.classList.add('is-open');
		this.orb.setAttribute('aria-expanded', 'true');
		this.autosize();
		this.scroll();
		if (!opts.auto) window.requestAnimationFrame(() => this.input.focus({ preventScroll: true }));
		return this;
	}

	close() {
		const hadFocus = this.bar.contains(document.activeElement);
		this.bar.hidden = true;
		this.root.classList.remove('is-open');
		this.orb.setAttribute('aria-expanded', 'false');
		if (!hadFocus) return;
		// Back to whatever opened the chat; the launcher may be hidden at the footer.
		const target = this.returnTo && this.returnTo.isConnected ? this.returnTo : this.orb;
		target.focus();
		if (document.activeElement !== target) this.orb.focus();
	}

	reset() {
		if (this.busy) return;
		this.state = { messages: [] };
		storage.clear();
		this.errorStatus.hidden = true;
		this.errorStatus.textContent = '';
		this.restore();
		this.typeGreeting();
		this.cyclePlaceholder();
		this.input.focus({ preventScroll: true });
	}

	setBusy(busy) {
		this.busy = busy;
		this.send.disabled = busy;
		this.bar.classList.toggle('is-busy', busy);
	}

	/** Plain role/text turns only; cards travel as a fixed note. */
	payloadMessages() {
		return this.state.messages
			.filter((m) => m.role === 'user' || m.role === 'assistant')
			.map((m) => ({ role: m.role, text: m.card ? CARD_NOTE : String(m.text || '') }))
			.filter((m) => m.text.trim());
	}

	async submit(preset) {
		if (this.busy) return;
		const text = (preset ?? this.input.value).trim().slice(0, MAX_CHARS);
		if (!text) return;
		const turns = this.state.messages.filter((m) => m.role === 'user').length;
		if (turns >= MAX_USER_TURNS) {
			this.addBubble('assistant', `Dieses Gespräch ist lang geworden. Bitte beginnen Sie ein neues Gespräch oder schreiben Sie direkt an ${this.cfg.contact}.`);
			return;
		}
		this.input.value = '';
		this.autosize();
		this.state.messages.push({ role: 'user', text });
		this.save();
		this.sync();
		this.cyclePlaceholder();
		this.addBubble('user', text);
		await this.ask();
	}

	async ask() {
		this.errorStatus.hidden = true;
		this.errorStatus.textContent = '';
		this.setBusy(true);
		const body = this.addBubble('assistant', '');
		body.parentElement.classList.add('is-typing');
		body.append(el('span', { class: 'hu-chat__typing', 'aria-label': 'Assistent schreibt' }, [el('i'), el('i'), el('i')]));
		let answer = '';
		let card = null;
		let failed = '';
		try {
			const headers = { 'Content-Type': 'application/json', Accept: 'text/event-stream' };
			if (this.cfg.nonce) headers['X-WP-Nonce'] = this.cfg.nonce;
			const response = await fetch(this.cfg.endpoint, {
				method: 'POST',
				credentials: 'same-origin',
				cache: 'no-store',
				headers,
				body: JSON.stringify({ messages: this.payloadMessages(), page: this.cfg.page }),
			});
			const type = response.headers.get('Content-Type') || '';
			if (!response.ok || !type.startsWith('text/event-stream') || !response.body) {
				let message = '';
				try {
					message = (await response.json()).message || '';
				} catch (error) {
					/* non-JSON error */
				}
				throw new Error(message || `Der Assistent ist gerade nicht erreichbar. Schreiben Sie bitte an ${this.cfg.contact}.`);
			}
			for await (const { event, data } of readEvents(response)) {
				if (event === 'text' && typeof data.text === 'string') {
					answer += data.text;
					body.parentElement.classList.remove('is-typing');
					renderMarkdown(body, answer);
					this.scroll();
				} else if (event === 'tool' && data.name === 'anfrage_uebergeben' && data.input) {
					card = data.input;
				} else if (event === 'error') {
					failed = data.text || 'Der Assistent ist gerade nicht erreichbar.';
				}
			}
		} catch (error) {
			failed = error.message || 'Der Assistent ist gerade nicht erreichbar.';
		}
		body.parentElement.classList.remove('is-typing');
		if (failed && !answer && !card) {
			this.errorStatus.textContent = failed;
			this.errorStatus.hidden = false;
			body.parentElement.classList.add('is-error');
			body.replaceChildren(el('p', { text: failed }));
			// The turn goes back into the input so the visitor can resend it.
			const lost = this.state.messages.pop();
			if (lost && lost.role === 'user' && !this.input.value) this.input.value = lost.text;
			this.save();
			this.sync();
		} else {
			if (answer) {
				this.state.messages.push({ role: 'assistant', text: answer });
			} else {
				body.parentElement.remove();
			}
			if (card) {
				this.state.messages.push({ role: 'assistant', card, cardDone: false });
				this.renderCard(card, false);
			}
			this.save();
		}
		this.setBusy(false);
		this.autosize();
		if (!this.bar.hidden && (this.form.contains(document.activeElement) || document.activeElement === document.body)) {
			this.input.focus({ preventScroll: true });
		}
	}

	renderCard(card, done) {
		const id = 'hu-chat-card-' + Math.random().toString(36).slice(2, 8);
		const field = (name, label, value, type = 'text', required = false) => el('label', { class: 'hu-chat__field' }, [
			el('span', { text: label + (required ? '' : ' (optional)') }),
			el('input', { type, name, value: value || '', required, autocomplete: name === 'email' ? 'email' : name === 'firma' ? 'organization' : 'name', maxlength: name === 'firma' ? 160 : 120 }),
		]);
		const status = el('p', { class: 'hu-chat__card-status', role: 'status' });
		const form = el('form', { class: 'hu-chat__card', 'aria-labelledby': id + '-h' }, [
			el('h3', { id: id + '-h', text: 'Anfrage an Haşim Üner' }),
			el('p', { class: 'hu-chat__card-sub', text: 'Bitte prüfen. Gesendet wird erst mit Ihrem Klick.' }),
			field('name', 'Name', card.name, 'text', true),
			field('email', 'E-Mail', card.email, 'email', true),
			field('firma', 'Firma', card.firma),
			el('div', { class: 'hu-chat__summary' }, [
				el('span', { text: 'Zusammenfassung' }),
				el('p', { text: card.zusammenfassung || '' }),
			]),
			el('label', { class: 'hu-chat__check' }, [
				el('input', { type: 'checkbox', name: 'verlauf' }),
				el('span', { text: 'Gesprächsverlauf mitsenden' }),
			]),
			el('label', { class: 'hu-chat__check' }, [
				el('input', { type: 'checkbox', name: 'consent', required: true }),
				el('span', {}, ['Ich stimme zu, dass meine Angaben zur Bearbeitung meiner Anfrage verarbeitet werden. Mehr dazu in der ', el('a', { href: this.cfg.privacy, text: 'Datenschutzerklärung' }), '.']),
			]),
			el('input', { type: 'text', name: 'company_website', tabindex: '-1', autocomplete: 'off', class: 'hu-chat__hp', 'aria-hidden': 'true' }),
			el('div', { class: 'hu-chat__card-actions' }, [
				el('button', { type: 'submit', class: 'hu-chat__primary', text: 'Anfrage senden', 'data-track-action': 'chat_handover_submit', 'data-track-category': 'lead_gen', 'data-track-section': 'chat_bar' }),
			]),
			status,
		]);
		form.addEventListener('submit', (event) => {
			event.preventDefault();
			this.handover(form, card, status);
		});
		if (done) {
			form.querySelectorAll('input,button').forEach((n) => { n.disabled = true; });
			status.textContent = 'Gesendet.';
		}
		this.log.append(form);
		this.scroll();
	}

	async handover(form, card, status) {
		const button = form.querySelector('button[type="submit"]');
		const data = new FormData(form);
		button.disabled = true;
		status.textContent = 'Wird gesendet …';
		const includeLog = data.get('verlauf') === 'on';
		const payload = {
			name: String(data.get('name') || '').trim(),
			email: String(data.get('email') || '').trim(),
			firma: String(data.get('firma') || '').trim(),
			weg: card.weg,
			zusammenfassung: card.zusammenfassung,
			naechster_schritt: card.naechster_schritt || '',
			page: this.cfg.page,
			company_website: String(data.get('company_website') || ''),
			verlauf_mitsenden: includeLog,
			verlauf: includeLog ? this.payloadMessages().filter((m) => m.text !== CARD_NOTE) : [],
		};
		try {
			const headers = { 'Content-Type': 'application/json' };
			if (this.cfg.nonce) headers['X-WP-Nonce'] = this.cfg.nonce;
			const response = await fetch(this.cfg.handover, { method: 'POST', credentials: 'same-origin', headers, body: JSON.stringify(payload) });
			const result = await response.json().catch(() => ({}));
			if (!response.ok || !result.ok) throw new Error(result.error || result.message || '');
			status.textContent = result.message || 'Gesendet.';
			form.querySelectorAll('input,button').forEach((n) => { n.disabled = true; });
			const stored = this.state.messages.find((m) => m.card === card) || [...this.state.messages].reverse().find((m) => m.card);
			if (stored) stored.cardDone = true;
			this.save();
		} catch (error) {
			status.textContent = error.message || `Senden hat nicht geklappt. Bitte direkt an ${this.cfg.contact} schreiben.`;
			button.disabled = false;
		}
	}
}

export function mount(root, cfg) {
	if (!instance) instance = new Chat(root, cfg);
	return instance;
}
