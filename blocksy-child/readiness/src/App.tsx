import {useMemo, useState} from 'react';

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
  },
  {
    title: 'Region',
    purpose: 'Marktreichweite einschätzen',
  },
  {
    title: 'Angebot',
    purpose: 'Wirtschaftliche Relevanz prüfen',
  },
  {
    title: 'Werbebudget',
    purpose: 'Skalierungsfähigkeit prüfen',
  },
  {
    title: 'Website',
    purpose: 'Anfragepfad einschätzen',
  },
  {
    title: 'Tracking',
    purpose: 'Messlage prüfen',
  },
  {
    title: 'Anfrageprozess',
    purpose: 'Vertriebsfähigkeit prüfen',
  },
  {
    title: 'Marktbild',
    purpose: 'Leadkosten-Korridor einordnen',
  },
];

const industryOptions: Option[] = [
  {value: 'solar', label: 'Solar / PV'},
  {value: 'heatpump', label: 'Wärmepumpe'},
  {value: 'shk', label: 'SHK'},
  {value: 'storage', label: 'Speicher / Energie'},
  {value: 'other', label: 'Andere Branche'},
];

const offerTypeOptions: Option[] = [
  {value: 'installation', label: 'Installation'},
  {value: 'planning_installation', label: 'Planung + Installation'},
  {value: 'maintenance', label: 'Wartung / Service'},
  {value: 'consulting', label: 'Beratung'},
];

const employeeOptions: Option[] = [
  {value: '1_5', label: '1-5'},
  {value: '6_9', label: '6-9'},
  {value: '10_25', label: '10-25'},
  {value: '26_50', label: '26-50'},
  {value: '50_plus', label: '50+'},
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
  {value: 'under_5k', label: '< 5.000 EUR'},
  {value: '5k_10k', label: '5.000-10.000 EUR'},
  {value: '10k_25k', label: '10.000-25.000 EUR'},
  {value: '25k_plus', label: '25.000 EUR+'},
];

const budgetOptions: Option[] = [
  {value: 'under_2500', label: '< 2.500 EUR / Monat'},
  {value: '2500_4999', label: '2.500-4.999 EUR / Monat'},
  {value: '5000_9999', label: '5.000-9.999 EUR / Monat'},
  {value: '10000_19999', label: '10.000-19.999 EUR / Monat'},
  {value: '20000_plus', label: '20.000 EUR+ / Monat'},
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

const signalLabels: Record<Signal, string> = {
  green: 'Grün',
  yellow: 'Gelb',
  red: 'Rot',
};

export function App() {
  const [form, setForm] = useState<FormState>(initialForm);
  const [stepIndex, setStepIndex] = useState(0);
  const [maxStepIndex, setMaxStepIndex] = useState(0);
  const [errors, setErrors] = useState<string[]>([]);
  const [showResult, setShowResult] = useState(false);

  const evaluation = useMemo(() => evaluateFit(form), [form]);
  const currentStep = steps[stepIndex];

  function updateField(field: keyof FormState, value: string) {
    setForm((current) => ({
      ...current,
      [field]: value,
    }));
    setErrors([]);
  }

  function goToStep(nextIndex: number) {
    if (nextIndex > maxStepIndex) {
      return;
    }

    setShowResult(false);
    setStepIndex(nextIndex);
    setErrors([]);
  }

  function handleNext() {
    const nextErrors = validateStep(stepIndex, form);

    if (nextErrors.length > 0) {
      setErrors(nextErrors);
      return;
    }

    if (stepIndex < steps.length - 1) {
      const nextIndex = stepIndex + 1;
      setStepIndex(nextIndex);
      setMaxStepIndex((current) => Math.max(current, nextIndex));
      setErrors([]);
      return;
    }

    setMaxStepIndex(steps.length - 1);
    setShowResult(true);
    setErrors([]);
  }

  function handleBack() {
    if (showResult) {
      setShowResult(false);
      return;
    }

    setStepIndex((current) => Math.max(0, current - 1));
    setErrors([]);
  }

  function handleReset() {
    setForm(initialForm);
    setStepIndex(0);
    setMaxStepIndex(0);
    setErrors([]);
    setShowResult(false);
  }

  return (
    <main className="readiness-shell" aria-labelledby="readiness-title" data-track-section="request_analysis_react_form">
      <section className="readiness-hero">
        <div className="readiness-kicker">Anfrage-System-Analyse</div>
        <h1 id="readiness-title">Prüfen, ob ein eigenes Anfrage-System wirtschaftlich Sinn macht.</h1>
        <p>
          Acht kurze Schritte für Founding-Partner-Fit, Marktbild, Anfragepfad, Messlage und Prozessreife.
          Das Ergebnis bleibt lokal im Browser und sendet nichts an n8n, CRM oder E-Mail.
        </p>
      </section>

      <div className="readiness-layout">
        <aside className="readiness-progress-panel" aria-label="Analyse-Fortschritt">
          <div className="readiness-progress-head">
            <span>8 Schritte</span>
            <strong>{showResult ? 'Ergebnis' : `${stepIndex + 1} / ${steps.length}`}</strong>
          </div>
          <ol className="readiness-progress">
            {steps.map((step, index) => {
              const isActive = !showResult && index === stepIndex;
              const isDone = showResult || index < stepIndex || index < maxStepIndex;
              const isLocked = index > maxStepIndex;

              return (
                <li key={step.title} className={isActive ? 'is-active' : isDone ? 'is-done' : ''}>
                  <button type="button" disabled={isLocked} onClick={() => goToStep(index)}>
                    <span>{String(index + 1).padStart(2, '0')}</span>
                    <strong>{step.title}</strong>
                  </button>
                </li>
              );
            })}
          </ol>
        </aside>

        {showResult ? (
          <ResultView evaluation={evaluation} onBack={handleBack} onReset={handleReset} />
        ) : (
          <section className="readiness-panel" aria-labelledby="readiness-step-title">
            <div className="readiness-step-head">
              <span>{currentStep.purpose}</span>
              <h2 id="readiness-step-title">{currentStep.title}</h2>
            </div>

            {errors.length > 0 && (
              <div className="readiness-errors" role="alert">
                {errors.map((error) => (
                  <p key={error}>{error}</p>
                ))}
              </div>
            )}

            <StepFields stepIndex={stepIndex} form={form} updateField={updateField} />

            <div className="readiness-actions">
              <button type="button" className="readiness-button readiness-button--secondary" onClick={handleBack} disabled={stepIndex === 0}>
                Zurück
              </button>
              <button
                type="button"
                className="readiness-button readiness-button--primary"
                onClick={handleNext}
                data-track-action={`request_analysis_step_${stepIndex + 1}_completed`}
                data-track-category="lead_funnel"
                data-track-section="request_analysis"
                data-track-funnel-stage={`request_analysis_step_${stepIndex + 1}`}
              >
                {stepIndex === steps.length - 1 ? 'Lokales Ergebnis anzeigen' : 'Weiter'}
              </button>
            </div>
          </section>
        )}
      </div>
    </main>
  );
}

function StepFields({
  stepIndex,
  form,
  updateField,
}: {
  stepIndex: number;
  form: FormState;
  updateField: (field: keyof FormState, value: string) => void;
}) {
  if (stepIndex === 0) {
    return (
      <div className="readiness-fields">
        <OptionGroup legend="Branche" value={form.industry} options={industryOptions} onChange={(value) => updateField('industry', value)} />
        <OptionGroup legend="Angebotsart" value={form.offerType} options={offerTypeOptions} onChange={(value) => updateField('offerType', value)} />
        <OptionGroup legend="Mitarbeitergröße" value={form.employeeRange} options={employeeOptions} onChange={(value) => updateField('employeeRange', value)} />
      </div>
    );
  }

  if (stepIndex === 1) {
    return (
      <div className="readiness-fields">
        <OptionGroup legend="Land" value={form.country} options={countryOptions} onChange={(value) => updateField('country', value)} />
        <TextField
          label="PLZ-Region"
          value={form.plzRegion}
          placeholder="z. B. 30, 31, Hannover, Region Sued"
          hint="Keine volle Adresse eintragen. Eine grobe Region reicht."
          onChange={(value) => updateField('plzRegion', value)}
        />
      </div>
    );
  }

  if (stepIndex === 2) {
    return (
      <div className="readiness-fields">
        <OptionGroup legend="Hauptleistung" value={form.offerFocus} options={offerFocusOptions} onChange={(value) => updateField('offerFocus', value)} />
        <OptionGroup
          legend="Durchschnittlicher Auftragswert"
          value={form.averageOrderValueRange}
          options={orderValueOptions}
          onChange={(value) => updateField('averageOrderValueRange', value)}
        />
      </div>
    );
  }

  if (stepIndex === 3) {
    return (
      <div className="readiness-fields">
        <OptionGroup
          legend="Monatliches Werbebudget"
          value={form.adBudgetRange}
          options={budgetOptions}
          onChange={(value) => updateField('adBudgetRange', value)}
          hint="Unter 5.000 EUR pro Monat blockiert die Founding-Cohort-Empfehlung."
        />
      </div>
    );
  }

  if (stepIndex === 4) {
    return (
      <div className="readiness-fields">
        <TextField
          label="Website-URL"
          value={form.websiteUrl}
          placeholder="beispiel.de"
          hint="Optional. Ohne URL bleibt die Befundtiefe eingeschraenkt."
          onChange={(value) => updateField('websiteUrl', value)}
        />
        <OptionGroup legend="CMS-Selbstauskunft" value={form.cms} options={cmsOptions} onChange={(value) => updateField('cms', value)} />
      </div>
    );
  }

  if (stepIndex === 5) {
    return (
      <div className="readiness-fields readiness-fields--two">
        <OptionGroup legend="Pixel sichtbar?" value={form.pixelPresent} options={yesNoUnknownOptions} onChange={(value) => updateField('pixelPresent', value)} />
        <OptionGroup legend="GTM vorhanden?" value={form.gtmPresent} options={yesNoUnknownOptions} onChange={(value) => updateField('gtmPresent', value)} />
        <OptionGroup legend="Consent Mode bekannt?" value={form.consentMode} options={yesNoUnknownOptions} onChange={(value) => updateField('consentMode', value)} />
        <OptionGroup legend="Meta CAPI bekannt?" value={form.metaCapi} options={yesNoUnknownOptions} onChange={(value) => updateField('metaCapi', value)} />
      </div>
    );
  }

  if (stepIndex === 6) {
    return (
      <div className="readiness-fields">
        <OptionGroup legend="Reaktionszeit auf Anfragen" value={form.responseTime} options={responseTimeOptions} onChange={(value) => updateField('responseTime', value)} />
        <OptionGroup legend="Verantwortung" value={form.responsible} options={responsibleOptions} onChange={(value) => updateField('responsible', value)} />
        <OptionGroup legend="CRM vorhanden?" value={form.crmPresent} options={yesNoUnknownOptions} onChange={(value) => updateField('crmPresent', value)} />
      </div>
    );
  }

  return (
    <div className="readiness-fields">
      <OptionGroup legend="Zielregion" value={form.targetRegion} options={targetRegionOptions} onChange={(value) => updateField('targetRegion', value)} />
      <OptionGroup legend="Hauptkanal heute" value={form.expectedChannelMix} options={channelOptions} onChange={(value) => updateField('expectedChannelMix', value)} />
      <OptionGroup legend="Wettbewerbseindruck" value={form.competition} options={competitionOptions} onChange={(value) => updateField('competition', value)} />
    </div>
  );
}

function OptionGroup({
  legend,
  value,
  options,
  hint,
  onChange,
}: {
  legend: string;
  value: string;
  options: Option[];
  hint?: string;
  onChange: (value: string) => void;
}) {
  return (
    <fieldset className="readiness-fieldset">
      <legend>{legend}</legend>
      {hint && <p className="readiness-hint">{hint}</p>}
      <div className="readiness-options">
        {options.map((option) => (
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
}: {
  label: string;
  value: string;
  placeholder?: string;
  hint?: string;
  onChange: (value: string) => void;
}) {
  const inputId = label.toLowerCase().replace(/[^a-z0-9]+/g, '-');

  return (
    <label className="readiness-text-field" htmlFor={inputId}>
      <span>{label}</span>
      {hint && <small>{hint}</small>}
      <input id={inputId} type="text" value={value} placeholder={placeholder} onChange={(event) => onChange(event.currentTarget.value)} />
    </label>
  );
}

function ResultView({evaluation, onBack, onReset}: {evaluation: Evaluation; onBack: () => void; onReset: () => void}) {
  return (
    <section className="readiness-result" aria-labelledby="readiness-result-title" data-track-section="request_analysis_result">
      <div className={`readiness-result-head readiness-result-head--${evaluation.signal}`}>
        <span>Lokales Ergebnis</span>
        <h2 id="readiness-result-title">{evaluation.label}</h2>
        <p>{evaluation.nextStep}</p>
      </div>

      <div className="readiness-result-grid">
        <div className="readiness-score">
          <span className={`readiness-signal readiness-signal--${evaluation.signal}`}>{signalLabels[evaluation.signal]}</span>
          <strong>{evaluation.score} / 100</strong>
          <p>Fit-Score aus den acht Formularschritten. Leadkosten bleiben ein Korridor, keine Garantie.</p>
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

      <div className="readiness-result-note">
        <strong>Privacy-Default:</strong> Dieses Ergebnis wurde nur im Browser berechnet. Es wurde kein n8n-Webhook gestartet, kein CRM-Datensatz erzeugt und keine E-Mail-Adresse abgefragt.
      </div>

      <div className="readiness-actions">
        <button type="button" className="readiness-button readiness-button--secondary" onClick={onBack}>
          Angaben bearbeiten
        </button>
        <button
          type="button"
          className="readiness-button readiness-button--primary"
          onClick={onReset}
          data-track-action="request_analysis_restart"
          data-track-category="lead_funnel"
          data-track-section="request_analysis_result"
        >
          Neu starten
        </button>
      </div>
    </section>
  );
}

function validateStep(index: number, form: FormState): string[] {
  const errors: string[] = [];

  if (index === 0) {
    requireField(errors, form.industry, 'Bitte Branche auswählen.');
    requireField(errors, form.offerType, 'Bitte Angebotsart auswählen.');
    requireField(errors, form.employeeRange, 'Bitte Mitarbeitergröße auswählen.');
  }

  if (index === 1) {
    requireField(errors, form.country, 'Bitte DACH-Land oder außerhalb DACH auswählen.');
    requireField(errors, form.plzRegion.trim(), 'Bitte eine grobe PLZ-Region eintragen.');
  }

  if (index === 2) {
    requireField(errors, form.offerFocus, 'Bitte Hauptleistung auswählen.');
    requireField(errors, form.averageOrderValueRange, 'Bitte durchschnittlichen Auftragswert auswählen.');
  }

  if (index === 3) {
    requireField(errors, form.adBudgetRange, 'Bitte Werbebudget-Range auswählen.');
  }

  if (index === 4) {
    requireField(errors, form.cms, 'Bitte CMS-Selbstauskunft auswählen.');

    if (form.websiteUrl.trim() && !isLikelyWebsite(form.websiteUrl)) {
      errors.push('Bitte Website als Domain oder URL eintragen, z. B. beispiel.de.');
    }
  }

  if (index === 5) {
    requireField(errors, form.pixelPresent, 'Bitte Pixel-Selbstauskunft auswählen.');
    requireField(errors, form.gtmPresent, 'Bitte GTM-Selbstauskunft auswählen.');
    requireField(errors, form.consentMode, 'Bitte Consent-Mode-Selbstauskunft auswählen.');
    requireField(errors, form.metaCapi, 'Bitte Meta-CAPI-Selbstauskunft auswählen.');
  }

  if (index === 6) {
    requireField(errors, form.responseTime, 'Bitte Reaktionszeit auswählen.');
    requireField(errors, form.responsible, 'Bitte Verantwortlichkeit auswählen.');
    requireField(errors, form.crmPresent, 'Bitte CRM-Selbstauskunft auswählen.');
  }

  if (index === 7) {
    requireField(errors, form.targetRegion, 'Bitte Zielregion auswählen.');
    requireField(errors, form.expectedChannelMix, 'Bitte Hauptkanal auswählen.');
    requireField(errors, form.competition, 'Bitte Wettbewerbseindruck auswählen.');
  }

  return errors;
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
    reasons.push('Keine Website-URL: Ergebnis bleibt bewusst eingeschraenkt.');
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
