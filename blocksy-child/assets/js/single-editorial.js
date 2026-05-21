/* ============================================================
   NEXUS Single Post — Editorial Behaviours
   Reading progress, sticky share rail, back-to-top, FAQ accordion,
   scroll reveal observer and rating widget (REST → post meta).

   Config injected via wp_localize_script:
     window.NexusSingleEditorial = {
       restEndpoint: '/wp-json/nexus/v1/post-rating',
       nonce:        '...',
       postId:       42,
       successMsg:   'Danke...',
       errorMsg:     'Das hat nicht geklappt...',
       shareUrl:     'https://...',
       shareTitle:   'Title'
     };
   ============================================================ */
(function () {
    'use strict';

    if (typeof document === 'undefined') return;

    var config = window.NexusSingleEditorial || {};

    document.addEventListener('DOMContentLoaded', function () {
        initProgressBar();
        initShareRail();
        initBackToTop();
        initFAQ();
        initReveal();
        initRating();
        initShareButtons();
    });

    /* ----------------------------------------------------------
       Reading progress bar
       ---------------------------------------------------------- */
    function initProgressBar() {
        var bar = document.querySelector('.nexus-reading-progress');
        var article = document.querySelector('.nexus-article-content');
        if (!bar || !article) return;

        var ticking = false;
        function update() {
            var rect = article.getBoundingClientRect();
            var top = rect.top + window.scrollY;
            var height = rect.height || 1;
            var scrolled = window.scrollY - top + window.innerHeight;
            var progress = Math.max(0, Math.min(100, (scrolled / height) * 100));
            bar.style.width = progress + '%';
            ticking = false;
        }
        function onScroll() {
            if (!ticking) {
                window.requestAnimationFrame(update);
                ticking = true;
            }
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll);
        update();
    }

    /* ----------------------------------------------------------
       Sticky share rail visibility
       ---------------------------------------------------------- */
    function initShareRail() {
        var rail = document.querySelector('.nexus-share-rail');
        if (!rail) return;

        var hero = document.querySelector('.nexus-article-hero');
        var endMarker = document.querySelector('.nexus-author-bio') ||
                        document.querySelector('.nexus-related-content') ||
                        document.querySelector('.nexus-rating');

        function update() {
            var y = window.scrollY;
            var heroBottom = hero ? hero.offsetTop + hero.offsetHeight : 600;
            var footerTop = endMarker ? endMarker.offsetTop : Number.MAX_SAFE_INTEGER;
            if (y > heroBottom - 200 && y < footerTop - 200) {
                rail.classList.add('is-visible');
            } else {
                rail.classList.remove('is-visible');
            }
        }
        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
        update();
    }

    /* ----------------------------------------------------------
       Back to top button
       ---------------------------------------------------------- */
    function initBackToTop() {
        var btn = document.querySelector('.nexus-back-to-top');
        if (!btn) return;

        function update() {
            if (window.scrollY > 600) {
                btn.classList.add('is-visible');
            } else {
                btn.classList.remove('is-visible');
            }
        }
        window.addEventListener('scroll', update, { passive: true });
        update();

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ----------------------------------------------------------
       FAQ accordion (delegated, works for content typed by editor)
       ---------------------------------------------------------- */
    function initFAQ() {
        document.addEventListener('click', function (event) {
            var question = event.target.closest('.faq-question');
            if (!question) return;
            var item = question.closest('.faq-item');
            if (!item) return;
            event.preventDefault();
            var isOpen = item.classList.toggle('is-open');
            question.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        // Initialise ARIA + IDs so screen readers can use the accordion.
        document.querySelectorAll('.faq-item').forEach(function (item, index) {
            var question = item.querySelector('.faq-question');
            var answer = item.querySelector('.faq-answer');
            if (!question || !answer) return;
            if (!answer.id) answer.id = 'nexus-faq-answer-' + index;
            question.setAttribute('aria-expanded', 'false');
            question.setAttribute('aria-controls', answer.id);
            question.setAttribute('type', 'button');
        });
    }

    /* ----------------------------------------------------------
       Scroll reveal observer (.nexus-reveal -> .is-in)
       ---------------------------------------------------------- */
    function initReveal() {
        var els = document.querySelectorAll('.nexus-reveal');
        if (!els.length || !('IntersectionObserver' in window)) {
            els.forEach(function (el) { el.classList.add('is-in'); });
            return;
        }
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -80px 0px' });
        els.forEach(function (el) { observer.observe(el); });
    }

    /* ----------------------------------------------------------
       Rating widget
       ---------------------------------------------------------- */
    function initRating() {
        var root = document.querySelector('.nexus-rating');
        if (!root) return;

        var buttons = root.querySelectorAll('.nexus-rating__btn');
        var feedback = root.querySelector('.nexus-rating__feedback');
        var thanks = root.querySelector('.nexus-rating__thanks');
        var errorEl = root.querySelector('.nexus-rating__error');
        var textarea = root.querySelector('textarea');
        var submitBtn = root.querySelector('.nexus-rating__submit');
        var skipBtn = root.querySelector('.nexus-rating__skip');

        var state = { rating: null, submitted: false };

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (state.submitted) return;
                buttons.forEach(function (b) { b.classList.remove('is-selected'); });
                btn.classList.add('is-selected');
                state.rating = btn.getAttribute('data-rating');

                if (feedback) feedback.classList.add('is-shown');
                if (errorEl) errorEl.classList.remove('is-shown');

                // Push the rating itself immediately (text is optional).
                sendRating(state.rating, '');
                pushDataLayer({
                    event: 'post_rating',
                    rating: state.rating,
                    post_id: config.postId || null
                });
            });
        });

        if (submitBtn) {
            submitBtn.addEventListener('click', function () {
                if (!state.rating) return;
                var text = textarea ? textarea.value.trim() : '';
                if (text) {
                    sendRating(state.rating, text);
                    pushDataLayer({
                        event: 'post_rating_feedback',
                        rating: state.rating,
                        post_id: config.postId || null,
                        feedback_length: text.length
                    });
                }
                showThanks();
            });
        }

        if (skipBtn) {
            skipBtn.addEventListener('click', function () { showThanks(); });
        }

        function showThanks() {
            state.submitted = true;
            if (feedback) feedback.classList.remove('is-shown');
            if (thanks) thanks.classList.add('is-shown');
            buttons.forEach(function (b) { b.disabled = true; });
        }

        function sendRating(rating, text) {
            if (!config.restEndpoint || !config.postId) return;
            try {
                var body = {
                    postId: config.postId,
                    rating: rating,
                    feedback: text || '',
                    nonce: config.nonce || ''
                };
                fetch(config.restEndpoint, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': config.nonce || ''
                    },
                    body: JSON.stringify(body)
                }).then(function (response) {
                    if (!response.ok && errorEl) {
                        errorEl.textContent = config.errorMsg || 'Bitte erneut versuchen.';
                        errorEl.classList.add('is-shown');
                    }
                }).catch(function () {
                    if (errorEl) {
                        errorEl.textContent = config.errorMsg || 'Bitte erneut versuchen.';
                        errorEl.classList.add('is-shown');
                    }
                });
            } catch (err) {
                // No-op — silently swallow so the UX stays smooth even on
                // restricted browsers.
            }
        }
    }

    /* ----------------------------------------------------------
       Vertical share rail buttons (+ copy link)
       ---------------------------------------------------------- */
    function initShareButtons() {
        var shareUrl = config.shareUrl || window.location.href;
        var shareTitle = config.shareTitle || document.title;

        document.querySelectorAll('[data-nexus-share]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var type = btn.getAttribute('data-nexus-share');
                var encodedUrl = encodeURIComponent(shareUrl);
                var encodedTitle = encodeURIComponent(shareTitle);
                var target = '';
                switch (type) {
                    case 'linkedin':
                        target = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodedUrl;
                        break;
                    case 'twitter':
                    case 'x':
                        target = 'https://twitter.com/intent/tweet?url=' + encodedUrl + '&text=' + encodedTitle;
                        break;
                    case 'email':
                        window.location.href = 'mailto:?subject=' + encodedTitle + '&body=' + encodedUrl;
                        return;
                    case 'copy':
                        copyToClipboard(shareUrl, btn);
                        return;
                }
                if (target) {
                    window.open(target, '_blank', 'noopener,width=600,height=520');
                }
                pushDataLayer({ event: 'post_share', method: type, post_id: config.postId || null });
            });
        });
    }

    function copyToClipboard(text, btn) {
        var done = function () {
            var original = btn.innerHTML;
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
            setTimeout(function () { btn.innerHTML = original; }, 2000);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(function () {});
        } else {
            var input = document.createElement('input');
            input.value = text;
            document.body.appendChild(input);
            input.select();
            try { document.execCommand('copy'); done(); } catch (e) {}
            document.body.removeChild(input);
        }
    }

    function pushDataLayer(data) {
        try {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push(data);
        } catch (e) {}
    }
})();
