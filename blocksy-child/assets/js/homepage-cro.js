/**
 * Homepage CRO Layer — animations & interactions
 *
 * - IntersectionObserver: reveal on scroll
 * - Counter animation for proof KPIs
 * - Interactive 3-question diagnose self-check
 * - Sticky mobile CTA visibility (after hero leaves viewport)
 */
(function () {
    'use strict';

    var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Reveal-on-scroll ---------- */
    function initReveal() {
        var els = document.querySelectorAll('.cro-reveal');
        if (!els.length) return;

        if (prefersReducedMotion || !('IntersectionObserver' in window)) {
            els.forEach(function (el) { el.classList.add('is-visible'); });
            return;
        }

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        els.forEach(function (el) { io.observe(el); });
    }

    /* ---------- Counter animation ---------- */
    function animateCounter(el) {
        var target = parseFloat(el.getAttribute('data-counter-target'));
        if (isNaN(target)) return;

        var prefix = el.getAttribute('data-counter-prefix') || '';
        var suffix = el.getAttribute('data-counter-suffix') || '';
        var decimals = parseInt(el.getAttribute('data-counter-decimals') || '0', 10);
        var duration = 1400;
        var start = performance.now();

        function format(v) {
            var fixed = decimals > 0 ? v.toFixed(decimals) : Math.round(v).toString();
            // German thousand separator
            var parts = fixed.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return prefix + parts.join(',') + suffix;
        }

        function step(now) {
            var elapsed = now - start;
            var t = Math.min(1, elapsed / duration);
            // easeOutCubic
            var eased = 1 - Math.pow(1 - t, 3);
            el.textContent = format(target * eased);
            if (t < 1) requestAnimationFrame(step);
            else el.textContent = format(target);
        }

        if (prefersReducedMotion) {
            el.textContent = format(target);
            return;
        }
        requestAnimationFrame(step);
    }

    function initCounters() {
        var counters = document.querySelectorAll('[data-counter-target]');
        if (!counters.length) return;

        if (!('IntersectionObserver' in window)) {
            counters.forEach(animateCounter);
            return;
        }

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.4 });

        counters.forEach(function (el) { io.observe(el); });
    }

    /* ---------- Interactive diagnose ---------- */
    function initDiagnose() {
        var quiz = document.querySelector('[data-cro-diagnose]');
        if (!quiz) return;

        var result = quiz.querySelector('[data-cro-diagnose-result]');
        var resultTitle = quiz.querySelector('[data-cro-diagnose-result-title]');
        var resultText = quiz.querySelector('[data-cro-diagnose-result-text]');
        if (!result || !resultTitle || !resultText) return;

        var answers = {};

        quiz.querySelectorAll('.cro-diagnose__answer').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var q = btn.getAttribute('data-question');
                var v = btn.getAttribute('data-value');
                if (!q || !v) return;

                quiz
                    .querySelectorAll('.cro-diagnose__answer[data-question="' + q + '"]')
                    .forEach(function (b) { b.classList.remove('is-selected'); });
                btn.classList.add('is-selected');

                answers[q] = v;
                evaluate();
            });
        });

        function evaluate() {
            var keys = Object.keys(answers);
            if (keys.length < 3) {
                result.classList.remove('is-active');
                resultTitle.textContent = 'Beantworten Sie alle drei Fragen.';
                resultText.textContent = 'Sie erhalten eine erste Einordnung, ob die vollständige Anfrage-System-Analyse sinnvoll ist.';
                return;
            }

            var bad = 0;
            keys.forEach(function (k) { if (answers[k] === 'bad') bad++; });

            result.classList.add('is-active');

            if (bad === 0) {
                resultTitle.textContent = 'Grünes Signal für die Analyse.';
                resultText.textContent = 'Zielgebiet, Projektwert und Entscheidungsweg sind klar genug. Die Anfrage-System-Analyse kann prüfen, ob daraus ein Founding-Partner-Fall wird.';
            } else if (bad === 1) {
                resultTitle.textContent = 'Gelbes Signal: erst sauber einordnen.';
                resultText.textContent = 'Ein wichtiger Punkt ist noch unscharf. Genau dafür ist die Analyse gedacht: Fit, Marktbild und nächsten Schritt belastbar klären.';
            } else {
                resultTitle.textContent = 'Rotes Signal für eine direkte Umsetzung.';
                resultText.textContent = 'Für einen Bau des Systems ist die Grundlage noch zu unklar. Die Analyse kann trotzdem zeigen, ob erst Positionierung, Region oder Angebot geschärft werden muss.';
            }
        }

        evaluate();
    }

    /* ---------- Sticky mobile CTA ---------- */
    function initStickyCta() {
        var cta = document.querySelector('.cro-sticky-cta');
        if (!cta) return;
        var hero = document.querySelector('.cro-hero');
        var finalSection = document.querySelector('.cro-final');
        if (!hero) return;

        if (!('IntersectionObserver' in window)) {
            cta.classList.add('is-visible');
            return;
        }

        var heroIo = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                // Hero leaves viewport → show CTA
                if (entry.intersectionRatio < 0.2) {
                    cta.classList.add('is-visible');
                } else {
                    cta.classList.remove('is-visible');
                }
            });
        }, { threshold: [0, 0.2, 0.5, 1] });

        heroIo.observe(hero);

        // Hide when final CTA section is in view (would be redundant)
        if (finalSection) {
            var finalIo = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        cta.classList.remove('is-visible');
                    }
                });
            }, { threshold: 0.3 });
            finalIo.observe(finalSection);
        }
    }

    function ready(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    ready(function () {
        initReveal();
        initCounters();
        initDiagnose();
        initStickyCta();
    });
})();
