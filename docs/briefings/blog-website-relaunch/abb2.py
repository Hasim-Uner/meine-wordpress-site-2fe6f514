"""Erzeugt Abb. 2 (schematische Vorher/Nachher-Kette) als SVG.
Vier Übergänge (02→03 bis 05→06) je ein Fünftel besser; Ergebnis am Ende rund doppelt."""
import json, sys
labels=['SUCHE','SEITE','BELEG','BUTTON','FORMULAR','POSTFACH','RÜCKRUF','AUFTRAG']
rv=[.80,.75,.70,.60,.60,.95,.50]
better={1,2,3,4}
def werte(r):
    v=[100.0]
    for x in r: v.append(v[-1]*x)
    return v
vor=werte(rv)
nach=werte([x*1.2 if i in better else x for i,x in enumerate(rv)])

def svg(W=900,H=330,top=58,band=190,x0=46,x1=None,dark=False,labels_on=True,rid='a2'):
    x1=x1 or W-120
    step=(x1-x0)/7
    xs=[x0+i*step for i in range(8)]
    mid=top+band/2
    sc=band/100
    def poly(v):
        up=[f"{x:.1f},{mid-h*sc/2:.1f}" for x,h in zip(xs,v)]
        dn=[f"{x:.1f},{mid+h*sc/2:.1f}" for x,h in reversed(list(zip(xs,v)))]
        return ' '.join(up+dn)
    c=dict(tinte='#f4f1ec',grau='#a9a199',matt='#9c948a',stempel='#ef8b4d',haar='#332e28',vorFill='#2b261f',vorOp='1',nachOp='.42') if dark else \
      dict(tinte='#171412',grau='#5b554d',matt='#736b62',stempel='#b8420f',haar='#dedad3',vorFill='#efece6',vorOp='1',nachOp='.22')
    o=[f'<svg class="abb2-svg" viewBox="0 0 {W} {H}" role="img" aria-labelledby="{rid}-t {rid}-d" xmlns="http://www.w3.org/2000/svg">',
       f'<title id="{rid}-t">Vorher und nachher: wie viele Besucher an jedem Übergang weiterkommen</title>',
       f'<desc id="{rid}-d">Schematische Darstellung ohne Messwerte. Zwei Bänder von der Suche bis zum Auftrag. An vier Übergängen, von der Seite bis zum Postfach, kommt nachher jeweils ein Fünftel mehr weiter. Am Ende der Kette sind es dadurch rund doppelt so viele Aufträge.</desc>']
    for x in xs:
        o.append(f'<line x1="{x:.1f}" y1="{top-8}" x2="{x:.1f}" y2="{top+band+8}" stroke="{c["haar"]}" stroke-width="1"/>')
    o.append(f'<polygon points="{poly(nach)}" fill="{c["stempel"]}" fill-opacity="{c["nachOp"]}" stroke="{c["stempel"]}" stroke-width="1.5" stroke-linejoin="round"/>')
    o.append(f'<polygon points="{poly(vor)}" fill="{c["vorFill"]}" fill-opacity="{c["vorOp"]}" stroke="{c["grau"]}" stroke-width="1.25" stroke-dasharray="4 3" stroke-linejoin="round"/>')
    # +20 %-Marken über den verbesserten Übergängen
    for i in sorted(better):
        xm=(xs[i]+xs[i+1])/2
        o.append(f'<text x="{xm:.1f}" y="{top-18}" text-anchor="middle" font-family="IBM Plex Mono, ui-monospace, monospace" font-size="11" font-weight="500" letter-spacing=".08em" fill="{c["stempel"]}">+ 20 %</text>')
    # Endmarken rechts
    xe=xs[-1]
    hv=vor[-1]*sc/2; hn=nach[-1]*sc/2
    o.append(f'<line x1="{xe+10:.1f}" y1="{mid-hn:.1f}" x2="{xe+10:.1f}" y2="{mid+hn:.1f}" stroke="{c["stempel"]}" stroke-width="2"/>')
    o.append(f'<text x="{xe+20:.1f}" y="{mid-hn-10:.1f}" font-family="IBM Plex Mono, ui-monospace, monospace" font-size="11" font-weight="500" letter-spacing=".12em" fill="{c["stempel"]}">NACHHER</text>')
    o.append(f'<text x="{xe+20:.1f}" y="{mid+5:.1f}" font-family="Satoshi, Figtree, sans-serif" font-size="26" font-weight="500" letter-spacing="-.02em" fill="{c["tinte"]}">rund 2 ×</text>')
    o.append(f'<text x="{xe+20:.1f}" y="{mid+hn+22:.1f}" font-family="IBM Plex Mono, ui-monospace, monospace" font-size="11" font-weight="500" letter-spacing=".12em" fill="{c["matt"]}">SO VIELE WIE VORHER</text>')
    if labels_on:
        for i,(x,l) in enumerate(zip(xs,labels)):
            o.append(f'<text x="{x:.1f}" y="{top+band+30}" text-anchor="middle" font-family="IBM Plex Mono, ui-monospace, monospace" font-size="10.5" font-weight="500" letter-spacing=".08em" fill="{c["matt"]}">{i+1:02d}</text>')
            o.append(f'<text x="{x:.1f}" y="{top+band+46}" text-anchor="middle" font-family="IBM Plex Mono, ui-monospace, monospace" font-size="11" font-weight="500" letter-spacing=".1em" fill="{c["grau"]}">{l}</text>')
    o.append('</svg>')
    return '\n'.join(o)

if __name__=='__main__':
    print(json.dumps({'vor':[round(v,1) for v in vor],'nach':[round(v,1) for v in nach],'ratio':round(nach[-1]/vor[-1],3)}))
    open('abb2-hell.svg','w').write(svg(x1=900-190))

