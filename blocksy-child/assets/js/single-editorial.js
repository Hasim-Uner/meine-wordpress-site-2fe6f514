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

    function initEditorialLayer() {
        prepareReaderEngagement();
        initProgressBar();
        initShareRail();
        initBackToTop();
        initFAQ();
        initReveal();
        initRating();
        initShareButtons();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEditorialLayer);
    } else {
        initEditorialLayer();
    }

    function isReaderArticle() {
        return !!document.querySelector('.nexus-article-reader-header');
    }

    /* ----------------------------------------------------------
       Article System engagement layer
       ---------------------------------------------------------- */
    function prepareReaderEngagement() {
        if (!isReaderArticle()) return;

        injectReaderEngagementStyles();
        ensureReaderShareRail();
        ensureReaderLikeRoot();
    }

    function injectReaderEngagementStyles() {
        if (document.getElementById('nexus-reader-engagement-style')) return;

        var style = document.createElement('style');
        style.id = 'nexus-reader-engagement-style';
        style.textContent = [
            '@media (min-width:1280px){',
            '.nexus-article-reader-header~.nexus-share-rail{display:flex!important;}',
            '.nexus-article-reader-header~.nexus-share-rail .nexus-share-rail__btn{position:relative;}',
            '.nexus-article-reader-header~.nexus-share-rail .nexus-share-rail__btn::after{content:attr(aria-label);position:absolute;left:calc(100% + 10px);top:50%;transform:translateY(-50%) translateX(-4px);padding:6px 8px;border:1px solid var(--nx-border,#232A30);border-radius:8px;background:#0b0f12;color:var(--nx-text,#f2ebdd);font-size:11px;line-height:1;white-space:nowrap;opacity:0;pointer-events:none;transition:opacity .16s ease,transform .16s ease;}',
            '.nexus-article-reader-header~.nexus-share-rail .nexus-share-rail__btn:hover::after,.nexus-article-reader-header~.nexus-share-rail .nexus-share-rail__btn:focus-visible::after{opacity:1;transform:translateY(-50%) translateX(0);}',
            '}',
            '.nexus-article-reader-header~.nexus-single-container .nexus-rating.nexus-reader-like{display:block!important;width:min(760px,calc(100% - 40px));margin:clamp(3rem,7vw,5.5rem) auto 0;padding:clamp(1.5rem,3vw,2rem) 0;border-top:1px solid var(--nx-border,#232A30);background:transparent;box-shadow:none;}',
            '.nexus-reader-like__eyebrow{display:block;margin-bottom:.65rem;color:var(--accent-hover,#e08a3c);font-family:var(--font-mono,ui-monospace,monospace);font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;}',
            '.nexus-reader-like__title{margin:0;color:var(--nx-text,#f2ebdd);font-family:var(--font-display,system-ui,sans-serif);font-size:clamp(1.45rem,2.5vw,2rem);line-height:1.15;letter-spacing:-.025em;}',
            '.nexus-reader-like__sub{margin:.6rem 0 0;color:var(--nx-text-muted,#b9b1a3);font-size:.92rem;line-height:1.55;}',
            '.nexus-reader-like__actions{display:flex;align-items:center;gap:.75rem;margin-top:1.15rem;flex-wrap:wrap;}',
            '.nexus-reader-like__button,.nexus-reader-like__share{display:inline-flex;align-items:center;justify-content:center;gap:.55rem;min-height:42px;padding:0 14px;border:1px solid var(--nx-border,#232A30);border-radius:999px;background:transparent;color:var(--nx-text-muted,#b9b1a3);font:inherit;font-size:.88rem;font-weight:650;cursor:pointer;transition:border-color .18s ease,background-color .18s ease,color .18s ease,transform .18s ease;}',
            '.nexus-reader-like__button:hover,.nexus-reader-like__button:focus-visible,.nexus-reader-like__share:hover,.nexus-reader-like__share:focus-visible{border-color:color-mix(in srgb,var(--accent-hover,#e08a3c) 45%,var(--nx-border,#232A30));color:var(--nx-text,#f2ebdd);transform:translateY(-1px);outline:none;}',
            '.nexus-reader-like__button svg,.nexus-reader-like__share svg{width:17px;height:17px;flex:0 0 auto;}',
            '.nexus-reader-like__button.is-selected{border-color:color-mix(in srgb,var(--accent-hover,#e08a3c) 52%,var(--nx-border,#232A30));background:color-mix(in srgb,var(--accent-hover,#e08a3c) 12%,transparent);color:var(--accent-hover,#e08a3c);}',
            '.nexus-reader-like__button.is-selected svg{fill:currentColor;}',
            '.nexus-reader-like__button:disabled{cursor:default;transform:none;opacity:1;}',
            '.nexus-reader-like__status{min-height:1.35em;margin:.75rem 0 0;color:var(--nx-text-dim,#8a8478);font-size:.8rem;}',
            '.nexus-reader-like__status.is-error{color:#d58a7b;}',
            '.nexus-reader-like__share{display:none;}',
            '@media (max-width:1279px){.nexus-reader-like__share{display:inline-flex;}}',
            '@media (max-width:640px){.nexus-article-reader-header~.nexus-single-container .nexus-rating.nexus-reader-like{width:min(100% - 28px,760px);}}',
            '@media (prefers-reduced-motion:reduce){.nexus-article-reader-header~.nexus-share-rail .nexus-share-rail__btn::after,.nexus-reader-like__button,.nexus-reader-like__share{transition:none!important;}}'
        ].join('');
        document.head.appendChild(style);
    }

    function ensureReaderShareRail() {
        var header = document.querySelector('.nexus-article-reader-header');
        if (!header) return;

        var rail = document.querySelector('.nexus-share-rail');
        if (!rail) {
            rail = document.createElement('aside');
            rail.className = 'nexus-share-rail';
            rail.setAttribute('aria-label', 'Artikel teilen');
            rail.innerHTML = [
                '<span class="nexus-share-rail__label">Teilen</span>',
                '<button class="nexus-share-rail__btn" type="button" data-nexus-share="linkedin" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg></button>',
                '<button class="nexus-share-rail__btn" type="button" data-nexus-share="whatsapp" aria-label="WhatsApp"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8A8.5 8.5 0 0 1 12.5 20a8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8A8.5 8.5 0 0 1 8.7 3.9a8.38 8.38 0 0 1 3.8-.9h.5A8.48 8.48 0 0 1 21 11v.5Z"/><path d="M9.2 8.4c.6 2.5 2 3.9 4.5 4.6"/></svg></button>',
                '<button class="nexus-share-rail__btn" type="button" data-nexus-share="email" aria-label="Per E-Mail teilen"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></button>',
                '<button class="nexus-share-rail__btn" type="button" data-nexus-share="copy" aria-label="Link kopieren"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg></button>'
            ].join('');
            header.insertAdjacentElement('afterend', rail);
            return;
        }

        var xButton = rail.querySelector('[data-nexus-share="x"], [data-nexus-share="twitter"]');
        if (xButton) {
            xButton.setAttribute('data-nexus-share', 'whatsapp');
            xButton.setAttribute('aria-label', 'WhatsApp');
            xButton.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8A8.5 8.5 0 0 1 12.5 20a8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8A8.5 8.5 0 0 1 8.7 3.9a8.38 8.38 0 0 1 3.8-.9h.5A8.48 8.48 0 0 1 21 11v.5Z"/><path d="M9.2 8.4c.6 2.5 2 3.9 4.5 4.6"/></svg>';
        }
    }

    function ensureReaderLikeRoot() {
        if (document.querySelector('.nexus-rating')) return;

        var author = document.querySelector('.nexus-author-bio');
        var main = document.querySelector('.nexus-single-container');
        if (!main) return;

        var root = document.createElement('section');
        root.className = 'nexus-rating nexus-reader-like';
        root.setAttribute('data-track-section', 'article_rating');
        root.setAttribute('aria-labelledby', 'nexus-reader-like-title');

        if (author && author.parentNode === main) {
            main.insertBefore(root, author);
        } else {
            main.appendChild(root);
        }
    }

    /* ----------------------------------------------------------
       Reading progress bar
       ---------------------------------------------------------- */
    function initProgressBar() {
        var bar = document.querySelector('.nexus-reading-progress');
        var article = document.querySelector('.nexus-article-content') ||
                      document.querySelector('[data-provider-decision]') ||
                      document.querySelector('[data-checkfox-cockpit]');
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

        var hero = document.querySelector('.nexus-article-hero') ||
                   document.querySelector('[data-provider-decision-hero]') ||
                   document.querySelector('.hu-checkfox__hero');
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
            var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
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

            var open = item.classList.contains('is-open');
            var scope = item.closest('.nexus-article-content') || document;
            scope.querySelectorAll('.faq-item.is-open').forEach(function (other) {
                if (other === item) return;
                other.classList.remove('is-open');
                var q = other.querySelector('.faq-question');
                if (q) q.setAttribute('aria-expanded', 'false');
            });

            item.classList.toggle('is-open', !open);
            question.setAttribute('aria-expanded', open ? 'false' : 'true');
        });

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
       Rating widget / reader like
       ---------------------------------------------------------- */
    function initRating() {
        var root = document.querySelector('.nexus-rating');
        if (!root) return;

        if (isReaderArticle()) {
            initReaderLike(root);
            return;
        }

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

                sendRating(state.rating, '', errorEl);
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
                    sendRating(state.rating, text, errorEl);
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
    }

    function initReaderLike(root) {
        root.classList.add('nexus-reader-like');
        root.classList.remove('nexus-reveal');
        root.innerHTML = [
            '<span class="nexus-reader-like__eyebrow">Leserfeedback</span>',
            '<h2 id="nexus-reader-like-title" class="nexus-reader-like__title">Hat dir dieser Beitrag geholfen?</h2>',
            '<p class="nexus-reader-like__sub">Ein Klick genügt — kein Login.</p>',
            '<div class="nexus-reader-like__actions">',
            '<button class="nexus-reader-like__button" type="button" data-reader-like aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z"/></svg><span>Gefällt mir</span></button>',
            '<button class="nexus-reader-like__share" type="button" data-nexus-share="native"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 10.5 6.8-4M8.6 13.5l6.8 4"/></svg><span>Teilen</span></button>',
            '</div>',
            '<p class="nexus-reader-like__status" role="status" aria-live="polite"></p>'
        ].join('');

        var button = root.querySelector('[data-reader-like]');
        var status = root.querySelector('.nexus-reader-like__status');
        var storageKey = 'nexus_reader_like_' + String(config.postId || window.location.pathname);
        var alreadyLiked = safeStorageGet(storageKey) === '1';
        var pending = false;

        if (alreadyLiked) {
            markReaderLikeSelected(button, status, false);
        }

        button.addEventListener('click', function () {
            if (pending || button.disabled) return;
            pending = true;
            status.textContent = '';
            status.classList.remove('is-error');

            sendRating('yes', '', status).then(function (ok) {
                pending = false;
                if (!ok) return;
                safeStorageSet(storageKey, '1');
                markReaderLikeSelected(button, status, true);
                pushDataLayer({
                    event: 'post_like',
                    rating: 'yes',
                    post_id: config.postId || null
                });
            });
        });
    }

    function markReaderLikeSelected(button, status, announce) {
        if (!button) return;
        button.classList.add('is-selected');
        button.setAttribute('aria-pressed', 'true');
        button.disabled = true;
        if (status) status.textContent = announce ? 'Danke — ist gespeichert.' : 'Du hast diesen Beitrag bereits markiert.';
    }

    function sendRating(rating, text, errorEl) {
        if (!config.restEndpoint || !config.postId) return Promise.resolve(false);

        var body = {
            postId: config.postId,
            rating: rating,
            feedback: text || '',
            nonce: config.nonce || ''
        };

        return fetch(config.restEndpoint, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': config.nonce || ''
            },
            body: JSON.stringify(body)
        }).then(function (response) {
            if (!response.ok) throw new Error('rating_failed');
            return true;
        }).catch(function () {
            if (errorEl) {
                errorEl.textContent = config.errorMsg || 'Bitte erneut versuchen.';
                errorEl.classList.add('is-error');
                errorEl.classList.add('is-shown');
            }
            return false;
        });
    }

    function safeStorageGet(key) {
        try {
            return window.localStorage ? window.localStorage.getItem(key) : null;
        } catch (e) {
            return null;
        }
    }

    function safeStorageSet(key, value) {
        try {
            if (window.localStorage) window.localStorage.setItem(key, value);
        } catch (e) {}
    }

    /* ----------------------------------------------------------
       Vertical share rail buttons (+ copy/native share)
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
                    case 'whatsapp':
                        target = 'https://wa.me/?text=' + encodeURIComponent(shareTitle + '\n' + shareUrl);
                        break;
                    case 'twitter':
                    case 'x':
                        target = 'https://twitter.com/intent/tweet?url=' + encodedUrl + '&text=' + encodedTitle;
                        break;
                    case 'email':
                        pushDataLayer({ event: 'post_share', method: type, post_id: config.postId || null });
                        window.location.href = 'mailto:?subject=' + encodedTitle + '&body=' + encodedUrl;
                        return;
                    case 'copy':
                        copyToClipboard(shareUrl, btn);
                        pushDataLayer({ event: 'post_share', method: type, post_id: config.postId || null });
                        return;
                    case 'native':
                        if (navigator.share) {
                            navigator.share({ title: shareTitle, url: shareUrl }).then(function () {
                                pushDataLayer({ event: 'post_share', method: 'native', post_id: config.postId || null });
                            }).catch(function () {});
                        } else {
                            copyToClipboard(shareUrl, btn);
                            pushDataLayer({ event: 'post_share', method: 'copy_fallback', post_id: config.postId || null });
                        }
                        return;
                }

                if (target) {
                    window.open(target, '_blank', 'noopener,width=600,height=520');
                    pushDataLayer({ event: 'post_share', method: type, post_id: config.postId || null });
                }
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
