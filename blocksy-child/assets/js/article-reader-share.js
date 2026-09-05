/* ============================================================
   Article System — robust share actions
   Delegated capture handling keeps dynamically inserted reader actions
   reliable across browsers and avoids duplicate legacy handlers.
   ============================================================ */
(function () {
    'use strict';

    if (typeof document === 'undefined') return;

    function isReaderArticle() {
        return !!document.querySelector('.nexus-article-reader-header');
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
        var link = document.createElement('a');
        link.href = url;
        link.target = '_blank';
        link.rel = 'noopener noreferrer';
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        link.remove();
    }

    function legacyCopy(text) {
        var input = document.createElement('textarea');
        input.value = text;
        input.setAttribute('readonly', '');
        input.style.position = 'fixed';
        input.style.opacity = '0';
        document.body.appendChild(input);
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
        button.setAttribute('aria-label', ok ? 'Link kopiert' : 'Link konnte nicht kopiert werden');
        button.classList.toggle('is-copied', ok);

        window.setTimeout(function () {
            if (originalLabel) button.setAttribute('aria-label', originalLabel);
            button.classList.remove('is-copied');
        }, 1800);
    }

    function copyUrl(text, button) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
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

    document.addEventListener('click', function (event) {
        if (!isReaderArticle()) return;

        var button = event.target.closest('[data-nexus-share]');
        if (!button) return;

        var type = button.getAttribute('data-nexus-share');
        var share = getShareData();
        var encodedUrl = encodeURIComponent(share.url);
        var encodedTitle = encodeURIComponent(share.title);

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

            case 'email':
                trackShare(type);
                window.location.href = 'mailto:?subject=' + encodedTitle + '&body=' + encodedUrl;
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
