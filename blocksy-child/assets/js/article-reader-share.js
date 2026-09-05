/* ============================================================
   Article System — robust share actions
   The reader shell has its own share behavior so legacy single-post
   handlers cannot break the Article System controls.
   ============================================================ */
(function () {
    'use strict';

    if (typeof document === 'undefined') return;

    function isReaderArticle() {
        return !!document.querySelector('.nexus-article-reader-header');
    }

    function makeReaderRailInteractive() {
        if (!isReaderArticle() || document.getElementById('nexus-reader-share-fix')) return;

        /*
         * Reader posts can hide the legacy author bio. single-editorial.js uses
         * that hidden node as the share rail's end marker, so offsetTop becomes
         * 0 and the rail never receives .is-visible. Keep the reader rail
         * independently interactive instead of coupling sharing to that marker.
         */
        var style = document.createElement('style');
        style.id = 'nexus-reader-share-fix';
        style.textContent = [
            '@media (min-width:1280px){',
            '.nexus-article-reader-header~.nexus-share-rail{display:flex!important;opacity:1!important;pointer-events:auto!important;}',
            '}'
        ].join('');
        document.head.appendChild(style);
    }

    function getShareData() {
        var config = window.NexusSingleEditorial || {};
        var canonical = document.querySelector('link[rel="canonical"]');

        return {
            url: config.shareUrl || (canonical && canonical.href) || window.location.href,
            title: config.shareTitle || document.title
        };
    }

    function openExternal(url) {
        var popup = null;

        try {
            popup = window.open(url, '_blank');
        } catch (e) {
            popup = null;
        }

        if (popup) {
            try {
                popup.opener = null;
            } catch (e) {}
            return;
        }

        /* Popup blockers should not turn a share action into a dead button. */
        window.location.href = url;
    }

    function legacyCopy(text) {
        var input = document.createElement('textarea');
        input.value = text;
        input.setAttribute('readonly', '');
        input.style.position = 'fixed';
        input.style.left = '-9999px';
        input.style.top = '0';
        document.body.appendChild(input);
        input.focus();
        input.select();
        input.setSelectionRange(0, input.value.length);

        var copied = false;
        try {
            copied = document.execCommand('copy');
        } catch (e) {
            copied = false;
        }

        input.remove();
        return copied;
    }

    function indicateCopy(button, ok) {
        if (!button) return;

        var originalLabel = button.getAttribute('aria-label') || '';
        var originalTitle = button.getAttribute('title');
        var message = ok ? 'Link kopiert' : 'Link konnte nicht kopiert werden';

        button.setAttribute('aria-label', message);
        button.setAttribute('title', message);
        button.classList.toggle('is-copied', ok);

        window.setTimeout(function () {
            if (originalLabel) button.setAttribute('aria-label', originalLabel);
            if (originalTitle === null) {
                button.removeAttribute('title');
            } else {
                button.setAttribute('title', originalTitle);
            }
            button.classList.remove('is-copied');
        }, 1800);
    }

    function copyUrl(text, button) {
        if (navigator.clipboard && navigator.clipboard.writeText && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function () {
                indicateCopy(button, true);
            }).catch(function () {
                indicateCopy(button, legacyCopy(text));
            });
            return;
        }

        indicateCopy(button, legacyCopy(text));
    }

    function trackShare(type) {
        try {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: 'post_share',
                method: type,
                post_id: (window.NexusSingleEditorial || {}).postId || null
            });
        } catch (e) {}
    }

    makeReaderRailInteractive();

    document.addEventListener('click', function (event) {
        if (!isReaderArticle()) return;

        var target = event.target;
        var button = target && target.closest ? target.closest('[data-nexus-share]') : null;
        if (!button) return;

        var type = button.getAttribute('data-nexus-share');
        var share = getShareData();
        var encodedUrl = encodeURIComponent(share.url);
        var encodedTitle = encodeURIComponent(share.title);

        /* This reader-specific handler owns the action; suppress legacy listeners. */
        event.preventDefault();
        event.stopImmediatePropagation();

        switch (type) {
            case 'linkedin':
                openExternal('https://www.linkedin.com/sharing/share-offsite/?url=' + encodedUrl);
                trackShare(type);
                break;

            case 'whatsapp':
                openExternal('https://wa.me/?text=' + encodeURIComponent(share.title + '\n' + share.url));
                trackShare(type);
                break;

            case 'twitter':
            case 'x':
                openExternal('https://twitter.com/intent/tweet?url=' + encodedUrl + '&text=' + encodedTitle);
                trackShare(type);
                break;

            case 'email':
                trackShare(type);
                window.location.href = 'mailto:?subject=' + encodedTitle + '&body=' + encodeURIComponent(share.title + '\n\n' + share.url);
                break;

            case 'copy':
                copyUrl(share.url, button);
                trackShare(type);
                break;

            case 'native':
                if (navigator.share) {
                    navigator.share({ title: share.title, url: share.url }).then(function () {
                        trackShare('native');
                    }).catch(function (error) {
                        if (error && error.name === 'AbortError') return;
                        copyUrl(share.url, button);
                        trackShare('copy_fallback');
                    });
                } else {
                    copyUrl(share.url, button);
                    trackShare('copy_fallback');
                }
                break;
        }
    }, true);
})();
