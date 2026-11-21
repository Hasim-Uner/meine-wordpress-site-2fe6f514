#!/usr/bin/env python3
"""Deterministischer Linter fuer Print-Briefe und LinkedIn-Beitraege.

Nutzung:
  channel-check.py brief FILE      Brief an eine Geschaeftsfuehrung
  channel-check.py linkedin FILE   LinkedIn-Inbound-Beitrag
  channel-check.py --strip FILE    Entwurf ohne Marker ausgeben (Druck/Posting)

Exit 0 = keine Befunde, 1 = Befunde, 2 = Aufruffehler. Der Linter findet Muster,
keine Wahrheit: er prueft, dass eine Quelle genannt ist, nicht ob sie stimmt.

Marker im Entwurf:
  [Q: canon:<schluessel>]       Wert aus blocksy-child/inc/canon/ (Getter oder Schluessel)
  [Q: <Repo-Pfad|Beschreibung>] Messung, Pruefung, Dokument
  [CTA]                         der eine Antwortweg des Briefs
HTML-Kommentare (<!-- ... -->) sind Notizen und zaehlen nirgends.
"""
import re
import sys
from pathlib import Path

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


def brief_checks(raw_lines, report):
    text = '\n'.join(raw_lines)
    clean = '\n'.join(clean_line(line) for line in raw_lines)
    for number, line in enumerate(raw_lines, 1):
        if BAD_SALUTATION.search(line):
            report.add(number, 'anrede', 'keine Sammelanrede; eine namentlich genannte Person ansprechen')
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
    if not LIMIT_SIGNAL.search('\n'.join(cleaned)):
        report.add(None, 'grenze-fehlt', 'keine Grenze (Gilt nicht|Grenze|Ausnahme|funktioniert nicht, wenn)')
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
    if strip:
        sys.stdout.write(strip_markers(raw))
        return 0
    lines = blank_comments(raw).split('\n')
    report = Report()
    common_checks(lines, report)
    size = brief_checks(lines, report) if mode == 'brief' else linkedin_checks(lines, report)
    for line in report.lines():
        print(line)
    unit = 'Wörter' if mode == 'brief' else 'Zeichen'
    verdict = f'{len(report.items)} Befunde' if report.items else 'ok'
    print(f'{mode}: {verdict}, {size} {unit}')
    return 1 if report.items else 0


if __name__ == '__main__':
    sys.exit(main(sys.argv))
