/**
 * Startseite: Funken auf den Stromlinien der Hero-Tafel.
 *
 * Die Tafel steht ohne Skript vollständig im Markup. Das Skript legt nur
 * bewegte Punkte darüber: Besuche fließen von links ein, ein Teil endet im
 * Stempel und zeigt dort seine Quelle. Es läuft nur, solange die Tafel
 * sichtbar ist, die Seite im Vordergrund liegt, niemand angehalten hat und
 * keine reduzierte Bewegung gewünscht ist.
 */
(function () {
    'use strict';

    var figure = document.querySelector('[data-home-feld]');
    if (!figure || figure.hasAttribute('data-home-feld-bereit') ||
        typeof window.matchMedia !== 'function' ||
        typeof window.IntersectionObserver !== 'function' ||
        typeof window.requestAnimationFrame !== 'function') {
        return;
    }

    var canvas = figure.querySelector('canvas');
    var ctx = canvas && canvas.getContext ? canvas.getContext('2d') : null;
    var toggle = figure.querySelector('[data-home-feld-schalter]');
    var toggleText = figure.querySelector('[data-home-feld-schalter-text]');
    var sourceLabel = figure.querySelector('[data-home-feld-quelle]');
    var stamp = figure.querySelector('[data-home-feld-stempel]');
    var svg = figure.querySelector('svg');
    if (!ctx || !toggle || !toggleText || !sourceLabel || !stamp || !svg || !svg.viewBox || !svg.viewBox.baseVal) return;

    var VIEW_W = svg.viewBox.baseVal.width || 800;
    var TRAIL = 10;          // Segmente pro Funke
    var GAP = 4;             // Abstand der Segmente in Tafeleinheiten
    var TARGET = 16;         // gleichzeitige Funken
    var CATCH_SHARE = 0.36;  // Anteil der Funken auf Linien mit Anfrage
    var LABEL_PAUSE = 1900;  // ms, bis die Quelle wieder wechseln darf

    var sources = (figure.getAttribute('data-home-feld-quellen') || '').split('|').filter(Boolean);
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var caught = [];
    var passing = [];
    var sparks = [];
    var point = { x: 0, y: 0 };
    var tail = { x: 0, y: 0 };
    var ink = '#171412';
    var copper = '#b8420f';
    var scale = 1;
    var dpr = 1;
    var inView = false;
    var paused = false;
    var raf = 0;
    var last = 0;
    var spawnIn = 0;
    var lastLabel = 0;
    var sourceIndex = Math.max(0, sources.indexOf(sourceLabel.textContent.trim()));

    function parseLine(path) {
        var numbers = (path.getAttribute('d') || '').match(/-?\d+(?:\.\d+)?/g);
        if (!numbers || numbers.length < 4) return null;
        var count = Math.floor(numbers.length / 2);
        var xs = new Float32Array(count);
        var ys = new Float32Array(count);
        var lengths = new Float32Array(count);
        for (var i = 0; i < count; i++) {
            xs[i] = +numbers[i * 2];
            ys[i] = +numbers[i * 2 + 1];
            lengths[i] = i ? lengths[i - 1] + Math.hypot(xs[i] - xs[i - 1], ys[i] - ys[i - 1]) : 0;
        }
        return { xs: xs, ys: ys, lengths: lengths, length: lengths[count - 1] };
    }

    function pointAt(line, s, out) {
        var lengths = line.lengths;
        var lo = 0;
        var hi = lengths.length - 1;
        if (s <= 0) { out.x = line.xs[0]; out.y = line.ys[0]; return; }
        if (s >= line.length) { out.x = line.xs[hi]; out.y = line.ys[hi]; return; }
        while (hi - lo > 1) {
            var mid = (lo + hi) >> 1;
            if (lengths[mid] <= s) lo = mid; else hi = mid;
        }
        var t = (s - lengths[lo]) / ((lengths[hi] - lengths[lo]) || 1);
        out.x = line.xs[lo] + (line.xs[hi] - line.xs[lo]) * t;
        out.y = line.ys[lo] + (line.ys[hi] - line.ys[lo]) * t;
    }

    function smooth(edge0, edge1, value) {
        var t = Math.min(1, Math.max(0, (value - edge0) / (edge1 - edge0)));
        return t * t * (3 - 2 * t);
    }

    function spawn(anywhere) {
        var catches = Math.random() < CATCH_SHARE;
        var pool = catches && caught.length ? caught : passing;
        if (!pool.length) return;
        var line = pool[Math.floor(Math.random() * pool.length)];
        sparks.push({
            line: line,
            catches: pool === caught,
            s: anywhere ? Math.random() * line.length * 0.85 : 0,
            speed: 88 + Math.random() * 48
        });
    }

    function pulse() {
        if (typeof stamp.animate !== 'function') return;
        var wave = stamp.querySelector('.home-feld__stempel-welle');
        var ring = stamp.querySelector('.home-feld__stempel-ring');
        if (wave) {
            wave.animate([
                { opacity: 0.55, transform: 'scale(1)' },
                { opacity: 0, transform: 'scale(2.6)' }
            ], { duration: 900, easing: 'cubic-bezier(.22, 1, .36, 1)' });
        }
        if (ring) {
            ring.animate([
                { transform: 'scale(1.3)' },
                { transform: 'scale(1)' }
            ], { duration: 320, easing: 'cubic-bezier(.22, 1, .36, 1)' });
        }
    }

    function arrive(now) {
        pulse();
        if (sources.length < 2 || now - lastLabel < LABEL_PAUSE) return;
        lastLabel = now;
        sourceIndex = (sourceIndex + 1 + Math.floor(Math.random() * (sources.length - 1))) % sources.length;
        sourceLabel.textContent = sources[sourceIndex];
        if (typeof sourceLabel.animate === 'function') {
            sourceLabel.animate([
                { opacity: 0, transform: 'translateY(0.25em)' },
                { opacity: 1, transform: 'none' }
            ], { duration: 240, easing: 'cubic-bezier(.22, 1, .36, 1)' });
        }
    }

    function drawSpark(spark) {
        var line = spark.line;
        var progress = spark.s / line.length;
        var fade = Math.min(1, spark.s / 90) * (spark.catches ? 1 : Math.min(1, (line.length - spark.s) / 140));
        var warm = spark.catches ? smooth(0.25, 0.7, progress) : 0;

        ctx.lineWidth = (spark.catches ? 1.6 : 1.2) * dpr;
        pointAt(line, spark.s, point);
        for (var k = 0; k < TRAIL; k++) {
            var back = spark.s - (k + 1) * GAP;
            if (back < 0) break;
            pointAt(line, back, tail);
            var alpha = (1 - k / TRAIL) * fade;
            ctx.beginPath();
            ctx.moveTo(point.x * scale, point.y * scale);
            ctx.lineTo(tail.x * scale, tail.y * scale);
            if (warm < 1) {
                ctx.globalAlpha = alpha * 0.62 * (1 - warm);
                ctx.strokeStyle = ink;
                ctx.stroke();
            }
            if (warm > 0) {
                ctx.globalAlpha = alpha * warm;
                ctx.strokeStyle = copper;
                ctx.stroke();
            }
            point.x = tail.x;
            point.y = tail.y;
        }
    }

    function step(dt, now) {
        spawnIn -= dt;
        if (spawnIn <= 0 && sparks.length < TARGET) {
            spawn(false);
            spawnIn = 0.32 + Math.random() * 0.4;
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.lineCap = 'round';
        for (var i = sparks.length - 1; i >= 0; i--) {
            var spark = sparks[i];
            spark.s += spark.speed * dt;
            if (spark.s >= spark.line.length) {
                sparks.splice(i, 1);
                if (spark.catches) arrive(now);
                continue;
            }
            drawSpark(spark);
        }
        ctx.globalAlpha = 1;
    }

    function running() {
        return inView && !paused && !document.hidden && !reducedMotion.matches;
    }

    function frame(now) {
        raf = 0;
        if (!running()) return;
        var dt = Math.min(0.05, Math.max(0, (now - last) / 1000));
        last = now;
        step(dt, now);
        raf = window.requestAnimationFrame(frame);
    }

    function start() {
        if (raf || !running()) return;
        last = window.performance && typeof window.performance.now === 'function' ? window.performance.now() : 0;
        raf = window.requestAnimationFrame(frame);
    }

    function stop() {
        if (raf) window.cancelAnimationFrame(raf);
        raf = 0;
    }

    function resize() {
        var width = canvas.clientWidth;
        var height = canvas.clientHeight;
        if (!width || !height) return;
        dpr = Math.min(2, window.devicePixelRatio || 1);
        canvas.width = Math.round(width * dpr);
        canvas.height = Math.round(height * dpr);
        scale = canvas.width / VIEW_W;
    }

    function setPaused(value) {
        paused = value;
        toggle.classList.toggle('is-pausiert', paused);
        toggle.setAttribute('aria-label', paused ? 'Bewegung fortsetzen' : 'Bewegung anhalten');
        toggleText.textContent = paused ? 'Weiter' : 'Anhalten';
        if (paused) stop(); else start();
    }

    function syncMotion() {
        toggle.hidden = reducedMotion.matches;
        if (reducedMotion.matches) {
            stop();
            sparks.length = 0;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        } else {
            start();
        }
    }

    try {
        [].forEach.call(figure.querySelectorAll('[data-home-feld-linie]'), function (path) {
            var line = parseLine(path);
            if (line) (path.getAttribute('data-home-feld-linie') === 'fang' ? caught : passing).push(line);
        });
        if (!caught.length || !passing.length) return;

        var styles = window.getComputedStyle(figure);
        ink = styles.getPropertyValue('--tinte').trim() || ink;
        copper = styles.getPropertyValue('--stempel').trim() || copper;

        resize();
        for (var n = 0; n < Math.round(TARGET * 0.7); n++) spawn(true);

        var observer = new window.IntersectionObserver(function (entries) {
            inView = entries[entries.length - 1].isIntersecting;
            if (inView) start(); else stop();
        });
        observer.observe(canvas);

        if (typeof window.ResizeObserver === 'function') {
            new window.ResizeObserver(resize).observe(canvas);
        } else {
            window.addEventListener('resize', resize, { passive: true });
        }
        toggle.addEventListener('click', function () { setPaused(!paused); });
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) stop(); else start();
        });
        if (typeof reducedMotion.addEventListener === 'function') {
            reducedMotion.addEventListener('change', syncMotion);
        } else if (typeof reducedMotion.addListener === 'function') {
            reducedMotion.addListener(syncMotion);
        }

        figure.setAttribute('data-home-feld-bereit', '');
        syncMotion();
    } catch (error) {
        stop();
        toggle.hidden = true;
    }
}());
