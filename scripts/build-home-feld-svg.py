#!/usr/bin/env python3
"""Erzeugt die Hero-Tafel der Startseite: blocksy-child/assets/img/home-feld.svg.

Die Linien sind Stromlinien eines einfachen Potentialfelds: eine Quelle links
ausserhalb der Tafel (Besuche), eine Senke rechts in der Tafel (die Anfrage)
und eine leichte Grundstroemung nach rechts. Linien, die in der Senke enden,
werden zur Anfrage; die uebrigen ziehen vorbei. Das Ergebnis ist
deterministisch und braucht keine Abhaengigkeiten.

    python3 scripts/build-home-feld-svg.py

Die Lage der Senke (SINK_X, SINK_Y) steht als Prozentwert auch in
assets/css/startseite.css (--home-feld-x, --home-feld-y); beide muessen passen.
"""
import math
import os

W, H = 800, 800
SINK_X, SINK_Y = 496.0, 360.0   # 62 % / 45 % der Tafel (--home-feld-x, --home-feld-y)
SINK_STRENGTH = 1100.0
SOURCE = (-300.0, 470.0, 6400.0)
FLOW = 1.2
LINES = 44
STOP_RADIUS = 26.0              # Linien enden ausserhalb des Stempels
STEP = 2.5
EPSILON = 0.45                  # Toleranz der Linienvereinfachung

OUT = os.path.join(os.path.dirname(__file__), '..', 'blocksy-child', 'assets', 'img', 'home-feld.svg')


def velocity(x, y):
    qx, qy, a = SOURCE
    dx, dy = x - qx, y - qy
    r2 = dx * dx + dy * dy
    vx, vy = a / (2 * math.pi) * dx / r2, a / (2 * math.pi) * dy / r2
    dx, dy = x - SINK_X, y - SINK_Y
    r2 = dx * dx + dy * dy
    vx -= SINK_STRENGTH / (2 * math.pi) * dx / r2
    vy -= SINK_STRENGTH / (2 * math.pi) * dy / r2
    return vx + FLOW, vy


def direction(x, y):
    vx, vy = velocity(x, y)
    m = math.hypot(vx, vy) or 1.0
    return vx / m, vy / m


def trace(y0):
    x, y = -30.0, y0
    points = [(x, y)]
    for _ in range(4000):
        k1 = direction(x, y)
        k2 = direction(x + STEP / 2 * k1[0], y + STEP / 2 * k1[1])
        k3 = direction(x + STEP / 2 * k2[0], y + STEP / 2 * k2[1])
        k4 = direction(x + STEP * k3[0], y + STEP * k3[1])
        x += STEP / 6 * (k1[0] + 2 * k2[0] + 2 * k3[0] + k4[0])
        y += STEP / 6 * (k1[1] + 2 * k2[1] + 2 * k3[1] + k4[1])
        points.append((x, y))
        if math.hypot(x - SINK_X, y - SINK_Y) < STOP_RADIUS:
            return points, True
        if x > W + 30 or y < -30 or y > H + 30:
            return points, False
    return points, False


def simplify(points, eps):
    if len(points) < 3:
        return points
    (x1, y1), (x2, y2) = points[0], points[-1]
    length = math.hypot(x2 - x1, y2 - y1) or 1.0
    dmax, index = 0.0, 0
    for i in range(1, len(points) - 1):
        d = abs((y2 - y1) * points[i][0] - (x2 - x1) * points[i][1] + x2 * y1 - y2 * x1) / length
        if d > dmax:
            dmax, index = d, i
    if dmax > eps:
        return simplify(points[:index + 1], eps)[:-1] + simplify(points[index:], eps)
    return [points[0], points[-1]]


def main():
    passing, captured = [], []
    for i in range(LINES):
        y0 = -10 + (H + 20) * (i + 0.5) / LINES
        points, is_captured = trace(y0)
        d = 'M' + ' L'.join('%.0f %.0f' % p for p in simplify(points, EPSILON))
        major = i % 5 == 2
        if is_captured:
            captured.append('<path class="home-feld__linie home-feld__linie--fang" data-home-feld-linie="fang" stroke="url(#home-feld-fang)" d="%s"/>' % d)
        else:
            passing.append('<path class="home-feld__linie%s" data-home-feld-linie="vorbei" stroke="url(#home-feld-vorbei%s)" d="%s"/>' % (' home-feld__linie--haupt' if major else '', '-haupt' if major else '', d))

    svg = '\n'.join([
        '<!-- Erzeugt von scripts/build-home-feld-svg.py. Nicht von Hand bearbeiten. -->',
        '<svg class="home-feld__feld" viewBox="0 0 %d %d" preserveAspectRatio="xMidYMid meet" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">' % (W, H),
        '<defs>',
        '<linearGradient id="home-feld-vorbei" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="%d" y2="0"><stop class="home-feld__ton" offset="0" stop-color="#171412" stop-opacity=".24"/><stop class="home-feld__ton" offset=".62" stop-color="#171412" stop-opacity=".22"/><stop class="home-feld__ton" offset="1" stop-color="#171412" stop-opacity="0"/></linearGradient>' % W,
        '<linearGradient id="home-feld-vorbei-haupt" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="%d" y2="0"><stop class="home-feld__ton" offset="0" stop-color="#171412" stop-opacity=".42"/><stop class="home-feld__ton" offset=".62" stop-color="#171412" stop-opacity=".36"/><stop class="home-feld__ton" offset="1" stop-color="#171412" stop-opacity="0"/></linearGradient>' % W,
        '<linearGradient id="home-feld-fang" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="%d" y2="0"><stop class="home-feld__ton" offset="0" stop-color="#171412" stop-opacity=".26"/><stop class="home-feld__ton" offset=".55" stop-color="#171412" stop-opacity=".34"/><stop class="home-feld__akzent" offset="1" stop-color="#b8420f" stop-opacity=".9"/></linearGradient>' % SINK_X,
        '<linearGradient id="home-feld-rand-x" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="%d" y2="0"><stop offset="0" stop-color="#fff" stop-opacity="0"/><stop offset=".12" stop-color="#fff"/></linearGradient>' % W,
        '<linearGradient id="home-feld-rand-y" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="0" y2="%d"><stop offset="0" stop-color="#fff" stop-opacity="0"/><stop offset=".1" stop-color="#fff"/><stop offset=".9" stop-color="#fff"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></linearGradient>' % H,
        '<mask id="home-feld-maske-x" maskUnits="userSpaceOnUse" x="0" y="0" width="%d" height="%d"><rect width="%d" height="%d" fill="url(#home-feld-rand-x)"/></mask>' % (W, H, W, H),
        '<mask id="home-feld-maske-y" maskUnits="userSpaceOnUse" x="0" y="0" width="%d" height="%d"><rect width="%d" height="%d" fill="url(#home-feld-rand-y)"/></mask>' % (W, H, W, H),
        '</defs>',
        '<g mask="url(#home-feld-maske-x)"><g mask="url(#home-feld-maske-y)" fill="none" stroke-width="1">',
        '\n'.join(passing),
        '\n'.join(captured),
        '</g></g>',
        '<g class="home-feld__stempel" data-home-feld-stempel="" fill="none" stroke="#b8420f">',
        '<circle class="home-feld__stempel-welle" cx="%.0f" cy="%.0f" r="22"/>' % (SINK_X, SINK_Y),
        '<circle class="home-feld__stempel-rand" cx="%.0f" cy="%.0f" r="22"/>' % (SINK_X, SINK_Y),
        '<circle class="home-feld__stempel-ring" cx="%.0f" cy="%.0f" r="12"/>' % (SINK_X, SINK_Y),
        '<circle class="home-feld__stempel-kern" cx="%.0f" cy="%.0f" r="4.5" fill="#b8420f" stroke="none"/>' % (SINK_X, SINK_Y),
        '</g>',
        '</svg>',
        '',
    ])
    with open(OUT, 'w', encoding='utf-8') as handle:
        handle.write(svg)
    print('%s: %d Linien, davon %d mit Anfrage, %d Bytes' % (os.path.normpath(OUT), LINES, len(captured), len(svg.encode('utf-8'))))


if __name__ == '__main__':
    main()
