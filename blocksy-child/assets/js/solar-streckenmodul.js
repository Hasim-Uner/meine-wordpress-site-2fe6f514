/* The PHP template owns values and the readable final state. This script only
   adds motion and tab interaction to the Solar request path. */
(function () {
  'use strict';

  var root = document.querySelector('[data-streckenmodul]');
  if (!root) return;

  var counts;
  try { counts = JSON.parse(root.getAttribute('data-counts')); } catch (error) { return; }
  if (!counts || !Array.isArray(counts.a) || !Array.isArray(counts.b)) return;

  var tabs = Array.prototype.slice.call(root.querySelectorAll('[role="tab"]'));
  var panel = root.querySelector('#modul-panel');
  var stage = root.querySelector('[data-module-stage]');
  var replay = root.querySelector('.modul-replay');
  if (tabs.length !== 5 || !panel || !stage || !replay) return;

  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var layouts = {
    wide: { w: 1000, xs: [100, 280, 460, 640, 820], x0: 20, end: 940, a: 110, b: 296, spread: 26, fall: 40 },
    narrow: { w: 520, xs: [40, 135, 230, 325, 420], x0: 8, end: 500, a: 95, b: 330, spread: 30, fall: 46 }
  };
  var fields = Array.prototype.slice.call(root.querySelectorAll('.modul-svg')).map(function (svg) {
    var layout = layouts[svg.getAttribute('data-layout')];
    if (!layout) return null;
    var dots = Array.prototype.slice.call(svg.querySelectorAll('.modul-punkt')).map(function (circle) {
      var way = circle.getAttribute('data-way');
      var index = Number(circle.getAttribute('data-index'));
      var last = Number(circle.getAttribute('data-last'));
      var seed = Math.sin((index + 1) * (way === 'a' ? 12.9898 : 78.233)) * 43758.5453;
      seed -= Math.floor(seed);
      var seed2 = Math.sin((index + 7) * (way === 'a' ? 3.1 : 5.7)) * 9999;
      seed2 -= Math.floor(seed2);
      return { circle: circle, way: way, last: last, dy: (seed - 0.5) * 2 * layout.spread * 0.8,
        dx: (seed2 - 0.5) * layout.w * 0.036, delay: index * (way === 'a' ? 95 : 55) + seed * 80 };
    });
    return { svg: svg, name: svg.getAttribute('data-layout'), layout: layout, dots: dots };
  }).filter(Boolean);
  if (fields.length !== 2) return;

  var countRows = {
    a: Array.prototype.slice.call(root.querySelectorAll('.modul-labels-row--a .modul-count')),
    b: Array.prototype.slice.call(root.querySelectorAll('.modul-labels-row--b .modul-count'))
  };
  var selected = 0;
  var manual = false;
  var played = false;
  var frame = 0;
  var start = 0;

  function select(index, byReader) {
    selected = index;
    if (byReader) manual = true;
    tabs.forEach(function (tab, i) {
      tab.setAttribute('aria-selected', i === index ? 'true' : 'false');
      tab.tabIndex = i === index ? 0 : -1;
    });
    var tab = tabs[index];
    panel.setAttribute('aria-labelledby', tab.id);
    panel.querySelector('.modul-panel-name').textContent = '0' + (index + 1) + ' · ' + tab.dataset.name;
    ['built', 'a', 'b'].forEach(function (key) {
      panel.querySelector('[data-module-copy="' + key + '"]').textContent = tab.dataset[key];
    });
    fields.forEach(function (field) {
      Array.prototype.forEach.call(field.svg.querySelectorAll('[data-station]'), function (line, i) {
        line.classList.toggle('is-selected', i === index);
      });
    });
  }

  tabs.forEach(function (tab, i) {
    tab.addEventListener('click', function () { select(i, true); });
    tab.addEventListener('keydown', function (event) {
      var step = event.key === 'ArrowRight' ? 1 : event.key === 'ArrowLeft' ? -1 : 0;
      if (!step) return;
      event.preventDefault();
      var next = (i + step + tabs.length) % tabs.length;
      select(next, true);
      tabs[next].focus();
    });
  });
  select(0, false);
  root.classList.add('js-ready');

  function draw(time) {
    var leads = { wide: [0, 0], narrow: [0, 0] };
    fields.forEach(function (field) {
      var l = field.layout;
      var speed = l.w * 0.00024;
      field.dots.forEach(function (dot) {
        var elapsed = time - dot.delay;
        var startX = l.x0 - 10 + dot.dx;
        var target = l.xs[dot.last] + dot.dx * 0.4;
        var x = startX + Math.max(0, elapsed) * speed;
        var fallTime = 0;
        var arrived = x >= target;
        if (arrived) {
          x = target;
          fallTime = (elapsed - (target - startX) / speed) / 520;
        }
        var y = l[dot.way] + dot.dy;
        var opacity = elapsed < 0 ? 0 : 1;
        var scale = 1;
        if (dot.last < 4 && arrived) {
          var k = Math.max(0, Math.min(1, fallTime));
          var ease = 1 - Math.pow(1 - k, 3);
          y += ease * l.fall * (0.6 + Math.abs(dot.dy) / l.spread * 0.4);
          opacity = 1 - 0.72 * ease;
          scale = 1 - 0.25 * ease;
        }
        if (dot.last === 4 && arrived) {
          x += Math.max(0, Math.min(1, fallTime)) * (l.end - target) * 0.55;
        }
        dot.circle.setAttribute('cx', '0');
        dot.circle.setAttribute('cy', '0');
        dot.circle.setAttribute('transform', 'translate(' + x.toFixed(1) + ' ' + y.toFixed(1) + ') scale(' + scale.toFixed(2) + ')');
        dot.circle.setAttribute('opacity', opacity.toFixed(2));
        if (elapsed > 0) {
          var wayIndex = dot.way === 'a' ? 0 : 1;
          leads[field.name][wayIndex] = Math.max(leads[field.name][wayIndex], x / l.w);
        }
      });
    });
    var currentName = window.matchMedia && window.matchMedia('(max-width: 599px)').matches ? 'narrow' : 'wide';
    var current = layouts[currentName];
    var lead = leads[currentName];
    var front = Math.max(lead[0], lead[1]) * current.w;
    current.xs.forEach(function (x, i) {
      if (lead[0] * current.w >= x - 2) countRows.a[i].classList.add('reached');
      if (lead[1] * current.w >= x - 2) countRows.b[i].classList.add('reached');
      if (front >= x - 2 && !manual && i !== selected) select(i, false);
    });
  }

  function finish() {
    draw(100000);
    countRows.a.concat(countRows.b).forEach(function (label) { label.classList.add('reached'); });
    root.classList.add('is-finished');
    root.classList.remove('is-animating');
  }
  function tick(timestamp) {
    if (!start) start = timestamp;
    var elapsed = timestamp - start;
    draw(elapsed);
    if (elapsed >= 3200) root.classList.add('is-finished');
    if (elapsed < 7200) frame = window.requestAnimationFrame(tick);
    else finish();
  }
  function play() {
    window.cancelAnimationFrame(frame);
    if (reduced) { finish(); return; }
    start = 0;
    manual = false;
    select(0, false);
    countRows.a.concat(countRows.b).forEach(function (label) { label.classList.remove('reached'); });
    root.classList.remove('is-finished');
    root.classList.add('is-animating');
    draw(0);
    frame = window.requestAnimationFrame(tick);
  }
  replay.addEventListener('click', play);
  if ('IntersectionObserver' in window && !reduced) {
    var observer = new IntersectionObserver(function (entries) {
      if (entries[0].isIntersecting && !played) {
        played = true;
        play();
        observer.disconnect();
      }
    }, { threshold: 0.45 });
    observer.observe(root);
  }
})();
