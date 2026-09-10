/* Route-specific reader refinements for /wordpress-projekte-auslagern/. */
(function () {
    'use strict';

    if (typeof document === 'undefined') return;

    function init() {
        var rail = document.querySelector('.nexus-share-rail');
        var article = document.querySelector('.agency-outsourcing-article');
        if (!rail || !article) return;

        var startMarker = article.querySelector('h2') || article;
        var endMarker = document.querySelector('.nexus-related-content') ||
                        document.querySelector('.nexus-rating') ||
                        document.querySelector('.nexus-author-bio');
        var ticking = false;

        function update() {
            var start = startMarker.getBoundingClientRect().top + window.scrollY - 180;
            var end = endMarker ? endMarker.getBoundingClientRect().top + window.scrollY - 240 : Number.MAX_SAFE_INTEGER;
            var y = window.scrollY;
            var visible = y >= start && y < end;

            document.body.classList.toggle('ao-share-ready', visible);
            ticking = false;
        }

        function onScroll() {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(update);
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll);
        update();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();