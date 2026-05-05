import React, {useMemo, useState} from 'react';
import {
  Battery,
  BarChart3,
  Building2,
  CheckCircle2,
  ChevronLeft,
  ChevronRight,
  Download,
  FileText,
  Home,
  PlugZap,
  ShieldCheck,
  SlidersHorizontal,
  Sun,
  Workflow,
  Zap,
} from 'lucide-react';
import {
  BarElement,
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  Tooltip,
} from 'chart.js';
import {Bar} from 'react-chartjs-2';

ChartJS.register(CategoryScale, LinearScale, BarElement, Tooltip, Legend);

type Orientation = 'S' | 'SW_SE' | 'W_E' | 'N';
type PropertyType = 'single_family' | 'two_family' | 'small_business';
type Timeline = 'now' | 'quarter' | 'later';

interface DemoInput {
  propertyType: PropertyType;
  annualConsumption: number;
  roofArea: number;
  orientation: Orientation;
  pvSize: number;
  storageSize: number;
  hasWallbox: boolean;
  region: string;
  timeline: Timeline;
}

interface CalculationResult {
  annualYield: number;
  autarchyRate: number;
  selfConsumptionRate: number;
  annualSavings: number;
  investmentCost: number;
  amortizationYears: number;
  fitScore: number;
  recommendation: string;
  leadPriority: 'hoch' | 'mittel' | 'niedrig';
}

const initialInput: DemoInput = {
  propertyType: 'single_family',
  annualConsumption: 4800,
  roofArea: 58,
  orientation: 'S',
  pvSize: 8,
  storageSize: 6,
  hasWallbox: true,
  region: 'Region Hannover',
  timeline: 'quarter',
};

const steps = [
  {id: 1, label: 'Bedarf', description: 'Käufer beantwortet Basisfragen'},
  {id: 2, label: 'System', description: 'Konfiguration wird berechnet'},
  {id: 3, label: 'Ergebnis', description: 'Empfehlung und PDF entstehen'},
  {id: 4, label: 'Übergabe', description: 'Prozess hinter dem Funnel'},
];

const formatNumber = (value: number) => new Intl.NumberFormat('de-DE').format(Math.round(value));
const formatMoney = (value: number) => `${formatNumber(value)} €`;
const defaultAnalysisUrl = '/anfrage-system-analyse/';

const getAnalysisUrl = () => {
  const root = document.getElementById('energie-fahrplan-root');
  const configuredUrl = root?.dataset.analysisUrl?.trim();

  return configuredUrl || defaultAnalysisUrl;
};

const orientationLabel = (orientation: Orientation) => {
  const labels: Record<Orientation, string> = {
    S: 'Süd',
    SW_SE: 'Süd-West / Süd-Ost',
    W_E: 'West / Ost',
    N: 'Nord',
  };

  return labels[orientation];
};

const propertyLabel = (propertyType: PropertyType) => {
  const labels: Record<PropertyType, string> = {
    single_family: 'Einfamilienhaus',
    two_family: 'Zweifamilienhaus',
    small_business: 'Kleingewerbe',
  };

  return labels[propertyType];
};

const timelineLabel = (timeline: Timeline) => {
  const labels: Record<Timeline, string> = {
    now: '0-3 Monate',
    quarter: '3-6 Monate',
    later: 'später / offen',
  };

  return labels[timeline];
};

const calculateSolarSystem = (input: DemoInput): CalculationResult => {
  const orientationFactors: Record<Orientation, number> = {
    S: 1,
    SW_SE: 0.94,
    W_E: 0.84,
    N: 0.58,
  };

  const usablePvSize = Math.min(input.pvSize, Math.max(3, input.roofArea / 6));
  const annualYield = usablePvSize * 980 * orientationFactors[input.orientation];
  const maxAutarchy = Math.min(96, (annualYield / input.annualConsumption) * 100);
  const baseAutarchy = Math.min(34, maxAutarchy * 0.45);
  const storageBoost = input.storageSize > 0 ? Math.min(48, Math.sqrt(input.storageSize) * 14) : 0;
  const wallboxBoost = input.hasWallbox ? 5 : 0;
  const autarchyRate = Math.min(88, baseAutarchy + storageBoost + wallboxBoost);
  const annualSelfConsumedKwh = (input.annualConsumption * autarchyRate) / 100;
  const selfConsumptionRate = Math.min(100, (annualSelfConsumedKwh / annualYield) * 100);
  const annualSavings = annualSelfConsumedKwh * 0.35 + Math.max(0, annualYield - annualSelfConsumedKwh) * 0.082;
  const investmentCost = usablePvSize * 1450 + input.storageSize * 780 + (input.hasWallbox ? 1200 : 0);
  const amortizationYears = annualSavings > 0 ? investmentCost / annualSavings : 99;

  const score =
    Math.min(35, usablePvSize * 3) +
    Math.min(25, input.storageSize * 3) +
    (input.hasWallbox ? 12 : 0) +
    (input.timeline === 'now' ? 18 : input.timeline === 'quarter' ? 13 : 6) +
    (input.orientation === 'N' ? -14 : 8);

  const fitScore = Math.max(20, Math.min(98, Math.round(score)));
  const leadPriority = fitScore >= 78 ? 'hoch' : fitScore >= 58 ? 'mittel' : 'niedrig';
  const recommendation =
    leadPriority === 'hoch'
      ? 'Hoher Projekt-Fit: PV, Speicher und Timing passen gut zusammen. In einem echten Anfrage-System würde dieser Lead priorisiert.'
      : leadPriority === 'mittel'
        ? 'Solider Projekt-Fit: Das System erkennt Potenzial, markiert aber offene Punkte für die Beratung.'
        : 'Niedriger Projekt-Fit: Die Anfrage wäre nicht verloren, aber sie würde nicht als Sofort-Priorität laufen.';

  return {
    annualYield,
    autarchyRate,
    selfConsumptionRate,
    annualSavings,
    investmentCost,
    amortizationYears,
    fitScore,
    recommendation,
    leadPriority,
  };
};

const trackDemoEvent = (eventName: string, payload: Record<string, unknown> = {}) => {
  const detail = {
    event: eventName,
    funnel: 'energie_fahrplan_demo',
    mode: 'local_demo',
    ...payload,
  };

  console.info('[EnergieFahrplan Demo]', detail);
  window.dispatchEvent(new CustomEvent('hu:demo-track', {detail}));
};

function App() {
  const [step, setStep] = useState(1);
  const [input, setInput] = useState<DemoInput>(initialInput);
  const [isExporting, setIsExporting] = useState(false);
  const analysisUrl = getAnalysisUrl();
  const result = useMemo(() => calculateSolarSystem(input), [input]);
  const reportId = useMemo(
    () => `EFD-${input.region.replace(/\W+/g, '').slice(0, 3).toUpperCase()}-${Math.round(input.pvSize * 10)}-${Math.round(input.storageSize * 10)}`,
    [input.region, input.pvSize, input.storageSize],
  );

  const goToStep = (nextStep: number) => {
    const boundedStep = Math.max(1, Math.min(steps.length, nextStep));
    if (boundedStep > step) {
      trackDemoEvent(`demo_step_${step}_completed`, {next_step: boundedStep});
    }

    if (boundedStep === 3) {
      trackDemoEvent('demo_result_shown', {fit_score: result.fitScore, priority: result.leadPriority});
    }

    setStep(boundedStep);
    window.scrollTo({top: 0, behavior: 'smooth'});
  };

  const updateInput = <K extends keyof DemoInput>(key: K, value: DemoInput[K]) => {
    setInput((current) => ({...current, [key]: value}));
  };

  const exportPdf = async () => {
    const report = document.getElementById('energy-demo-report');
    if (!report) {
      return;
    }

    setIsExporting(true);
    trackDemoEvent('demo_pdf_export_started', {report_id: reportId});

    try {
      const [{default: html2canvas}, {default: jsPDF}] = await Promise.all([
        import('html2canvas'),
        import('jspdf'),
      ]);
      const canvas = await html2canvas(report, {
        scale: 2,
        backgroundColor: '#ffffff',
      });
      const imgData = canvas.toDataURL('image/png');
      const pdf = new jsPDF('p', 'mm', 'a4');
      const pageWidth = pdf.internal.pageSize.getWidth();
      const pageHeight = pdf.internal.pageSize.getHeight();
      const imgHeight = (canvas.height * pageWidth) / canvas.width;
      let heightLeft = imgHeight;
      let position = 0;

      pdf.addImage(imgData, 'PNG', 0, position, pageWidth, imgHeight);
      heightLeft -= pageHeight;

      while (heightLeft > 0) {
        position -= pageHeight;
        pdf.addPage();
        pdf.addImage(imgData, 'PNG', 0, position, pageWidth, imgHeight);
        heightLeft -= pageHeight;
      }

      pdf.save(`energie-fahrplan-demo-${reportId.toLowerCase()}.pdf`);
      trackDemoEvent('demo_pdf_export_success', {report_id: reportId});
    } finally {
      setIsExporting(false);
    }
  };

  const chartData = {
    labels: ['Jan', 'Feb', 'Mrz', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
    datasets: [
      {
        label: 'PV-Ertrag',
        data: [180, 320, 560, 810, 1020, 1120, 1080, 930, 680, 430, 220, 150].map(
          (value) => value * (result.annualYield / 7500),
        ),
        backgroundColor: 'rgba(14, 165, 233, 0.72)',
      },
      {
        label: 'Verbrauch',
        data: Array(12).fill(input.annualConsumption / 12),
        backgroundColor: 'rgba(245, 158, 11, 0.62)',
      },
    ],
  };

  return (
    <main className="energy-demo-shell" data-track-section="energie_fahrplan_demo">
      <section className="energy-demo-hero" aria-labelledby="energy-demo-title">
        <div className="energy-demo-hero__copy">
          <span className="energy-demo-kicker">Interaktive Showroom-Demo</span>
          <h1 id="energy-demo-title">EnergieFahrplan aus Käufer-Sicht erleben.</h1>
          <p>
            Füllen Sie den Beispiel-Funnel aus und sehen Sie, wie daraus Ergebnis, PDF und Lead-Karte
            entstehen. Alles läuft lokal im Browser: keine Speicherung, kein CRM-Submit, kein n8n-Workflow.
          </p>
          <div className="energy-demo-hero__actions">
            <button
              type="button"
              className="energy-demo-btn energy-demo-btn--primary"
              onClick={() => goToStep(1)}
              data-track-action="demo_start"
              data-track-category="lead_funnel"
              data-track-funnel-stage="demo_view"
            >
              Demo ausfüllen
            </button>
            <a
              className="energy-demo-btn energy-demo-btn--ghost"
              href={analysisUrl}
              data-track-action="demo_cta_request_analysis_hero"
              data-track-category="lead_gen"
              data-track-funnel-stage="request_analysis"
            >
              Analyse starten
            </a>
          </div>
        </div>
        <aside className="energy-demo-hero__panel" aria-label="Demo-Grenzen">
          <ShieldCheck aria-hidden="true" />
          <h2>Demo-Modus</h2>
          <ul>
            <li>PDF wird lokal erzeugt.</li>
            <li>CRM und n8n werden nur erklärt.</li>
            <li>Keine personenbezogenen Daten nötig.</li>
          </ul>
        </aside>
      </section>

      <nav className="energy-demo-progress" aria-label="Demo-Fortschritt">
        {steps.map((item) => (
          <button
            key={item.id}
            type="button"
            className={item.id === step ? 'is-active' : item.id < step ? 'is-complete' : ''}
            onClick={() => item.id <= step && goToStep(item.id)}
            aria-current={item.id === step ? 'step' : undefined}
          >
            <span>{item.id}</span>
            <strong>{item.label}</strong>
            <small>{item.description}</small>
          </button>
        ))}
      </nav>

      <div className="energy-demo-layout">
        <aside className="energy-demo-operator" aria-label="Sicht des Betriebsinhabers">
          <span className="energy-demo-kicker">Betreiber-Sicht</span>
          <h2>Was der Betrieb sieht</h2>
          <p>
            Der Käufer erlebt einen einfachen Energie-Fahrplan. Der Betrieb bekommt strukturierte
            Entscheidungsdaten und kann daraus Rückruf, Beratung oder Absage ableiten.
          </p>
          <LeadCard input={input} result={result} reportId={reportId} />
        </aside>

        <section className="energy-demo-stage" aria-live="polite">
          {step === 1 && (
            <div className="energy-demo-step">
              <StepHead
                icon={<Home />}
                label="Schritt 1"
                title="Käufer gibt Bedarf und Kontext ein."
                text="Diese Felder sind bewusst grob. In einer echten Implementation werden sie pro Betrieb angepasst."
              />

              <div className="energy-demo-card-grid">
                {(['single_family', 'two_family', 'small_business'] as PropertyType[]).map((propertyType) => (
                  <button
                    key={propertyType}
                    type="button"
                    className={input.propertyType === propertyType ? 'energy-demo-choice is-selected' : 'energy-demo-choice'}
                    onClick={() => updateInput('propertyType', propertyType)}
                  >
                    <Building2 aria-hidden="true" />
                    <strong>{propertyLabel(propertyType)}</strong>
                  </button>
                ))}
              </div>

              <RangeField
                label="Jahresverbrauch"
                value={input.annualConsumption}
                min={1200}
                max={18000}
                step={100}
                suffix="kWh"
                onChange={(value) => updateInput('annualConsumption', value)}
              />

              <div className="energy-demo-form-grid">
                <label className="energy-demo-field">
                  <span>Region</span>
                  <select value={input.region} onChange={(event) => updateInput('region', event.target.value)}>
                    <option>Region Hannover</option>
                    <option>NRW</option>
                    <option>Norddeutschland</option>
                    <option>DACH</option>
                  </select>
                </label>
                <label className="energy-demo-field">
                  <span>Umsetzungsfenster</span>
                  <select value={input.timeline} onChange={(event) => updateInput('timeline', event.target.value as Timeline)}>
                    <option value="now">0-3 Monate</option>
                    <option value="quarter">3-6 Monate</option>
                    <option value="later">später / offen</option>
                  </select>
                </label>
              </div>
            </div>
          )}

          {step === 2 && (
            <div className="energy-demo-step">
              <StepHead
                icon={<SlidersHorizontal />}
                label="Schritt 2"
                title="System wird live konfiguriert."
                text="Jede Eingabe verändert Ergebnis, Score und spätere Lead-Karte. Genau das macht den Funnel verwertbar."
              />

              <RangeField
                label="Dachfläche"
                value={input.roofArea}
                min={20}
                max={160}
                step={2}
                suffix="m²"
                onChange={(value) => updateInput('roofArea', value)}
              />

              <div className="energy-demo-card-grid">
                {(['S', 'SW_SE', 'W_E', 'N'] as Orientation[]).map((orientation) => (
                  <button
                    key={orientation}
                    type="button"
                    className={input.orientation === orientation ? 'energy-demo-choice is-selected' : 'energy-demo-choice'}
                    onClick={() => updateInput('orientation', orientation)}
                  >
                    <Sun aria-hidden="true" />
                    <strong>{orientationLabel(orientation)}</strong>
                  </button>
                ))}
              </div>

              <div className="energy-demo-form-grid">
                <RangeField
                  label="PV-Größe"
                  value={input.pvSize}
                  min={3}
                  max={30}
                  step={0.5}
                  suffix="kWp"
                  onChange={(value) => updateInput('pvSize', value)}
                />
                <RangeField
                  label="Speicher"
                  value={input.storageSize}
                  min={0}
                  max={24}
                  step={1}
                  suffix="kWh"
                  onChange={(value) => updateInput('storageSize', value)}
                />
              </div>

              <button
                type="button"
                className={input.hasWallbox ? 'energy-demo-toggle is-active' : 'energy-demo-toggle'}
                onClick={() => updateInput('hasWallbox', !input.hasWallbox)}
              >
                <PlugZap aria-hidden="true" />
                <span>
                  <strong>Wallbox vorhanden oder geplant</strong>
                  <small>Erhöht Eigenverbrauch und Lead-Qualifizierung.</small>
                </span>
                <CheckCircle2 aria-hidden="true" />
              </button>
            </div>
          )}

          {step === 3 && (
            <div className="energy-demo-step">
              <div id="energy-demo-report" className="energy-demo-report">
                <StepHead
                  icon={<BarChart3 />}
                  label="Schritt 3"
                  title="Ergebnisbühne mit lokalem PDF."
                  text="Der Käufer bekommt eine verwertbare Orientierung. Der Betrieb bekommt gleichzeitig qualifizierte Daten."
                />

                <ResultSummary input={input} result={result} reportId={reportId} />

                <div className="energy-demo-chart">
                  <Bar
                    data={chartData}
                    options={{
                      responsive: true,
                      maintainAspectRatio: false,
                      plugins: {
                        legend: {position: 'bottom'},
                        tooltip: {backgroundColor: '#111827', padding: 12},
                      },
                      scales: {
                        x: {grid: {display: false}},
                        y: {beginAtZero: true},
                      },
                    }}
                  />
                </div>

                <div className="energy-demo-recommendation">
                  <h3>Empfehlung</h3>
                  <p>{result.recommendation}</p>
                </div>
              </div>

              <button
                type="button"
                className="energy-demo-btn energy-demo-btn--primary"
                onClick={exportPdf}
                disabled={isExporting}
                data-track-action="demo_pdf_download"
                data-track-category="lead_funnel"
                data-track-funnel-stage="demo_result_shown"
              >
                <Download aria-hidden="true" />
                {isExporting ? 'PDF wird erzeugt' : 'PDF lokal herunterladen'}
              </button>
            </div>
          )}

          {step === 4 && (
            <div className="energy-demo-step">
              <StepHead
                icon={<Workflow />}
                label="Schritt 4"
                title="Was in einem echten Kunden-System danach passiert."
                text="In dieser Demo wird nichts gesendet. In einer Custom-Implementation kann genau dieser Prozess angeschlossen werden."
              />

              <ProcessMap />

              <div className="energy-demo-final-grid">
                <LeadCard input={input} result={result} reportId={reportId} />
                <div className="energy-demo-next-card">
                  <span className="energy-demo-kicker">Nächster Schritt</span>
                  <h3>So könnte Ihr eigener Anfrageprozess aussehen.</h3>
                  <p>
                    Wenn Sie sehen möchten, ob ein solcher Funnel für Ihren Betrieb passt,
                    ist die Anfrage-System-Analyse der nächste Schritt. Dort geht es um Fit, Marktbild,
                    Anfragepfad und sinnvolle Systemgrenzen.
                  </p>
                  <a
                    className="energy-demo-btn energy-demo-btn--primary"
                    href={analysisUrl}
                    data-track-action="demo_cta_request_analysis_final"
                    data-track-category="lead_gen"
                    data-track-funnel-stage="request_analysis"
                  >
                    Analyse starten
                  </a>
                </div>
              </div>
            </div>
          )}

          <div className="energy-demo-nav">
            <button
              type="button"
              className="energy-demo-btn energy-demo-btn--secondary"
              onClick={() => goToStep(step - 1)}
              disabled={step === 1}
            >
              <ChevronLeft aria-hidden="true" />
              Zurück
            </button>
            <button
              type="button"
              className="energy-demo-btn energy-demo-btn--primary"
              onClick={() => (step < steps.length ? goToStep(step + 1) : goToStep(1))}
            >
              {step < steps.length ? 'Weiter' : 'Demo neu starten'}
              <ChevronRight aria-hidden="true" />
            </button>
          </div>
        </section>
      </div>
    </main>
  );
}

function StepHead({
  icon,
  label,
  title,
  text,
}: {
  icon: React.ReactNode;
  label: string;
  title: string;
  text: string;
}) {
  return (
    <header className="energy-demo-step-head">
      <div className="energy-demo-step-head__icon">{icon}</div>
      <div>
        <span className="energy-demo-kicker">{label}</span>
        <h2>{title}</h2>
        <p>{text}</p>
      </div>
    </header>
  );
}

function RangeField({
  label,
  value,
  min,
  max,
  step,
  suffix,
  onChange,
}: {
  label: string;
  value: number;
  min: number;
  max: number;
  step: number;
  suffix: string;
  onChange: (value: number) => void;
}) {
  return (
    <label className="energy-demo-range">
      <span>
        <strong>{label}</strong>
        <em>
          {formatNumber(value)} {suffix}
        </em>
      </span>
      <input
        type="range"
        min={min}
        max={max}
        step={step}
        value={value}
        onChange={(event) => onChange(Number(event.target.value))}
      />
    </label>
  );
}

function ResultSummary({
  input,
  result,
  reportId,
}: {
  input: DemoInput;
  result: CalculationResult;
  reportId: string;
}) {
  const metrics = [
    {label: 'Jahresertrag', value: `${formatNumber(result.annualYield)} kWh`, icon: <Sun />},
    {label: 'Autarkie', value: `${result.autarchyRate.toFixed(0)} %`, icon: <Battery />},
    {label: 'Ersparnis / Jahr', value: formatMoney(result.annualSavings), icon: <Zap />},
    {label: 'Amortisation', value: `${result.amortizationYears.toFixed(1)} Jahre`, icon: <BarChart3 />},
  ];

  return (
    <section className="energy-demo-result">
      <div className="energy-demo-result__head">
        <span>Report {reportId}</span>
        <strong>{propertyLabel(input.propertyType)} · {input.region}</strong>
      </div>
      <div className="energy-demo-metrics">
        {metrics.map((metric) => (
          <article key={metric.label}>
            {metric.icon}
            <span>{metric.label}</span>
            <strong>{metric.value}</strong>
          </article>
        ))}
      </div>
    </section>
  );
}

function LeadCard({
  input,
  result,
  reportId,
}: {
  input: DemoInput;
  result: CalculationResult;
  reportId: string;
}) {
  return (
    <section className="energy-demo-lead-card" aria-label="Installateur Lead-Karteikarte">
      <div className="energy-demo-lead-card__top">
        <span>Demo-Lead-Karte</span>
        <strong className={`priority-${result.leadPriority}`}>{result.leadPriority}</strong>
      </div>
      <h3>{propertyLabel(input.propertyType)} in {input.region}</h3>
      <dl>
        <div>
          <dt>Projekt</dt>
          <dd>{input.pvSize} kWp · {input.storageSize} kWh Speicher</dd>
        </div>
        <div>
          <dt>Dach</dt>
          <dd>{input.roofArea} m² · {orientationLabel(input.orientation)}</dd>
        </div>
        <div>
          <dt>Timing</dt>
          <dd>{timelineLabel(input.timeline)}</dd>
        </div>
        <div>
          <dt>Fit-Score</dt>
          <dd>{result.fitScore}/100</dd>
        </div>
        <div>
          <dt>Report-ID</dt>
          <dd>{reportId}</dd>
        </div>
      </dl>
      <p>
        Demo-Karte ohne Kontakt- oder CRM-Submit. In einer Kunden-Implementation kann hier ein CRM-Datensatz,
        Lead-Score und Routing entstehen.
      </p>
    </section>
  );
}

function ProcessMap() {
  const items = [
    {
      title: 'Käufer füllt aus',
      text: 'Geführter Flow statt offenes Kontaktformular.',
      status: 'aktiv in Demo',
      icon: <FileText />,
    },
    {
      title: 'Ergebnis entsteht',
      text: 'Berechnung, Empfehlung und PDF laufen lokal.',
      status: 'aktiv in Demo',
      icon: <BarChart3 />,
    },
    {
      title: 'CRM-Karte',
      text: 'Lead-Datensatz mit Score und Projektkontext.',
      status: 'nur erklärt',
      icon: <Building2 />,
    },
    {
      title: 'n8n-Routing',
      text: 'Rückruf, Termin oder Absage nach Regelwerk.',
      status: 'nur erklärt',
      icon: <Workflow />,
    },
    {
      title: 'Server-side Tracking',
      text: 'Events pro Schritt für sGTM, GA4 und CAPI.',
      status: 'nur erklärt',
      icon: <ShieldCheck />,
    },
  ];

  return (
    <div className="energy-demo-process" aria-label="Prozesskarte">
      {items.map((item, index) => (
        <article key={item.title}>
          <div className="energy-demo-process__index">{index + 1}</div>
          <div className="energy-demo-process__icon">{item.icon}</div>
          <h3>{item.title}</h3>
          <p>{item.text}</p>
          <span>{item.status}</span>
        </article>
      ))}
    </div>
  );
}

export default App;
