#!/usr/bin/env python3
"""Deterministischer Linter fuer Print-Briefe und LinkedIn-Beitraege.

Nutzung:
  channel-check.py brief FILE      Brief an eine Geschaeftsfuehrung
  channel-check.py linkedin FILE   LinkedIn-Inbound-Beitrag
  channel-check.py --strip FILE    Entwurf ohne Marker ausgeben (Druck/Posting)

Exit 0 = keine Befunde, 1 = Befunde, 2 = Aufruffehler. Der Linter findet Muster,
keine Wahrheit: er prueft, dass eine Quelle genannt ist, nicht ob sie stimmt.

Ein Brief-Entwurf beginnt mit einem Front-Matter-Block zwischen zwei --- Zeilen:
  adressquelle:, widerspruchsliste_geprueft: ja YYYY-MM-DD,
  antwortweg: marktcheck|white-label|projektanfrage, freigabe: Kuerzel YYYY-MM-DD | offen
Der Block wird nicht als Brieftext geprueft; --strip entfernt ihn.

Marker im Entwurf:
  [Q: canon:<schluessel>]       Wert aus blocksy-child/inc/canon/ (Getter oder Schluessel)
  [Q: <Repo-Pfad|Beschreibung>] Messung, Pruefung, Dokument
  [CTA]                         der eine Antwortweg des Briefs
HTML-Kommentare (<!-- ... -->) sind Notizen und zaehlen nirgends.
"""
import re
import sys
from datetime import date
from pathlib import Path
from urllib.parse import urlparse

ROOT = Path(__file__).resolve().parents[4]
CANON = ROOT / 'blocksy-child/inc/canon'

MAX_BRIEF_WORDS = 350
MAX_POST_CHARS = 3000
MAX_HOOK_CHARS = 120
MAX_HASHTAGS = 3

SOURCE = re.compile(r'\[Q:[^\]\n]*\]')
CTA = re.compile(r'\[CTA\]')
ANY_MARKER = re.compile(r'\[Q:[^\]\n]*\]|\[CTA\]')
COMMENT = re.compile(r'<!--.*?-->', re.S)

# Zitate wie "Art. 14 DSGVO" oder "§ 7 Abs. 2 Nr. 1" sind keine Behauptungszahlen.
LEGAL_CITE = re.compile(
    r'(?:Art\.|Artikel|§§?)\s*\d+[a-z]?(?:\s*(?:Abs\.|Absatz|Nr\.|S\.|Satz|lit\.)\s*\d+[a-z]?(?:\s*(?:/|und|bis|,)\s*\d+)*)*')
# Abkuerzungen, nach denen ein Punkt keinen Satz beendet.
ABBREVIATIONS = {'art', 'abs', 'nr', 'bzw', 'ca', 'ggf', 'usw', 'vgl', 'dr', 'inkl', 'z', 'b', 'd', 'h', 's'}
SENTENCE_END = re.compile(r'[.!?]+(?:[ \t]*\x01+)*(?=\s|$)')

AGENCY = re.compile(
    r'ganzheitlich|ma(?:ß|ss)geschneidert|innovativ|rundum[- ]sorglos|360\s*°|synergie|mehrwert'
    r'|n(?:ä|ae)chste[nrs]?\s+level|next level|game[- ]?changer|full[- ]service'
    r'|\bgrowth\b(?!\s+(?:audit|architect))', re.I)
BRAND_BAN = re.compile(
    r'founding|growth audit|wgos|growth architect|performance-marketing-agentur', re.I)
URGENCY = re.compile(
    r'nur noch|letzte chance|pl(?:ä|ae)tze?\s+(?:noch\s+)?frei|freie pl(?:ä|ae)tze|\bfrist|bis zum\s+\d', re.I)
FREE = re.compile(r'kostenlos', re.I)
PLACEHOLDER = re.compile(r'\[(?:BELEG FEHLT|KUNDENSPRACHE FEHLT|DEFINITION OWNER)[^\]]*\]|\bTODO\b|\bTBD\b')

SALUTATION_LINE = re.compile(r'^\s*(?:Sehr geehrte[rn]?|Guten Tag|Hallo|Liebe[rn]?)\b[^\n]*$', re.I)
BAD_SALUTATION = re.compile(r'Sehr\s+geehrte\s+Damen\s+und\s+Herren', re.I)
POSTSCRIPT = re.compile(r'^\s*[*_>]*P\.\s?S\.', re.M)
OBJECTION = re.compile(r'Widerspruch|widersprechen', re.I)
DATA_SOURCE = re.compile(r'Datenschutz|Art\.\s*14|Datenquelle', re.I)

URL = re.compile(r'https?://|www\.|\b[a-z0-9-]+\.(?:de|com|net|org|io|eu)\b', re.I)
HASHTAG = re.compile(r'(?<![\w#])#[\wÄÖÜäöüß]+')
ENGAGEMENT_BAIT = re.compile(
    r'\bkommentier\w*|\blike[,]?\s+wenn|\blink\s+(?:in|im)\s+(?:den|dem|ersten)?\s*kommentar\w*'
    r'|teile\s+(?:diesen|den)\s+(?:post|beitrag)|\bfolge\s+mir\b|\bmarkier\w*\s+(?:jemand|einen|deine)'
    r'|schreib\w*\s+(?:mir\s+)?(?:\w+\s+)?in\s+die\s+kommentare|hinterlasse\s+einen\s+kommentar'
    r'|schreib\s+mir\s+(?:eine\s+)?(?:dm|nachricht)|\bdm\b', re.I)
SALES_CTA = re.compile(
    r'termin\s+(?:buchen|vereinbaren)|jetzt\s+anfragen|kontaktier\w*\s+mich|melde\s+dich\b', re.I)
LIMIT_SIGNAL = re.compile(r'Gilt nicht|Grenze|Ausnahme|funktioniert nicht, wenn', re.I)
MIN_LIMIT_WORDS = 8

NAMED_SALUTATION = re.compile(r'^(?:Sehr geehrte Frau|Sehr geehrter Herr|Guten Tag,? (?:Frau|Herr)) \S+')

# Antwortwege im Brieftext (ohne Marker): URL, E-Mail, Telefon, Antwort-Formulierung.
EMAIL = re.compile(r'[\w.+-]+@[\w-]+(?:\.[\w-]+)+')
CHANNEL_URL = re.compile(r'(?:https?://|www\.)[^\s<>()\[\]"“”„]+', re.I)
PHONE = re.compile(r'(?<![\w@/.:-])(?:\+49|0049|0\d)(?:[ ./()-]{0,2}\d)+')
PHONE_DATE = re.compile(r'0?\d{1,2}[./]\d{1,2}[./]\d{2,4}')
REPLY_PHRASE = re.compile(r'\b(?:antworten|rufen|schreiben)\s+Sie\b', re.I)
MIN_PHONE_DIGITS = 7

# Front Matter: Pflichtfelder und die drei Antwortrouten (docs/architecture/CONVERSION_ROUTING.md).
FM_LINE = re.compile(r'^\s*([A-Za-z_]+)\s*:\s*(.*?)\s*$')
FM_RULES = {'adressquelle': 'fm-adressquelle', 'widerspruchsliste_geprueft': 'fm-widerspruch',
            'antwortweg': 'fm-antwortweg', 'freigabe': 'fm-freigabe'}
ISO_DATE = re.compile(r'\d{4}-\d{2}-\d{2}')
# Antwortroute -> Linktext in llms.txt. Der Pfad selbst wird zur Laufzeit aus llms.txt gelesen
# (CONTEXT.md: Routen nie kopieren). Geprueft wird nur, wenn der Brief eine URL enthaelt.
ROUTE_LABELS = {
    'marktcheck': 'Marktcheck',
    'white-label': 'Für Agenturen: White-Label',
    'projektanfrage': 'Projekt anfragen',
}
LLMS_LINK = re.compile(r'^- \[(?P<label>[^\]]+)\]\((?P<href>[^)\s]+)\)', re.M)


def route_target(route):
    """Pfad und Pflicht-Query der Route aus llms.txt; fehlt der Eintrag, ist das ein Bedienfehler."""
    llms = ROOT / 'llms.txt'
    links = {m['label']: m['href'] for m in LLMS_LINK.finditer(llms.read_text(encoding='utf-8'))}
    href = links.get(ROUTE_LABELS[route])
    if not href:
        print(f'FEHLER: Route {route!r} ({ROUTE_LABELS[route]}) fehlt in {llms}.', file=sys.stderr)
        sys.exit(2)
    parsed = urlparse(href)
    return parsed.path, parsed.query

_canon_text = None


def canon_text():
    global _canon_text
    if _canon_text is None:
        _canon_text = '\n'.join(p.read_text(errors='ignore') for p in sorted(CANON.glob('*.php'))) \
            if CANON.is_dir() else ''
    return _canon_text


def blank_comments(raw):
    """Kommentare durch Leerzeichen ersetzen, Zeilennummern bleiben stabil."""
    return COMMENT.sub(lambda m: re.sub(r'[^\n]', ' ', m.group()), raw)


def clean_line(line):
    """Marker entfernen, Zwischenraum bereinigen."""
    lead = '' if line.lstrip().startswith('[') else line[:len(line) - len(line.lstrip())]
    body = ANY_MARKER.sub('', line.strip())
    body = re.sub(r'[ \t]+([.,;:!?])', r'\1', body)
    body = re.sub(r'(?<=\S)[ \t]{2,}', ' ', body).strip()
    return lead + body if body else ''


def strip_markers(raw):
    text = COMMENT.sub('', raw)
    out = []
    for line in text.split('\n'):
        cleaned = clean_line(line)
        if cleaned or not ANY_MARKER.search(line):
            out.append(cleaned)
    return re.sub(r'\n{3,}', '\n\n', '\n'.join(out)).strip() + '\n'


def paragraphs(lines):
    """(Startzeile, [Zeilen]) je Absatz; Zeilennummern 1-basiert."""
    block, start = [], 0
    for number, line in enumerate(lines, 1):
        if line.strip():
            if not block:
                start = number
            block.append(line)
        elif block:
            yield start, block
            block = []
    if block:
        yield start, block


def sentences(base_line, block):
    """(Zeile, Satz) je Satz. Ein Marker direkt hinter dem Satzende gehoert zu diesem Satz."""
    text = '\n'.join(block)
    masked = SOURCE.sub(lambda m: '\x01' * len(m.group()), text)
    start = 0
    pieces = []
    for match in SENTENCE_END.finditer(masked):
        word = re.search(r'(\w+)\W*$', masked[:match.start()])
        if word and word.group(1).lower() in ABBREVIATIONS:
            continue
        pieces.append((start, match.end()))
        start = match.end()
    if text[start:].strip():
        pieces.append((start, len(text)))
    for begin, end in pieces:
        segment = text[begin:end]
        offset = begin + len(segment) - len(segment.lstrip())
        if segment.strip():
            yield base_line + text.count('\n', 0, offset), segment


def snippet(text, limit=48):
    flat = ' '.join(text.split())
    return flat if len(flat) <= limit else flat[:limit - 1] + '…'


def front_matter_span(lines):
    """(Start, Ende) der --- Zeilen; Ende None, wenn der Block nicht geschlossen ist; None ohne Block."""
    first = next((i for i, line in enumerate(lines) if line.strip()), None)
    if first is None or lines[first].strip() != '---':
        return None
    for index in range(first + 1, len(lines)):
        if lines[index].strip() == '---':
            return first, index
    return first, None


def valid_date(text):
    try:
        date.fromisoformat(text)
    except ValueError:
        return False
    return True


def front_matter_checks(lines, span, report):
    """Pflichtfelder pruefen; gibt den Wert von antwortweg zurueck (oder None)."""
    start = span[0] + 1 if span else None
    fields = {}
    if span and span[1] is None:
        report.add(start, 'fm-block', 'Front Matter nicht mit --- geschlossen')
    elif span:
        for number in range(span[0] + 1, span[1]):
            match = FM_LINE.match(lines[number])
            if match:
                fields.setdefault(match.group(1).lower(), (number + 1, match.group(2)))
    for key, rule in FM_RULES.items():
        number, value = fields.get(key, (start, ''))
        if not value:
            where = 'leer' if key in fields else 'fehlt (Front-Matter-Block am Anfang des Entwurfs)'
            report.add(number, rule, f'{key}: {where}')
    route = None
    number, value = fields.get('widerspruchsliste_geprueft', (start, ''))
    if value:
        match = re.match(r'ja\b\W*(\d{4}-\d{2}-\d{2})\W*$', value, re.I)
        if not match or not valid_date(match.group(1)):
            report.add(number, 'fm-widerspruch', 'erwartet "ja YYYY-MM-DD" (Datum des Abgleichs)')
    number, value = fields.get('antwortweg', (start, ''))
    if value:
        if value.lower() in ROUTE_LABELS:
            route = value.lower()
        else:
            report.add(number, 'fm-antwortweg', 'erwartet marktcheck | white-label | projektanfrage')
    number, value = fields.get('freigabe', (start, ''))
    if value.lower() == 'offen':
        report.add(number, 'freigabe-offen', 'freigabe: offen; Inhaber hat noch nicht freigegeben')
    elif value:
        before = ISO_DATE.search(value)
        if not before or not value[:before.start()].strip(' ,;-:') or not valid_date(before.group(0)):
            report.add(number, 'fm-freigabe', 'erwartet "Name/Kuerzel YYYY-MM-DD" oder "offen"')
    return route


def blank_span(lines, span):
    """Front-Matter-Zeilen leeren, Zeilennummern bleiben stabil."""
    if not span or span[1] is None:
        return lines
    return ['' if span[0] <= i <= span[1] else line for i, line in enumerate(lines)]


def reply_channels(clean_lines):
    """Distinkte Antwortwege (URL, E-Mail, Telefon, Antwort-Formulierung) im Brieftext.

    Dieselbe URL/E-Mail/Nummer zaehlt einmal (das P.S. darf den einen Weg wiederholen). Eine
    Formulierung ("antworten Sie", "rufen Sie", "schreiben Sie") ist ein eigener Weg, ausser derselbe
    Satz nennt den passenden Wert (Telefon bzw. E-Mail/URL).
    """
    found, all_urls = set(), set()
    for base, block in paragraphs(clean_lines):
        for _, sentence in sentences(base, block):
            urls = {m.group(0).rstrip('.,;:!?').lower() for m in CHANNEL_URL.finditer(sentence)}
            rest = CHANNEL_URL.sub(' ', sentence)
            emails = {m.group(0).lower() for m in EMAIL.finditer(rest)}
            rest = EMAIL.sub(' ', rest)
            phones = set()
            for match in PHONE.finditer(rest):
                digits = re.sub(r'\D', '', match.group(0))
                if len(digits) >= MIN_PHONE_DIGITS and not PHONE_DATE.fullmatch(match.group(0).strip()):
                    phones.add(digits)
            found |= {('url', v) for v in urls} | {('email', v) for v in emails} | {('telefon', v) for v in phones}
            all_urls |= urls
            for match in REPLY_PHRASE.finditer(rest):
                call = match.group(0).lower().startswith('rufen')
                if not (phones if call else (emails or urls)):
                    found.add(('telefon', 'formulierung') if call else ('antwort', 'formulierung'))
    return found, all_urls


def route_consistency(route, urls, report):
    path, query = route_target(route)
    wanted_query = set(filter(None, query.split('&')))
    for url in sorted(urls):
        parsed = urlparse(url if '://' in url else '//' + url)
        same_path = parsed.path.rstrip('/') == path.rstrip('/')
        if not same_path or not wanted_query <= set(filter(None, parsed.query.split('&'))):
            expected = path + (f'?{query}' if query else '')
            report.add(None, 'antwortweg-url',
                       f'URL {url} passt nicht zu antwortweg: {route} (llms.txt: {expected})')


class Report:
    def __init__(self):
        self.items = []

    def add(self, line, rule, message):
        self.items.append((line if line is not None else 10 ** 9, rule, message))

    def lines(self):
        for line, rule, message in sorted(self.items):
            label = '-' if line == 10 ** 9 else str(line)
            yield f'L{label:<4} {rule:<20} {message}'


def common_checks(raw_lines, report):
    for number, line in enumerate(raw_lines, 1):
        cleaned = ANY_MARKER.sub('', line)
        for rule, pattern, label in (
                ('agentur-floskel', AGENCY, 'Agentursprache'),
                ('brand-bann', BRAND_BAN, 'gesperrter Begriff (BRAND Anti-Patterns)'),
                ('dringlichkeit', URGENCY, 'Dringlichkeit/Frist'),
                ('kostenlos', FREE, 'kostenlos als Wertversprechen? Mensch entscheidet')):
            for match in pattern.finditer(cleaned):
                report.add(number, rule, f'{label}: "{match.group(0)}"')
        for match in PLACEHOLDER.finditer(line):
            report.add(number, 'platzhalter', f'offener Platzhalter: {match.group(0)[:40]}')
        for match in SOURCE.finditer(line):
            content = match.group(0)[3:-1].strip()
            if not content:
                report.add(number, 'quelle-leer', 'Quellenmarker ohne Inhalt')
            key = re.match(r'canon:\s*(\S+)', content)
            if key and canon_text():
                name = re.sub(r'\(\)$', '', key.group(1))
                if name not in canon_text():
                    report.add(number, 'quelle-unbekannt', f'canon:{name} steht nicht in inc/canon/')
    for base, block in paragraphs(raw_lines):
        for number, sentence in sentences(base, block):
            plain = LEGAL_CITE.sub('', ANY_MARKER.sub('', sentence))
            if re.search(r'\d', plain) and not SOURCE.search(sentence):
                report.add(number, 'zahl-ohne-quelle', f'"{snippet(plain)}"')


def brief_checks(raw_lines, report, route=None):
    text = '\n'.join(raw_lines)
    clean = '\n'.join(clean_line(line) for line in raw_lines)
    for number, line in enumerate(raw_lines, 1):
        if BAD_SALUTATION.search(line):
            report.add(number, 'anrede', 'keine Sammelanrede; eine namentlich genannte Person ansprechen')
    first_line = next(((n, l) for n, l in enumerate(raw_lines, 1) if l.strip()), None)
    if not first_line or not NAMED_SALUTATION.match(first_line[1].strip()):
        report.add(first_line[0] if first_line else None, 'anrede-nicht-namentlich',
                   'erste Zeile muss namentlich anreden: Sehr geehrte Frau|Sehr geehrter Herr|Guten Tag Frau|Herr <Name>')
    channels, urls = reply_channels([clean_line(line) for line in raw_lines])
    if len(channels) > 1:
        report.add(None, 'mehrere-antwortwege',
                   f'{len(channels)} Antwortwege im Text ({", ".join(sorted(k for k, _ in channels))}); genau einer erlaubt')
    if route:
        route_consistency(route, urls, report)
    words = len(clean.split())
    if words > MAX_BRIEF_WORDS:
        report.add(None, 'laenge', f'{words} Wörter, höchstens {MAX_BRIEF_WORDS}')
    body = [(n, b) for n, b in paragraphs(raw_lines)
            if not (len(b) == 1 and SALUTATION_LINE.match(b[0]))]
    if body:
        number, block = body[0]
        if not SOURCE.search('\n'.join(block)):
            report.add(number, 'einstieg-ohne-fakt', 'erster Absatz ohne [Q: …]-Marker (beobachteter Fakt)')
        first = next(sentences(number, block), None)
        if first and re.match(r'[\s*_>"„]*(?:Ich|Wir)\b', ANY_MARKER.sub('', first[1]), re.I):
            report.add(first[0], 'einstieg-ich-wir', 'erster Satz beginnt mit Ich/Wir; beim Leser beginnen')
    ctas = [text.count('\n', 0, m.start()) + 1 for m in CTA.finditer(text)]
    if not ctas:
        report.add(None, 'cta-anzahl', 'kein [CTA]; genau ein Antwortweg erforderlich')
    for number in ctas[1:]:
        report.add(number, 'cta-anzahl', f'{len(ctas)} [CTA]; genau einer erlaubt')
    if not POSTSCRIPT.search(text):
        report.add(None, 'ps-fehlt', 'keine P.S.-Zeile')
    missing = [name for name, pattern in (('Widerspruch', OBJECTION), ('Datenquelle/Datenschutz', DATA_SOURCE))
               if not pattern.search(clean)]
    if missing:
        report.add(None, 'widerspruch-quelle', 'Hinweis fehlt: ' + ' und '.join(missing))
    return words


def linkedin_checks(raw_lines, report):
    cleaned = [clean_line(line) for line in raw_lines]
    total = len('\n'.join(cleaned).strip())
    if total > MAX_POST_CHARS:
        report.add(None, 'laenge', f'{total} Zeichen, höchstens {MAX_POST_CHARS}')
    for number, line in enumerate(cleaned, 1):
        if line.strip():
            if len(line) > MAX_HOOK_CHARS:
                report.add(number, 'hook-laenge', f'erste Zeile {len(line)} Zeichen, höchstens {MAX_HOOK_CHARS}')
            break
    tags = 0
    for number, line in enumerate(cleaned, 1):
        match = URL.search(line)
        if match:
            report.add(number, 'url', f'URL im Beitrag: "{match.group(0)}"')
        for _ in HASHTAG.finditer(line):
            tags += 1
            if tags == MAX_HASHTAGS + 1:
                report.add(number, 'hashtags', f'mehr als {MAX_HASHTAGS} Hashtags')
        for match in ENGAGEMENT_BAIT.finditer(line):
            report.add(number, 'engagement-koeder', f'Engagement-Köder: "{match.group(0)}"')
        for match in SALES_CTA.finditer(line):
            report.add(number, 'cta-verboten', f'Verkaufs-CTA im Beitrag: "{match.group(0)}"')
    for number, line in enumerate(raw_lines, 1):
        if CTA.search(line):
            report.add(number, 'cta-verboten', '[CTA] gehört nicht in einen LinkedIn-Beitrag')
    if not any(SOURCE.search(line) for line in raw_lines):
        report.add(None, 'beobachtung-ohne-quelle', 'kein [Q: …]-Marker; die Beobachtung braucht eine Quelle')
    first = next((i for i, line in enumerate(cleaned) if line.strip()), None)
    limit_lines = [line for i, line in enumerate(cleaned) if i != first and LIMIT_SIGNAL.search(line)]
    if not LIMIT_SIGNAL.search('\n'.join(cleaned)):
        report.add(None, 'grenze-fehlt', 'keine Grenze (Gilt nicht|Grenze|Ausnahme|funktioniert nicht, wenn)')
    elif not any(len(line.split()) >= MIN_LIMIT_WORDS for line in limit_lines):
        report.add(None, 'grenze-zu-duenn', f'Grenze-Zeile hat weniger als {MIN_LIMIT_WORDS} Wörter; sagen, wann die These nicht gilt')
    return total


USAGE = ('Nutzung: channel-check.py brief FILE | linkedin FILE | --strip FILE\n'
         'Exit 0 ok, 1 Befunde, 2 Aufruffehler.')


def main(argv):
    args = [a for a in argv[1:] if a != '--strip']
    strip = len(args) != len(argv) - 1
    if '-h' in args or '--help' in args:
        print(USAGE)
        return 0
    mode = None
    if args and args[0] in ('brief', 'linkedin'):
        mode = args.pop(0)
    elif args and not strip:
        print(f'Unbekannter Unterbefehl: {args[0]}\n{USAGE}', file=sys.stderr)
        return 2
    if len(args) != 1 or (mode is None and not strip):
        print(USAGE, file=sys.stderr)
        return 2
    path = Path(args[0])
    if not path.is_file():
        print(f'Datei nicht gefunden: {path}', file=sys.stderr)
        return 2
    raw = path.read_text(encoding='utf-8')
    lines = blank_comments(raw).split('\n')
    span = front_matter_span(lines)
    if strip:
        kept = raw.split('\n')
        if span and span[1] is not None:
            kept = kept[:span[0]] + kept[span[1] + 1:]
        sys.stdout.write(strip_markers('\n'.join(kept)))
        return 0
    report = Report()
    route = None
    if mode == 'brief':
        route = front_matter_checks(lines, span, report)
        lines = blank_span(lines, span)
    common_checks(lines, report)
    size = brief_checks(lines, report, route) if mode == 'brief' else linkedin_checks(lines, report)
    for line in report.lines():
        print(line)
    unit = 'Wörter' if mode == 'brief' else 'Zeichen'
    verdict = f'{len(report.items)} Befunde' if report.items else 'ok'
    print(f'{mode}: {verdict}, {size} {unit}')
    return 1 if report.items else 0


if __name__ == '__main__':
    sys.exit(main(sys.argv))
