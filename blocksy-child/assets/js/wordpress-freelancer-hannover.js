(() => {
	'use strict';

	const accordion = document.querySelector('[data-fr-accordion]');

	if (!accordion) {
		return;
	}

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
})();
