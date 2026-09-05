(function () {
    'use strict';

    if (typeof document === 'undefined') return;

    function initReaderShareFix() {
        var header = document.querySelector('.nexus-article-reader-header');
        var rail = document.querySelector('.nexus-share-rail');
        if (!header || !rail) return;

        var config = window.NexusSingleEditorial || {};
        var shareUrl = config.shareUrl || window.location.href;
        var shareTitle = config.shareTitle || document.title;
        var linkedinUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(shareUrl);
        var whatsappUrl = 'https://wa.me/?text=' + encodeURIComponent(shareTitle + '\n' + shareUrl);
        var emailUrl = 'mailto:?subject=' + encodeURIComponent(shareTitle) + '&body=' + encodeURIComponent(shareUrl);

        rail.innerHTML = [
            '<span class="nexus-share-rail__label">Teilen</span>',
            '<a class="nexus-share-rail__btn" href="' + linkedinUrl + '" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" data-reader-share-link="linkedin"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg></a>',
            '<a class="nexus-share-rail__btn" href="' + whatsappUrl + '" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" data-reader-share-link="whatsapp"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8A8.5 8.5 0 0 1 12.5 20a8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8A8.5 8.5 0 0 1 8.7 3.9a8.38 8.38 0 0 1 3.8-.9h.5A8.48 8.48 0 0 1 21 11v.5Z"/><path d="M9.2 8.4c.6 2.5 2 3.9 4.5 4.6"/></svg></a>',
            '<a class="nexus-share-rail__btn" href="' + emailUrl + '" aria-label="Per E-Mail teilen" data-reader-share-link="email"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></a>',
            '<button class="nexus-share-rail__btn" type="button" data-reader-share-copy aria-label="Link kopieren"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg></button>'
        ].join('');

        rail.querySelectorAll('[data-reader-share-link]').forEach(function (link) {
            link.addEventListener('click', function () {
                pushShareEvent(link.getAttribute('data-reader-share-link'));
            });
        });

        var copyButton = rail.querySelector('[data-reader-share-copy]');
        if (copyButton) {
            copyButton.addEventListener('click', function () {
                copyLink(shareUrl, copyButton);
                pushShareEvent('copy');
            });
        }
    }

    function copyLink(text, button) {
        var done = function () {
            var original = button.innerHTML;
            button.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';
            button.setAttribute('aria-label', 'Link kopiert');
            setTimeout(function () {
                button.innerHTML = original;
                button.setAttribute('aria-label', 'Link kopieren');
            }, 1800);
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(function () {
                fallbackCopy(text, done);
            });
            return;
        }

        fallbackCopy(text, done);
    }

    function fallbackCopy(text, done) {
        var input = document.createElement('textarea');
        input.value = text;
        input.setAttribute('readonly', '');
        input.style.position = 'fixed';
        input.style.opacity = '0';
        document.body.appendChild(input);
        input.select();
        try {
            if (document.execCommand('copy')) done();
        } catch (e) {}
        document.body.removeChild(input);
    }

    function pushShareEvent(method) {
        try {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'post_share',
                method: method,
                post_id: (window.NexusSingleEditorial || {}).postId || null
            });
        } catch (e) {}
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReaderShareFix);
    } else {
        initReaderShareFix();
    }
})();
