/**
 * Startseite: Die Übergabe-Tafel hakt ihre Punkte einmal ab, sobald sie in den
 * Blick kommt, danach setzt der Stempel. Ohne Skript, bei reduzierter Bewegung
 * und wenn die Tafel beim Laden schon sichtbar ist, steht sie fertig da.
 */
(function () {
    'use strict';

    var plate = document.querySelector('[data-home-uebergabe]');
    if (!plate || typeof window.IntersectionObserver !== 'function' ||
        typeof window.matchMedia !== 'function' ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
        plate.getBoundingClientRect().top < window.innerHeight) {
        return;
    }

    plate.classList.add('is-bereit');
    var observer = new window.IntersectionObserver(function (entries) {
        if (!entries[entries.length - 1].isIntersecting) return;
        plate.classList.add('is-sichtbar');
        observer.disconnect();
    }, { threshold: 0.35 });
    observer.observe(plate);
}());
