import {useEffect, useMemo, useRef, useState, type FormEvent} from 'react';

type Signal = 'green' | 'yellow' | 'red';

type FormState = {
  industry: string;
  offerType: string;
  employeeRange: string;
  country: string;
  plzRegion: string;
  offerFocus: string;
  averageOrderValueRange: string;
  adBudgetRange: string;
  websiteUrl: string;
  cms: string;
  pixelPresent: string;
  gtmPresent: string;
  consentMode: string;
  metaCapi: string;
  crmPresent: string;
  responseTime: string;
  responsible: string;
  targetRegion: string;
  expectedChannelMix: string;
  competition: string;
};

type Option = {
  value: string;
  label: string;
  note?: string;
};

type Question = {
  field: keyof FormState;
  stepIndex: number;
  label: string;
  detail: string;
  options?: Option[];
  placeholder?: string;
  hint?: string;
  optional?: boolean;
};

type ModuleResult = {
  label: string;
  signal: Signal;
  text: string;
};

type Evaluation = {
  signal: Signal;
  label: string;
  score: number;
  reasons: string[];
  nextStep: string;
  modules: ModuleResult[];
};

const initialForm: FormState = {
  industry: '',
  offerType: '',
  employeeRange: '',
  country: '',
  plzRegion: '',
  offerFocus: '',
  averageOrderValueRange: '',
  adBudgetRange: '',
  websiteUrl: '',
  cms: '',
  pixelPresent: '',
  gtmPresent: '',
  consentMode: '',
  metaCapi: '',
  crmPresent: '',
  responseTime: '',
  responsible: '',
  targetRegion: '',
  expectedChannelMix: '',
  competition: '',
};

const steps = [
  {
    title: 'Betrieb',
    purpose: 'ICP-Fit prüfen',
    focus: 'Branche, Angebotsart und Teamgröße zeigen, ob der Fall in den aktuellen Founding-Partner-Rahmen passt.',
    output: 'Fit-Rahmen',
  },
  {
    title: 'Region',
    purpose: 'Marktreichweite einschätzen',
    focus: 'Der Zielmarkt muss groß genug sein, ohne außerhalb des DACH-Rahmens zu laufen.',
    output: 'Marktraum',
  },
  {
    title: 'Angebot',
    purpose: 'Wirtschaftliche Relevanz prüfen',
    focus: 'Auftragswert und Hauptleistung entscheiden, ob ein Anfrage-System die Akquisekosten tragen kann.',
    output: 'Unit Economics',
  },
  {
    title: 'Werbebudget',
    purpose: 'Skalierungsfähigkeit prüfen',
    focus: 'Budget ist kein Kaufdruck, sondern ein Signal, ob belastbare Nachfrage überhaupt getestet werden kann.',
    output: 'Skalierungsfenster',
  },
  {
    title: 'Website',
    purpose: 'Anfragepfad einschätzen',
    focus: 'Die Website zeigt, wie konkret der spätere Befund werden kann. Eine grobe URL reicht.',
    output: 'Anfragepfad',
  },
  {
    title: 'Tracking',
    purpose: 'Messlage prüfen',
    focus: 'Ohne Messbarkeit wird Wachstum zur Meinung. Unklarheit ist erlaubt, wird aber bewusst rot markiert.',
    output: 'Datenreife',
  },
  {
    title: 'Anfrageprozess',
    purpose: 'Vertriebsfähigkeit prüfen',
    focus: 'Bezahlte Anfragen verlieren Wert, wenn Reaktion, Verantwortung oder Nachverfolgung fehlen.',
    output: 'Prozessreife',
  },
  {
    title: 'Marktbild',
    purpose: 'Leadkosten-Korridor einordnen',
    focus: 'Region, Kanal und Wettbewerb ergeben nur einen Korridor, keine Garantie.',
    output: 'Korridor',
  },
];

const industryOptions: Option[] = [
  {value: 'solar', label: 'Solar / PV', note: 'Kernfokus'},
  {value: 'heatpump', label: 'Wärmepumpe', note: 'Kernfokus'},
  {value: 'shk', label: 'SHK', note: 'Kernfokus'},
  {value: 'storage', label: 'Speicher / Energie', note: 'naher Fit'},
  {value: 'other', label: 'Andere Branche', note: 'meist kein Fit'},
];

const offerTypeOptions: Option[] = [
  {value: 'installation', label: 'Installation'},
  {value: 'planning_installation', label: 'Planung + Installation'},
  {value: 'maintenance', label: 'Wartung / Service'},
  {value: 'consulting', label: 'Beratung'},
];

const employeeOptions: Option[] = [
  {value: '1_5', label: '1-5', note: 'oft zu früh'},
  {value: '6_9', label: '6-9', note: 'Grenzfall'},
  {value: '10_25', label: '10-25', note: 'starker Fit'},
  {value: '26_50', label: '26-50', note: 'prüfbar'},
  {value: '50_plus', label: '50+', note: 'prüfbar'},
];

const countryOptions: Option[] = [
  {value: 'de', label: 'Deutschland'},
  {value: 'at', label: 'Österreich'},
  {value: 'ch', label: 'Schweiz'},
  {value: 'other', label: 'Außerhalb DACH'},
];

const offerFocusOptions: Option[] = [
  {value: 'pv', label: 'PV-Anlagen'},
  {value: 'heatpump', label: 'Wärmepumpen'},
  {value: 'shk', label: 'SHK-Projekte'},
  {value: 'storage', label: 'Speicher / Wallbox'},
  {value: 'mixed', label: 'Gemischtes Angebot'},
];

const orderValueOptions: Option[] = [
  {value: 'under_5k', label: '< 5.000 EUR', note: 'kritisch'},
  {value: '5k_10k', label: '5.000-10.000 EUR', note: 'prüfen'},
  {value: '10k_25k', label: '10.000-25.000 EUR', note: 'tragfähig'},
  {value: '25k_plus', label: '25.000 EUR+', note: 'stark'},
];

const budgetOptions: Option[] = [
  {value: 'under_2500', label: '< 2.500 EUR / Monat', note: 'kein Cohort-Fit'},
  {value: '2500_4999', label: '2.500-4.999 EUR / Monat', note: 'kein Cohort-Fit'},
  {value: '5000_9999', label: '5.000-9.999 EUR / Monat', note: 'Testfenster'},
  {value: '10000_19999', label: '10.000-19.999 EUR / Monat', note: 'solides Fenster'},
  {value: '20000_plus', label: '20.000 EUR+ / Monat', note: 'starkes Fenster'},
];

const cmsOptions: Option[] = [
  {value: 'wordpress', label: 'WordPress'},
  {value: 'shopify', label: 'Shopify'},
  {value: 'custom', label: 'Eigenbau / anderes CMS'},
  {value: 'unknown', label: 'Weiß ich nicht'},
  {value: 'none', label: 'Keine Website'},
];

const yesNoUnknownOptions: Option[] = [
  {value: 'yes', label: 'Ja'},
  {value: 'no', label: 'Nein'},
  {value: 'unknown', label: 'Weiß ich nicht'},
];

const responseTimeOptions: Option[] = [
  {value: 'under_1h', label: '< 1 Stunde'},
  {value: 'same_day', label: 'Am selben Tag'},
  {value: 'next_day', label: 'Nächster Werktag'},
  {value: 'later', label: 'Später'},
  {value: 'unknown', label: 'Weiß ich nicht'},
];

const responsibleOptions: Option[] = [
  {value: 'dedicated', label: 'Feste Person / Team'},
  {value: 'shared', label: 'Geteilt im Team'},
  {value: 'none', label: 'Nicht klar geregelt'},
  {value: 'unknown', label: 'Weiß ich nicht'},
];

const targetRegionOptions: Option[] = [
  {value: 'local', label: 'Lokal / Stadt'},
  {value: 'regional', label: 'Region / Bundesland'},
  {value: 'dach', label: 'DACH-weit'},
  {value: 'unclear', label: 'Noch unklar'},
];

const channelOptions: Option[] = [
  {value: 'search', label: 'Google / SEO'},
  {value: 'paid', label: 'Ads'},
  {value: 'referral', label: 'Empfehlungen'},
  {value: 'mixed', label: 'Gemischt'},
  {value: 'unknown', label: 'Weiß ich nicht'},
];

const competitionOptions: Option[] = [
  {value: 'low', label: 'Niedrig'},
  {value: 'medium', label: 'Mittel'},
  {value: 'high', label: 'Hoch'},
  {value: 'unknown', label: 'Weiß ich nicht'},
];

const questions: Question[] = [
  {
    field: 'industry',
    stepIndex: 0,
    label: 'In welcher Branche arbeitet der Betrieb hauptsächlich?',
    detail: 'Das ist der wichtigste Fit-Filter für die Founding-Cohort.',
    options: industryOptions,
  },
  {
    field: 'offerType',
    stepIndex: 0,
    label: 'Welche Angebotsart beschreibt den Betrieb am besten?',
    detail: 'Je klarer das Angebot, desto sauberer lässt sich der Anfragepfad bewerten.',
    options: offerTypeOptions,
  },
  {
    field: 'employeeRange',
    stepIndex: 0,
    label: 'Wie groß ist das Team ungefähr?',
    detail: 'Zu klein ist oft zu früh. Zu groß braucht meist andere Systemgrenzen.',
    options: employeeOptions,
  },
  {
    field: 'country',
    stepIndex: 1,
    label: 'In welchem Markt soll die Nachfrage entstehen?',
    detail: 'Der aktuelle Analyse-Rahmen ist auf DACH ausgelegt.',
    options: countryOptions,
  },
  {
    field: 'plzRegion',
    stepIndex: 1,
    label: 'Welche grobe PLZ-Region oder Stadt ist relevant?',
    detail: 'Keine Adresse. Nur der Marktraum, damit der Korridor nicht beliebig wird.',
    placeholder: 'z. B. 30, Hannover, Region Süd',
  },
  {
    field: 'offerFocus',
    stepIndex: 2,
    label: 'Welche Leistung soll primär Anfragen erzeugen?',
    detail: 'Ein Anfrage-System braucht einen klaren wirtschaftlichen Fokus.',
    options: offerFocusOptions,
  },
  {
    field: 'averageOrderValueRange',
    stepIndex: 2,
    label: 'Wie hoch ist der durchschnittliche Auftragswert?',
    detail: 'Der Auftragswert entscheidet, ob bezahlte Nachfrage wirtschaftlich tragfähig sein kann.',
    options: orderValueOptions,
  },
  {
    field: 'adBudgetRange',
    stepIndex: 3,
    label: 'Welches monatliche Werbebudget ist realistisch?',
    detail: 'Unter 5.000 EUR pro Monat wird eine belastbare Umsetzungsempfehlung meist zu dünn.',
    options: budgetOptions,
  },
  {
    field: 'websiteUrl',
    stepIndex: 4,
    label: 'Welche Website soll grob geprüft werden?',
    detail: 'Optional. Ohne URL bleibt die Befundtiefe bewusst eingeschränkt.',
    placeholder: 'beispiel.de',
    optional: true,
  },
  {
    field: 'cms',
    stepIndex: 4,
    label: 'Welches Website-System wird genutzt?',
    detail: 'Das ist nur eine Selbstauskunft für den späteren Anfragepfad.',
    options: cmsOptions,
  },
  {
    field: 'pixelPresent',
    stepIndex: 5,
    label: 'Ist ein Werbe-Pixel sichtbar oder bekannt?',
    detail: 'Unklar ist okay, wird aber als Messrisiko bewertet.',
    options: yesNoUnknownOptions,
  },
  {
    field: 'gtmPresent',
    stepIndex: 5,
    label: 'Ist Google Tag Manager vorhanden?',
    detail: 'GTM ist kein Muss, macht saubere Messung aber deutlich einfacher.',
    options: yesNoUnknownOptions,
  },
  {
    field: 'consentMode',
    stepIndex: 5,
    label: 'Ist Consent Mode bekannt oder eingerichtet?',
    detail: 'Consent entscheidet, ob spätere Messung rechtlich und technisch tragfähig wird.',
    options: yesNoUnknownOptions,
  },
  {
    field: 'metaCapi',
    stepIndex: 5,
    label: 'Ist Meta CAPI bekannt oder eingerichtet?',
    detail: 'Nur als Reifegrad-Signal. Noch keine technische Integration.',
    options: yesNoUnknownOptions,
  },
  {
    field: 'responseTime',
    stepIndex: 6,
    label: 'Wie schnell werden neue Anfragen beantwortet?',
    detail: 'Bezahlte Anfragen verlieren schnell Wert, wenn die Reaktion zu langsam ist.',
    options: responseTimeOptions,
  },
  {
    field: 'responsible',
    stepIndex: 6,
    label: 'Wer ist für neue Anfragen verantwortlich?',
    detail: 'Ohne klare Verantwortung wird ein Funnel schnell zum Leck.',
    options: responsibleOptions,
  },
  {
    field: 'crmPresent',
    stepIndex: 6,
    label: 'Gibt es eine CRM- oder Nachverfolgungsstruktur?',
    detail: 'Hier geht es nur darum, ob Nachverfolgung heute schon verbindlich existiert.',
    options: yesNoUnknownOptions,
  },
  {
    field: 'targetRegion',
    stepIndex: 7,
    label: 'Wie groß ist die gewünschte Zielregion?',
    detail: 'Das hilft, den Leadkosten-Korridor realistisch zu lesen.',
    options: targetRegionOptions,
  },
  {
    field: 'expectedChannelMix',
    stepIndex: 7,
    label: 'Welcher Kanal bringt heute am ehesten Nachfrage?',
    detail: 'Der Kanal ist ein Marktbild-Signal, keine Media-Planung.',
    options: channelOptions,
  },
  {
    field: 'competition',
    stepIndex: 7,
    label: 'Wie fühlt sich der Wettbewerb in der Region an?',
    detail: 'Hoher Wettbewerb ist kein Ausschluss, aber ein Risiko für den Korridor.',
    options: competitionOptions,
  },
];

const signalLabels: Record<Signal, string> = {
  green: 'Grün',
  yellow: 'Gelb',
  red: 'Rot',
};

const autoAdvanceDelayMs = 180;

export function App() {
  const [form, setForm] = useState<FormState>(initialForm);
  const [questionIndex, setQuestionIndex] = useState(0);
  const [errors, setErrors] = useState<string[]>([]);
  const [showResult, setShowResult] = useState(false);
  const advanceTimer = useRef<number | null>(null);

  const evaluation = useMemo(() => evaluateFit(form), [form]);
  const currentQuestion = questions[questionIndex];
  const currentStep = steps[currentQuestion.stepIndex];
  const currentValue = form[currentQuestion.field].trim();
  const progressPercent = showResult ? 100 : Math.round(((questionIndex + 1) / questions.length) * 100);
  const activeStepIndexes = new Set(questions.slice(0, questionIndex + 1).map((question) => question.stepIndex));
  const needsManualContinue = !currentQuestion.options || questionIndex === questions.length - 1;
  const canContinue = currentQuestion.optional || currentValue !== '';
  const nextButtonLabel = questionIndex === questions.length - 1
    ? 'Lokales Ergebnis anzeigen'
    : currentQuestion.optional && currentValue === ''
      ? 'Überspringen'
      : 'Weiter';

  useEffect(() => () => clearAdvanceTimer(), []);

  function clearAdvanceTimer() {
    if (null === advanceTimer.current) {
      return;
    }

    window.clearTimeout(advanceTimer.current);
    advanceTimer.current = null;
  }

  function updateField(field: keyof FormState, value: string) {
    clearAdvanceTimer();
    setForm((current) => ({
      ...current,
      [field]: value,
    }));
    setErrors([]);
  }

  function handleQuestionChange(value: string) {
    updateField(currentQuestion.field, value);

    if (!currentQuestion.options || questionIndex >= questions.length - 1) {
      return;
    }

    advanceTimer.current = window.setTimeout(function () {
      advanceTimer.current = null;
      setQuestionIndex((current) => current === questionIndex ? current + 1 : current);
      setErrors([]);
    }, autoAdvanceDelayMs);
  }

  function handleNext() {
    clearAdvanceTimer();
    const nextErrors = validateQuestion(currentQuestion, form);

    if (nextErrors.length > 0) {
      setErrors(nextErrors);
      return;
    }

    if (questionIndex < questions.length - 1) {
      setQuestionIndex((current) => current + 1);
      setErrors([]);
      return;
    }

    const finalErrors = validateAllQuestions(form);

    if (finalErrors.length > 0) {
      const firstInvalidIndex = questions.findIndex((question) => validateQuestion(question, form).length > 0);
      setQuestionIndex(firstInvalidIndex >= 0 ? firstInvalidIndex : questionIndex);
      setErrors(finalErrors.slice(0, 2));
      return;
    }

    setShowResult(true);
    setErrors([]);
  }

  function handleBack() {
    clearAdvanceTimer();

    if (showResult) {
      setShowResult(false);
      return;
    }

    setQuestionIndex((current) => Math.max(0, current - 1));
    setErrors([]);
  }

  function handleReset() {
    clearAdvanceTimer();
    setForm(initialForm);
    setQuestionIndex(0);
    setErrors([]);
    setShowResult(false);
  }

  return (
    <main className="readiness-shell" aria-labelledby="readiness-title" data-track-section="request_analysis_react_form">
      <section className="readiness-flow" aria-label="System-Diagnose">
        <div className="readiness-flow-top">
          <div>
            <div className="readiness-kicker">System-Diagnose</div>
            <h1 id="readiness-title">Fit-Check</h1>
          </div>
          <div className="readiness-flow-status" aria-label="Fortschritt">
            <span>{showResult ? 'Ergebnis' : `Frage ${questionIndex + 1}`}</span>
            <strong>{showResult ? '100%' : `${progressPercent}%`}</strong>
          </div>
        </div>

        <div className="readiness-progress-meter" aria-hidden="true">
          <span style={{width: `${progressPercent}%`}} />
        </div>

        <ol className="readiness-chapter-track" aria-label="Diagnosekapitel">
          {steps.map((step, index) => (
            <li
              key={step.title}
              className={
                showResult || index < currentQuestion.stepIndex
                  ? 'is-done'
                  : activeStepIndexes.has(index)
                    ? 'is-active'
                    : ''
              }
            >
              <span>{String(index + 1).padStart(2, '0')}</span>
              <strong>{step.title}</strong>
            </li>
          ))}
        </ol>

        {showResult ? (
          <ResultView evaluation={evaluation} form={form} onBack={handleBack} onReset={handleReset} />
        ) : (
          <section className="readiness-panel readiness-flow-card" aria-labelledby="readiness-question-title">
            <div className="readiness-step-head">
              <span>{currentStep.purpose}</span>
              <strong>{currentStep.title}</strong>
            </div>

            <div className="readiness-question-stage">
              <div className="readiness-question-count">
                <span>{String(questionIndex + 1).padStart(2, '0')}</span>
                <span>{questions.length}</span>
              </div>

              <div className="readiness-question-copy">
                <h2 id="readiness-question-title">{currentQuestion.label}</h2>
                <p>{currentQuestion.detail}</p>
              </div>

              <div className={`readiness-error-slot ${errors.length > 0 ? 'has-errors' : ''}`} role="alert" aria-live="polite">
                {errors.map((error) => (
                  <p key={error}>{error}</p>
                ))}
              </div>

              <QuestionInput question={currentQuestion} value={form[currentQuestion.field]} onChange={handleQuestionChange} onContinue={handleNext} />
            </div>

            <div className={`readiness-actions ${needsManualContinue ? '' : 'readiness-actions--auto'}`}>
              {questionIndex > 0 ? (
                <button type="button" className="readiness-button readiness-button--secondary" onClick={handleBack}>
                  Zurück
                </button>
              ) : (
                <span className="readiness-actions__spacer" aria-hidden="true" />
              )}

              {needsManualContinue ? (
                <button
                  type="button"
                  className="readiness-button readiness-button--primary"
                  onClick={handleNext}
                  disabled={!canContinue}
                  data-track-action={`request_analysis_question_${questionIndex + 1}_completed`}
                  data-track-category="lead_funnel"
                  data-track-section="request_analysis"
                  data-track-funnel-stage={`request_analysis_question_${questionIndex + 1}`}
                >
                  {nextButtonLabel}
                </button>
              ) : (
                <span className="readiness-auto-hint">Antwort wählen, dann geht es automatisch weiter.</span>
              )}
            </div>
          </section>
        )}

        {!showResult && <p className="readiness-flow-note">Bis zum Kontakt-Schritt bleibt die Ampel lokal. Erst nach Einwilligung werden Ergebnis und Kontaktdaten gespeichert.</p>}
      </section>
    </main>
  );
}

function QuestionInput({
  question,
  value,
  onChange,
  onContinue,
}: {
  question: Question;
  value: string;
  onChange: (value: string) => void;
  onContinue: () => void;
}) {
  if (!question.options) {
    return <TextField label="Antwort" value={value} placeholder={question.placeholder} hint={question.hint} onChange={onChange} onEnter={onContinue} />;
  }

  return (
    <fieldset className="readiness-fieldset" aria-label={question.label}>
      <div className="readiness-options">
        {question.options.map((option) => (
          <button
            key={option.value}
            type="button"
            className={value === option.value ? 'is-selected' : ''}
            onClick={() => onChange(option.value)}
            aria-pressed={value === option.value}
          >
            <span>{option.label}</span>
            {option.note && <small>{option.note}</small>}
          </button>
        ))}
      </div>
    </fieldset>
  );
}

function TextField({
  label,
  value,
  placeholder,
  hint,
  onChange,
  onEnter,
}: {
  label: string;
  value: string;
  placeholder?: string;
  hint?: string;
  onChange: (value: string) => void;
  onEnter: () => void;
}) {
  const inputId = label.toLowerCase().replace(/[^a-z0-9]+/g, '-');

  return (
    <label className="readiness-text-field" htmlFor={inputId}>
      <span>{label}</span>
      {hint && <small>{hint}</small>}
      <input
        id={inputId}
        type="text"
        value={value}
        placeholder={placeholder}
        onChange={(event) => onChange(event.currentTarget.value)}
        onKeyDown={(event) => {
          if (event.key !== 'Enter') {
            return;
          }

          event.preventDefault();
          onEnter();
        }}
      />
    </label>
  );
}

function ResultView({evaluation, form, onBack, onReset}: {evaluation: Evaluation; form: FormState; onBack: () => void; onReset: () => void}) {
  const actionPlan = getActionPlan(evaluation.signal);

  return (
    <section className="readiness-result" aria-labelledby="readiness-result-title" data-track-section="request_analysis_result">
      <div className={`readiness-result-head readiness-result-head--${evaluation.signal}`}>
        <span>Lokales Ergebnis</span>
        <h2 id="readiness-result-title">{evaluation.label}</h2>
        <p>{evaluation.nextStep}</p>
      </div>

      <div className="readiness-result-summary" aria-label="Ergebniszusammenfassung">
        <div>
          <span>Ampel</span>
          <strong>{signalLabels[evaluation.signal]}</strong>
        </div>
        <div>
          <span>Punktzahl</span>
          <strong>{evaluation.score} / 100</strong>
        </div>
        <div>
          <span>Empfehlung</span>
          <strong>{actionPlan.shortLabel}</strong>
        </div>
      </div>

      <div className="readiness-result-grid">
        <div className="readiness-score">
          <span className={`readiness-signal readiness-signal--${evaluation.signal}`}>{signalLabels[evaluation.signal]}</span>
          <strong>{evaluation.score} / 100</strong>
          <p>Punktzahl aus den acht Formularschritten. Leadkosten bleiben ein Korridor, keine Garantie.</p>
        </div>

        <div className="readiness-reasons">
          <h3>Begründung</h3>
          <ul>
            {evaluation.reasons.map((reason) => (
              <li key={reason}>{reason}</li>
            ))}
          </ul>
        </div>
      </div>

      <div className="readiness-modules" aria-label="Einzelsignale">
        {evaluation.modules.map((module) => (
          <article key={module.label} className={`readiness-module readiness-module--${module.signal}`}>
            <span>{signalLabels[module.signal]}</span>
            <h3>{module.label}</h3>
            <p>{module.text}</p>
          </article>
        ))}
      </div>

      <div className="readiness-action-plan">
        <div>
          <span>Nächster sinnvoller Schritt</span>
          <h3>{actionPlan.title}</h3>
          <p>{actionPlan.text}</p>
        </div>
        <ol>
          {actionPlan.items.map((item) => (
            <li key={item}>{item}</li>
          ))}
        </ol>
      </div>

      <ConversionBlock evaluation={evaluation} form={form} onBack={onBack} onReset={onReset} />
    </section>
  );
}

type AnalysisConfig = {
  submitUrl?: string;
  submitEnabled?: boolean;
  calcomUrl?: string;
  contactUrl?: string;
  e3Stats?: Record<string, string>;
};

function getAnalysisConfig(): AnalysisConfig {
  if (typeof window === 'undefined') {
    return {};
  }
  return ((window as unknown as {HU_ANALYSIS_CONFIG?: AnalysisConfig}).HU_ANALYSIS_CONFIG) || {};
}

function readQueryParam(name: string): string {
  if (typeof window === 'undefined') {
    return '';
  }
  try {
    return new URLSearchParams(window.location.search).get(name) || '';
  } catch (e) {
    return '';
  }
}

function getSignalCta(signal: Signal) {
  if (signal === 'green') {
    return {
      headline: 'Letzter Schritt — Termin sichern',
      lead: 'Ihr Fit ist tragfähig. Buchen Sie einen 30-Min-Slot — ich bereite das Gespräch mit Ihren Angaben vor.',
      primaryLabel: 'Termin buchen',
      secondaryLabel: 'Lieber per Mail',
    };
  }

  if (signal === 'yellow') {
    return {
      headline: 'Letzter Schritt — Lücken durchsprechen',
      lead: 'Es gibt klärbare Signale. 30 Minuten reichen, um zu entscheiden, was vor einer Umsetzung steht.',
      primaryLabel: 'Termin buchen — Lücken durchsprechen',
      secondaryLabel: 'Per Mail klären',
    };
  }

  return {
    headline: 'Letzter Schritt — kostenfreies Erstgespräch',
    lead: 'Eine Umsetzung wäre jetzt zu früh. In 30 Minuten klären wir, was zuerst steht — ohne Pitch.',
    primaryLabel: 'Erstgespräch — kostenfrei',
    secondaryLabel: 'Per Mail melden',
  };
}

const FIELD_LABELS: Record<keyof FormState, string> = {
  industry: 'Branche',
  offerType: 'Angebotsart',
  employeeRange: 'Teamgröße',
  country: 'Land',
  plzRegion: 'PLZ / Region',
  offerFocus: 'Hauptleistung',
  averageOrderValueRange: 'Auftragswert',
  adBudgetRange: 'Werbebudget / Monat',
  websiteUrl: 'Website',
  cms: 'CMS',
  pixelPresent: 'Werbepixel',
  gtmPresent: 'Google Tag Manager',
  consentMode: 'Consent Mode',
  metaCapi: 'Meta CAPI',
  crmPresent: 'CRM',
  responseTime: 'Antwortzeit',
  responsible: 'Verantwortlich',
  targetRegion: 'Zielregion',
  expectedChannelMix: 'Hauptkanal',
  competition: 'Wettbewerb',
};

function answersAsLabels(form: FormState): Record<string, string> {
  const result: Record<string, string> = {};
  (Object.keys(FIELD_LABELS) as (keyof FormState)[]).forEach((field) => {
    const raw = form[field];
    if (!raw) {
      return;
    }
    const question = questions.find((q) => q.field === field);
    const option = question?.options?.find((o) => o.value === raw);
    result[field] = option ? option.label : raw;
  });
  return result;
}

function ConversionBlock({evaluation, form, onBack, onReset}: {evaluation: Evaluation; form: FormState; onBack: () => void; onReset: () => void}) {
  const config = getAnalysisConfig();
  const signalCta = getSignalCta(evaluation.signal);
  const submitEnabled = config.submitEnabled !== false && Boolean(config.submitUrl);
  const calcomFallback = config.calcomUrl || 'https://cal.com/hasim-uener/30min?overlayCalendar=true';
  const contactFallback = config.contactUrl || '/kontakt/';
  const e3Stats = config.e3Stats || {};

  const [name, setName] = useState('');
  const [company, setCompany] = useState('');
  const [email, setEmail] = useState('');
  const [consent, setConsent] = useState(false);
  const [honeypot, setHoneypot] = useState('');
  const [status, setStatus] = useState<'idle' | 'submitting' | 'success' | 'error'>('idle');
  const [errorMessage, setErrorMessage] = useState('');
  const [calcomUrl, setCalcomUrl] = useState(calcomFallback);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setErrorMessage('');

    if (!submitEnabled || !config.submitUrl) {
      setStatus('success');
      setCalcomUrl(calcomFallback);
      return;
    }

    if (!name.trim() || !company.trim() || !email.trim() || !consent) {
      setErrorMessage('Bitte Name, Firma, E-Mail und Einwilligung ausfüllen.');
      return;
    }

    setStatus('submitting');

    const payload = {
      name: name.trim(),
      company: company.trim(),
      email: email.trim(),
      consent: true,
      signal: evaluation.signal,
      score: evaluation.score,
      reasons: evaluation.reasons,
      action_plan_label: getActionPlan(evaluation.signal).shortLabel,
      answers: answersAsLabels(form),
      company_website: honeypot,
      ads_source: readQueryParam('ads_source') || readQueryParam('utm_source'),
      ads_keyword: readQueryParam('ads_keyword') || readQueryParam('utm_term'),
      utm_medium: readQueryParam('utm_medium'),
      utm_campaign: readQueryParam('utm_campaign'),
      gclid: readQueryParam('gclid'),
    };

    try {
      const response = await fetch(config.submitUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload),
      });
      const data = await response.json().catch(() => ({}));
      if (!response.ok || !data?.ok) {
        throw new Error(data?.error || 'Etwas ist schiefgelaufen. Bitte erneut versuchen.');
      }
      setCalcomUrl(typeof data.calcomUrl === 'string' && data.calcomUrl ? data.calcomUrl : calcomFallback);
      setStatus('success');
    } catch (e) {
      const message = e instanceof Error ? e.message : 'Bitte später erneut versuchen.';
      setErrorMessage(message);
      setStatus('error');
    }
  }

  return (
    <div className="readiness-conversion" data-track-section="request_analysis_conversion">
      {Object.keys(e3Stats).length > 0 && (
        <div className="readiness-proof" aria-label="Referenzergebnisse">
          <span>Referenz · E3 New Energy</span>
          <ul>
            {e3Stats.lead_count && <li><strong>{e3Stats.lead_count}</strong>Anfragen / 9 Monate</li>}
            {e3Stats.sales_conversion && <li><strong>{e3Stats.sales_conversion}</strong>Abschlussquote</li>}
            {e3Stats.cpl_reduction && <li><strong>{e3Stats.cpl_reduction}</strong>Kosten / Anfrage</li>}
          </ul>
        </div>
      )}

      {status !== 'success' ? (
        <form className="readiness-contact" onSubmit={handleSubmit} data-track-section="request_analysis_contact_form">
          <div className="readiness-contact-head">
            <span>Schritt 9</span>
            <h3>{signalCta.headline}</h3>
            <p>{signalCta.lead}</p>
          </div>
          <div className="readiness-contact-fields">
            <label>
              <span>Name</span>
              <input
                type="text"
                value={name}
                onChange={(e) => setName(e.target.value)}
                autoComplete="name"
                required
                disabled={status === 'submitting'}
              />
            </label>
            <label>
              <span>Firma</span>
              <input
                type="text"
                value={company}
                onChange={(e) => setCompany(e.target.value)}
                autoComplete="organization"
                required
                disabled={status === 'submitting'}
              />
            </label>
            <label>
              <span>E-Mail</span>
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                autoComplete="email"
                required
                disabled={status === 'submitting'}
              />
            </label>
          </div>
          <label className="readiness-contact-honeypot" aria-hidden="true">
            Firma (Webadresse, leer lassen)
            <input
              type="text"
              tabIndex={-1}
              autoComplete="off"
              value={honeypot}
              onChange={(e) => setHoneypot(e.target.value)}
            />
          </label>
          <label className="readiness-contact-consent">
            <input
              type="checkbox"
              checked={consent}
              onChange={(e) => setConsent(e.target.checked)}
              disabled={status === 'submitting'}
              required
            />
            <span>Ich willige in die Verarbeitung meiner Angaben zur Vorbereitung des Gesprächs ein. Keine Newsletter, kein Verkauf an Dritte.</span>
          </label>
          {errorMessage && (
            <div className="readiness-contact-error" role="alert">{errorMessage}</div>
          )}
          <div className="readiness-contact-actions">
            <button type="button" className="readiness-button readiness-button--secondary" onClick={onBack}>
              Angaben bearbeiten
            </button>
            <button
              type="submit"
              className="readiness-button readiness-button--primary"
              disabled={status === 'submitting'}
              data-track-action="request_analysis_contact_submitted"
              data-track-category="lead_funnel"
              data-track-section="request_analysis_contact_form"
              data-track-funnel-stage="request_analysis_contact_submit"
            >
              {status === 'submitting' ? 'Wird gesendet …' : 'Ergebnis sichern & weiter'}
            </button>
          </div>
        </form>
      ) : (
        <div className={`readiness-final-cta readiness-final-cta--${evaluation.signal}`} data-track-section="request_analysis_final_cta">
          <div className="readiness-final-cta-head">
            <span>Eingegangen — danke, {name || 'Sie'}.</span>
            <h3>{signalCta.headline}</h3>
            <p>{signalCta.lead}</p>
          </div>
          <div className="readiness-final-cta-actions">
            <a
              href={calcomUrl}
              className="readiness-button readiness-button--primary"
              target="_blank"
              rel="noopener"
              data-track-action="request_analysis_cta_termin"
              data-track-category="lead_funnel"
              data-track-section="request_analysis_final_cta"
              data-track-funnel-stage="request_analysis_cta_termin"
            >
              {signalCta.primaryLabel}
            </a>
            <a
              href={contactFallback}
              className="readiness-button readiness-button--secondary"
              data-track-action="request_analysis_cta_mail"
              data-track-category="lead_funnel"
              data-track-section="request_analysis_final_cta"
              data-track-funnel-stage="request_analysis_cta_mail"
            >
              {signalCta.secondaryLabel}
            </a>
          </div>
          <div className="readiness-final-cta-foot">
            <button type="button" className="readiness-link" onClick={onReset}>
              Neu starten
            </button>
          </div>
        </div>
      )}
    </div>
  );
}

function validateQuestion(question: Question, form: FormState): string[] {
  const errors: string[] = [];
  const value = form[question.field].trim();

  if (!question.optional) {
    requireField(errors, value, question.options ? 'Bitte eine Antwort auswählen.' : 'Bitte das Feld ausfüllen.');
  }

  if (question.field === 'websiteUrl' && value && !isLikelyWebsite(value)) {
    errors.push('Bitte Website als Domain oder URL eintragen, z. B. beispiel.de.');
  }

  return errors;
}

function validateAllQuestions(form: FormState): string[] {
  return questions.flatMap((question) => validateQuestion(question, form));
}

function requireField(errors: string[], value: string, message: string) {
  if (!value) {
    errors.push(message);
  }
}

function isLikelyWebsite(value: string) {
  const normalized = value.trim().replace(/^https?:\/\//i, '').replace(/^www\./i, '');
  return /^[a-z0-9-]+(\.[a-z0-9-]+)+(\/.*)?$/i.test(normalized);
}

function evaluateFit(form: FormState): Evaluation {
  let score = 0;
  const hardStops: string[] = [];
  const reasons: string[] = [];
  const modules: ModuleResult[] = [];

  if (['solar', 'heatpump', 'shk'].includes(form.industry)) {
    score += 16;
    reasons.push('Branche passt zum Anfrage-System-Fokus.');
  } else if (form.industry === 'storage') {
    score += 10;
    reasons.push('Energie-nahes Angebot, aber ICP-Fit muss enger geprüft werden.');
  } else {
    hardStops.push('Branche liegt außerhalb des aktuellen Solar-/SHK-/Wärmepumpen-Fokus.');
  }

  if (form.employeeRange === '10_25') {
    score += 8;
  } else if (['6_9', '26_50', '50_plus'].includes(form.employeeRange)) {
    score += 5;
  } else if (form.employeeRange === '1_5') {
    hardStops.push('Sehr kleiner Betrieb: Umsetzung und Anfragevolumen können zu früh sein.');
  }

  modules.push(buildModule('ICP-Fit', hardStops.length > 0 ? 'red' : score >= 21 ? 'green' : 'yellow', resolveIcpText(form)));

  if (['de', 'at', 'ch'].includes(form.country)) {
    score += 10;
  } else if (form.country === 'other') {
    hardStops.push('Außerhalb DACH ist für v1 keine Empfehlung vorgesehen.');
  }

  if (form.plzRegion.trim()) {
    score += 4;
  }

  if (form.averageOrderValueRange === 'under_5k') {
    hardStops.push('Auftragswert unter 5.000 EUR senkt die Wirtschaftlichkeit deutlich.');
  } else if (form.averageOrderValueRange === '5k_10k') {
    score += 7;
    reasons.push('Auftragswert ist möglich, braucht aber genaue Marge und Prozessklarheit.');
  } else if (['10k_25k', '25k_plus'].includes(form.averageOrderValueRange)) {
    score += 12;
    reasons.push('Auftragswert kann bezahlte Nachfrage wirtschaftlich tragen.');
  }

  if (['under_2500', '2500_4999'].includes(form.adBudgetRange)) {
    hardStops.push('Werbebudget unter 5.000 EUR pro Monat blockiert die Founding-Cohort-Empfehlung.');
  } else if (form.adBudgetRange === '5000_9999') {
    score += 10;
  } else if (['10000_19999', '20000_plus'].includes(form.adBudgetRange)) {
    score += 16;
  }

  modules.push(buildModule('Wirtschaftlichkeit', hasEconomicStop(form) ? 'red' : score >= 48 ? 'green' : 'yellow', resolveEconomicsText(form)));

  if (form.websiteUrl.trim()) {
    score += 6;
    reasons.push('Website-URL liegt vor; der Anfragepfad kann konkreter bewertet werden.');
  } else {
    score += 2;
    reasons.push('Keine Website-URL: Ergebnis bleibt bewusst eingeschränkt.');
  }

  if (form.cms === 'wordpress') {
    score += 4;
  } else if (['shopify', 'custom'].includes(form.cms)) {
    score += 2;
  }

  modules.push(buildModule('Anfragepfad', form.websiteUrl.trim() ? 'green' : 'yellow', form.websiteUrl.trim() ? 'Website ist prüfbar.' : 'Ohne URL bleibt der Befund grober.'));

  const trackingValues = [form.pixelPresent, form.gtmPresent, form.consentMode, form.metaCapi];
  const trackingYes = trackingValues.filter((value) => value === 'yes').length;
  const trackingUnknownOrNo = trackingValues.filter((value) => value === 'unknown' || value === 'no').length;
  score += trackingYes * 2;

  if (trackingUnknownOrNo > 0) {
    reasons.push('Tracking- oder Consent-Lage ist unklar und wird rot bewertet.');
  } else {
    reasons.push('Tracking-Selbstauskunft wirkt belastbar.');
  }

  modules.push(
    buildModule(
      'Messbarkeit',
      trackingUnknownOrNo === 0 ? 'green' : trackingUnknownOrNo >= 3 ? 'red' : 'yellow',
      trackingUnknownOrNo === 0 ? 'Pixel, GTM, Consent und CAPI sind laut Selbstauskunft bekannt.' : 'Unklare Tracking-Antworten müssen vor Skalierung geklärt werden.',
    ),
  );

  if (['under_1h', 'same_day'].includes(form.responseTime)) {
    score += 6;
  } else if (form.responseTime === 'next_day') {
    score += 3;
  } else if (['later', 'unknown'].includes(form.responseTime)) {
    hardStops.push('Langsame oder unklare Reaktionszeit gefährdet bezahlte Anfragen.');
  }

  if (form.responsible === 'dedicated') {
    score += 5;
  } else if (form.responsible === 'shared') {
    score += 3;
  } else if (['none', 'unknown'].includes(form.responsible)) {
    hardStops.push('Kein klarer Verantwortlicher für neue Anfragen.');
  }

  if (form.crmPresent === 'yes') {
    score += 5;
  } else if (['no', 'unknown'].includes(form.crmPresent)) {
    reasons.push('CRM-Lage ist rot: Nachverfolgung wäre in v1 ein Risiko.');
  }

  modules.push(buildModule('Anfrageprozess', resolveProcessSignal(form), resolveProcessText(form)));

  if (form.targetRegion === 'regional') {
    score += 6;
  } else if (['local', 'dach'].includes(form.targetRegion)) {
    score += 4;
  } else if (form.targetRegion === 'unclear') {
    score += 1;
  }

  if (form.expectedChannelMix === 'mixed') {
    score += 5;
  } else if (['search', 'paid', 'referral'].includes(form.expectedChannelMix)) {
    score += 3;
  }

  if (form.competition === 'medium') {
    score += 5;
  } else if (form.competition === 'low') {
    score += 4;
  } else if (form.competition === 'high') {
    score += 2;
    reasons.push('Hoher Wettbewerb spricht für vorsichtigen Leadkosten-Korridor.');
  } else if (form.competition === 'unknown') {
    score += 1;
  }

  modules.push(buildModule('Marktbild', resolveMarketSignal(form), resolveMarketText(form)));

  const finalScore = Math.max(0, Math.min(100, score));
  const signal = resolveOverallSignal(finalScore, hardStops);
  const label = resolveResultLabel(signal);
  const nextStep = resolveNextStep(signal);
  const finalReasons = [...hardStops, ...reasons].slice(0, 6);

  return {
    signal,
    label,
    score: finalScore,
    reasons: finalReasons.length > 0 ? finalReasons : ['Die Angaben reichen für eine erste lokale Einordnung.'],
    nextStep,
    modules,
  };
}

function buildModule(label: string, signal: Signal, text: string): ModuleResult {
  return {label, signal, text};
}

function hasEconomicStop(form: FormState) {
  return form.averageOrderValueRange === 'under_5k' || ['under_2500', '2500_4999'].includes(form.adBudgetRange);
}

function resolveIcpText(form: FormState) {
  if (['solar', 'heatpump', 'shk'].includes(form.industry) && form.employeeRange === '10_25') {
    return 'Branche und Betriebsgröße passen sehr gut zum Founding-Partner-Rahmen.';
  }

  if (form.industry === 'storage') {
    return 'Energie-naher Fit, aber die Angebotslogik muss enger bewertet werden.';
  }

  return 'Fit ist nicht eindeutig genug für den aktuellen Fokus.';
}

function resolveEconomicsText(form: FormState) {
  if (hasEconomicStop(form)) {
    return 'Budget oder Auftragswert liegt unter der Schwelle für eine belastbare Umsetzungsempfehlung.';
  }

  if (form.adBudgetRange === '5000_9999') {
    return 'Wirtschaftlichkeit ist möglich, braucht aber saubere Priorisierung.';
  }

  return 'Budget und Auftragswert lassen einen sinnvollen Anfragepfad zu.';
}

function resolveProcessSignal(form: FormState): Signal {
  if (['later', 'unknown'].includes(form.responseTime) || ['none', 'unknown'].includes(form.responsible)) {
    return 'red';
  }

  if (form.crmPresent !== 'yes' || form.responseTime === 'next_day' || form.responsible === 'shared') {
    return 'yellow';
  }

  return 'green';
}

function resolveProcessText(form: FormState) {
  if (['later', 'unknown'].includes(form.responseTime) || ['none', 'unknown'].includes(form.responsible)) {
    return 'Prozessreife ist aktuell der größte Engpass.';
  }

  if (form.crmPresent !== 'yes') {
    return 'Anfragen können starten, aber Nachverfolgung muss vor Skalierung stabil werden.';
  }

  return 'Reaktionszeit, Verantwortung und CRM wirken anschlussfähig.';
}

function resolveMarketSignal(form: FormState): Signal {
  if (form.targetRegion === 'unclear' || form.expectedChannelMix === 'unknown') {
    return 'yellow';
  }

  if (form.competition === 'high' && ['under_2500', '2500_4999', '5000_9999'].includes(form.adBudgetRange)) {
    return 'yellow';
  }

  return 'green';
}

function resolveMarketText(form: FormState) {
  if (form.targetRegion === 'unclear' || form.expectedChannelMix === 'unknown') {
    return 'Zielmarkt oder Kanalbild ist noch zu unscharf für eine harte Prognose.';
  }

  if (form.competition === 'high') {
    return 'Markt ist kompetitiv; Leadkosten dürfen nur als vorsichtiger Korridor gelesen werden.';
  }

  return 'Marktbild reicht für eine erste Korridor-Einordnung.';
}

function resolveOverallSignal(score: number, hardStops: string[]): Signal {
  if (hardStops.length >= 2 || score < 48) {
    return 'red';
  }

  if (hardStops.length === 1 || score < 74) {
    return 'yellow';
  }

  return 'green';
}

function resolveResultLabel(signal: Signal) {
  if (signal === 'green') {
    return 'Grüner Fit: Umsetzung prüfen';
  }

  if (signal === 'yellow') {
    return 'Gelber Fit: erst Lücken klären';
  }

  return 'Roter Fit: keine Umsetzungsempfehlung';
}

function resolveNextStep(signal: Signal) {
  if (signal === 'green') {
    return 'Nächster Schritt: Befund als Founding-Partner-Fit einordnen und erst danach über Umsetzung sprechen.';
  }

  if (signal === 'yellow') {
    return 'Nächster Schritt: die gelben und roten Signale klären, bevor Budget oder Technik gebaut wird.';
  }

  return 'Nächster Schritt: nicht in einen Umsetzungs-Pitch springen; zuerst Branche, Budget, Prozess oder Messbarkeit korrigieren.';
}

function getActionPlan(signal: Signal) {
  if (signal === 'green') {
    return {
      shortLabel: 'Analyse vertiefen',
      title: 'Founding-Partner-Fit sauber prüfen',
      text: 'Der Fall wirkt tragfähig genug, um aus der lokalen Ampel eine echte System-Diagnose abzuleiten.',
      items: [
        'Website und Anfragepfad konkret prüfen.',
        'Leadkosten-Korridor mit Region und Angebot abgleichen.',
        'Erst danach über Umsetzung, Tracking und Automatisierung sprechen.',
      ],
    };
  }

  if (signal === 'yellow') {
    return {
      shortLabel: 'Lücken schließen',
      title: 'Erst die gelben Signale klären',
      text: 'Der Fall ist nicht ausgeschlossen, aber eine direkte Umsetzung wäre zu früh.',
      items: [
        'Messlage, Reaktionszeit oder Verantwortlichkeit schärfen.',
        'Budget und Auftragswert gegen realistische Leadkosten halten.',
        'Danach erneut bewerten, ob die Analyse belastbar genug ist.',
      ],
    };
  }

  return {
    shortLabel: 'Nicht bauen',
    title: 'Keine Umsetzung in diesem Zustand',
    text: 'Die aktuelle Kombination erzeugt zu viel Risiko für einen seriösen Umsetzungs-Pitch.',
    items: [
      'Harten Stopper identifizieren und zuerst beheben.',
      'Kein Budget in Funnel-Technik oder Ads skalieren.',
      'Bei verbessertem Fit die Analyse neu starten.',
    ],
  };
}
