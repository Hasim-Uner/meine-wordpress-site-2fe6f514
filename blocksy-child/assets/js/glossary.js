/* Progressive enhancement: every term and destination is already in the HTML. */
(function () {
	'use strict';
	const directory = document.querySelector('[data-glossary-directory]');
	if (!directory) return;
	const search = directory.querySelector('[data-glossary-search]');
	const filters = Array.from(directory.querySelectorAll('[data-glossary-filter]'));
	const count = directory.querySelector('[data-glossary-count]');
	const empty = directory.querySelector('[data-glossary-empty]');
	const normalize = (value) => value.toLocaleLowerCase('de').normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/ß/g, 'ss');
	const entries = Array.from(directory.querySelectorAll('[data-glossary-item]')).map((element) => ({
		element,
		area: element.dataset.area,
		text: normalize(element.dataset.search || '')
	}));
	let area = '';
	function update() {
		const words = normalize(search.value.trim()).split(/\s+/).filter(Boolean);
		let matches = 0;
		entries.forEach((entry) => {
			const visible = (!area || entry.area === area) && words.every((word) => entry.text.includes(word));
			entry.element.hidden = !visible;
			if (visible) matches += 1;
		});
		count.textContent = matches === 1 ? '1 Begriff' : `${matches} Begriffe`;
		empty.hidden = matches > 0;
		filters.forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.glossaryFilter === area)));
	}
	search.addEventListener('input', update);
	filters.forEach((button) => button.addEventListener('click', () => {
		area = button.dataset.glossaryFilter;
		update();
	}));
	directory.querySelector('[data-glossary-reset]').addEventListener('click', () => {
		search.value = '';
		area = '';
		update();
		search.focus();
	});
	directory.querySelector('[data-glossary-controls]').hidden = false;
	update();
}());
