/** One bounded, illustrative homepage journey; the final state lives in PHP. */
(function () {
    'use strict';

    var journey = document.querySelector('[data-home-journey]');
    if (!journey || journey.hasAttribute('data-journey-ready') ||
        typeof window.matchMedia !== 'function' ||
        typeof window.IntersectionObserver !== 'function') {
        return;
    }

    var replay = journey.querySelector('[data-journey-replay]');
    var progress = journey.querySelector('[data-journey-progress]');
    var artwork = journey.querySelector('[data-journey-artwork]');
    if (!replay || !progress || !artwork) return;

    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var artworkReady = artwork.complete && artwork.naturalWidth > 0;
    var hasPlayed = false;
    var isPlaying = false;
    var isInView = false;
    var settleTimer = 0;

    function settle() {
        window.clearTimeout(settleTimer);
        settleTimer = 0;
        isPlaying = false;
        journey.classList.remove('is-playing');
    }

    function play() {
        if (isPlaying || !isInView || !artworkReady || document.hidden || reducedMotion.matches) return;
        hasPlayed = true;
        isPlaying = true;
        journey.classList.add('is-playing');
        // Watchdog also settles if CSS is unavailable or animationend is lost.
        settleTimer = window.setTimeout(settle, 5000);
    }

    function syncPreference() {
        settle();
        replay.hidden = reducedMotion.matches || !artworkReady;
        if (!hasPlayed) play();
    }

    try {
        var observer = new window.IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                isInView = entry.isIntersecting && entry.intersectionRatio >= 0.2;
                if (!isInView) settle();
                else if (!hasPlayed) play();
            });
        }, { threshold: [0, 0.2] });

        observer.observe(journey);
        progress.addEventListener('animationend', function (event) {
            if (event.animationName === 'hu-journey-progress') settle();
        });
        replay.addEventListener('click', play);
        artwork.addEventListener('load', function () {
            artworkReady = artwork.naturalWidth > 0;
            if (artworkReady) journey.classList.add('has-artwork');
            replay.hidden = reducedMotion.matches || !artworkReady;
            if (!hasPlayed) play();
        });
        artwork.addEventListener('error', function () {
            artworkReady = false;
            settle();
            journey.classList.remove('has-artwork');
            replay.hidden = true;
        });
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) settle();
            else if (!hasPlayed) play();
        });
        window.addEventListener('pagehide', settle);
        if (typeof reducedMotion.addEventListener === 'function') {
            reducedMotion.addEventListener('change', syncPreference);
        } else if (typeof reducedMotion.addListener === 'function') {
            reducedMotion.addListener(syncPreference);
        }
        journey.setAttribute('data-journey-ready', '');
        if (artworkReady) journey.classList.add('has-artwork');
        replay.hidden = reducedMotion.matches || !artworkReady;
    } catch (error) {
        settle();
        replay.hidden = true;
        if (observer) observer.disconnect();
    }
}());
