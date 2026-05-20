/**
 * Blog archive filtering.
 *
 * Combines category buttons with a lightweight client-side search.
 */
document.addEventListener('DOMContentLoaded', function() {
    const blogWrapper = document.querySelector('[data-blog-filter-root]');
    if (!blogWrapper) {
        return;
    }

    const filterButtons = Array.from(blogWrapper.querySelectorAll('.hu-filter-btn'));
    const postCards = Array.from(blogWrapper.querySelectorAll('.post-card'));
    const searchInput = blogWrapper.querySelector('[data-blog-search]');
    const emptyState = blogWrapper.querySelector('[data-blog-empty]');
    let activeFilter = 'all';

    const normalize = (value) => (value || '')
        .toString()
        .trim()
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

    const getSearchValue = () => (searchInput ? normalize(searchInput.value) : '');

    const cardMatchesCategory = (card) => {
        if (activeFilter === 'all') {
            return true;
        }

        try {
            const categories = JSON.parse(card.getAttribute('data-categories') || '[]');
            return Array.isArray(categories) && categories.includes(activeFilter);
        } catch (error) {
            return false;
        }
    };

    const cardMatchesSearch = (card) => {
        const query = getSearchValue();
        if (!query) {
            return true;
        }

        return (card.getAttribute('data-search') || '').includes(query);
    };

    const applyFilters = () => {
        let visibleCount = 0;

        postCards.forEach((card) => {
            const isVisible = cardMatchesCategory(card) && cardMatchesSearch(card);
            card.classList.toggle('hide', !isVisible);

            if (isVisible) {
                visibleCount += 1;
            }
        });

        if (emptyState) {
            emptyState.hidden = visibleCount > 0;
        }
    };

    filterButtons.forEach((button) => {
        button.addEventListener('click', function() {
            filterButtons.forEach((btn) => {
                btn.classList.remove('is-active');
                btn.setAttribute('aria-pressed', 'false');
            });

            this.classList.add('is-active');
            this.setAttribute('aria-pressed', 'true');
            activeFilter = this.dataset.filter || 'all';
            applyFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
});
