import React, { useEffect, useState } from "react";

export const phases = [
  {
    id: 1,
    label: "Fundament",
    color: "#60A5FA",
    icon: "⚡",
    modules: ["Performance Core", "Security", "Privacy Tracking"],
    stat: "98",
    statLabel: "Speed Score Mobile",
  },
  {
    id: 2,
    label: "Wachstum",
    color: "#34D399",
    icon: "📈",
    modules: ["Technical SEO", "Content Engine", "Conversion CRO"],
    stat: "+180%",
    statLabel: "Organischer Traffic",
  },
  {
    id: 3,
    label: "Skalierung",
    color: "#FB923C",
    icon: "🚀",
    modules: ["Paid Booster", "n8n Automation", "Lead Routing"],
    stat: "über 85 %",
    statLabel: "Kosten pro Lead",
  },
];

export default function HomepageMindmapTeaser() {
  const [mounted, setMounted] = useState(false);
  const [activeId, setActiveId] = useState(null);
  const wgosUrl =
    typeof window !== "undefined" &&
    window.NexusHomeMindmapConfig &&
    window.NexusHomeMindmapConfig.wgosUrl
      ? String(window.NexusHomeMindmapConfig.wgosUrl)
      : "/wordpress-growth-operating-system/";

  useEffect(() => {
    const raf = requestAnimationFrame(() => setMounted(true));
    return () => cancelAnimationFrame(raf);
  }, []);

  return (
    <section className="hmt-root" aria-labelledby="hmt-title">
      <div className="hmt-shell">
        <p className="hmt-kicker">System-Vorschau</p>
        <h3 id="hmt-title">WGOS in 3 Phasen</h3>
        <p className="hmt-subtitle">
          Fundament aufbauen, Wachstum stabilisieren, Skalierung kontrolliert
          hochfahren.
        </p>

        <div className="hmt-map" aria-label="WGOS Teaser Mindmap">
          {phases.map((phase, index) => (
            <button
              key={phase.id}
              type="button"
              className={[
                "hmt-node",
                mounted ? "is-mounted" : "",
                activeId === phase.id ? "is-active" : "",
              ]
                .filter(Boolean)
                .join(" ")}
              style={{
                "--node-color": phase.color,
                animationDelay: `${index * 140}ms`,
              }}
              aria-expanded={activeId === phase.id}
              aria-label={`${phase.label}: ${phase.modules.join(", ")}`}
              onClick={() =>
                setActiveId((current) => (current === phase.id ? null : phase.id))
              }
            >
              <span className="hmt-icon" aria-hidden="true">
                {phase.icon}
              </span>
              <span className="hmt-label">{phase.label}</span>

              <span className="hmt-tooltip" role="tooltip">
                {phase.modules.map((module) => (
                  <span key={module} className="hmt-tip-item">
                    {module}
                  </span>
                ))}
              </span>

              <span className="hmt-stat">
                <strong>{phase.stat}</strong>
                <em>{phase.statLabel}</em>
              </span>
            </button>
          ))}
        </div>

        <a className="hmt-cta" href={wgosUrl}>
          Das vollständige System →
        </a>
      </div>

      <style>{`
        .hmt-root {
          background: #070709;
          border: 1px solid rgba(255, 176, 32, 0.2);
          border-radius: 14px;
          padding: 28px 20px;
          margin: 28px 0;
        }

        .hmt-shell {
          max-width: 980px;
          margin: 0 auto;
        }

        .hmt-kicker {
          margin: 0 0 8px;
          color: #ffb020;
          text-transform: uppercase;
          letter-spacing: 0.08em;
          font-size: 12px;
          font-weight: 700;
        }

        .hmt-root h3 {
          margin: 0;
          color: #f8fafc;
          font-size: clamp(1.25rem, 2.6vw, 1.9rem);
          line-height: 1.2;
        }

        .hmt-subtitle {
          margin: 10px 0 0;
          color: rgba(248, 250, 252, 0.75);
          font-size: 0.98rem;
        }

        .hmt-map {
          display: grid;
          grid-template-columns: repeat(3, minmax(0, 1fr));
          gap: 14px;
          margin-top: 22px;
        }

        .hmt-node {
          appearance: none;
          border: 1px solid rgba(255, 255, 255, 0.14);
          border-top: 3px solid var(--node-color);
          border-radius: 12px;
          background: linear-gradient(
            180deg,
            rgba(255, 255, 255, 0.03),
            rgba(255, 255, 255, 0.015)
          );
          color: #f8fafc;
          text-align: left;
          cursor: pointer;
          padding: 14px 14px 12px;
          min-height: 132px;
          position: relative;
          opacity: 0;
          transform: translateY(12px);
          transition: border-color 0.25s ease, transform 0.25s ease,
            box-shadow 0.25s ease;
        }

        .hmt-node.is-mounted {
          animation: hmtFadeUp 420ms ease forwards;
        }

        .hmt-node:hover {
          border-color: rgba(255, 176, 32, 0.45);
          transform: translateY(-2px);
          box-shadow: 0 16px 36px rgba(0, 0, 0, 0.35);
        }

        .hmt-node.is-active {
          border-color: rgba(255, 176, 32, 0.6);
        }

        .hmt-icon {
          display: inline-flex;
          width: 30px;
          height: 30px;
          align-items: center;
          justify-content: center;
          border-radius: 999px;
          background: rgba(255, 255, 255, 0.09);
          font-size: 16px;
        }

        .hmt-label {
          display: block;
          margin-top: 10px;
          font-size: 1rem;
          font-weight: 700;
          color: #f8fafc;
        }

        .hmt-tooltip {
          display: grid;
          gap: 5px;
          margin-top: 10px;
          opacity: 0;
          max-height: 0;
          overflow: hidden;
          transition: opacity 0.24s ease, max-height 0.24s ease;
        }

        .hmt-node:hover .hmt-tooltip,
        .hmt-node.is-active .hmt-tooltip {
          opacity: 1;
          max-height: 90px;
        }

        .hmt-tip-item {
          color: rgba(248, 250, 252, 0.82);
          font-size: 0.84rem;
          line-height: 1.35;
        }

        .hmt-stat {
          display: grid;
          margin-top: 8px;
          opacity: 0;
          max-height: 0;
          overflow: hidden;
          transform: scale(0.98);
          transform-origin: top left;
          transition: opacity 0.22s ease, max-height 0.22s ease,
            transform 0.22s ease;
        }

        .hmt-node.is-active .hmt-stat {
          opacity: 1;
          max-height: 72px;
          transform: scale(1);
        }

        .hmt-stat strong {
          color: #ffb020;
          font-size: 1.35rem;
          line-height: 1;
          margin-top: 2px;
        }

        .hmt-stat em {
          color: rgba(248, 250, 252, 0.82);
          font-size: 0.78rem;
          font-style: normal;
          margin-top: 4px;
        }

        .hmt-cta {
          display: inline-flex;
          margin-top: 18px;
          color: #ffb020;
          text-decoration: none;
          font-weight: 700;
          letter-spacing: 0.02em;
        }

        .hmt-cta:hover {
          color: #ffc14d;
        }

        @keyframes hmtFadeUp {
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        @media (max-width: 900px) {
          .hmt-map {
            grid-template-columns: 1fr;
          }

          .hmt-node {
            min-height: 0;
          }
        }
      `}</style>
    </section>
  );
}
