import { createRoot, render, useEffect, useRef, useState } from '@wordpress/element';
import { wgosAssetModules, wgosAssetPhases, wgosAssets } from './wgos-assets';

const HASH_PREFIX = '#asset-';

function getAssetById(assetId) {
	return (
		wgosAssets.find(
			(asset) => asset.id === assetId || (Array.isArray(asset.aliases) && asset.aliases.includes(assetId)),
		) || null
	);
}

function getModuleById(moduleId) {
	return wgosAssetModules.find((module) => module.id === moduleId) || null;
}

function resolveHref(cta, links) {
	if (cta?.href) {
		return cta.href;
	}

	if (cta?.hrefKey && links?.[cta.hrefKey]) {
		return links[cta.hrefKey];
	}

	return '#pakete';
}

function emitExplorerEvent(name, asset) {
	if (!asset || typeof document === 'undefined') {
		return;
	}

	const module = getModuleById(asset.moduleId);

	document.dispatchEvent(
		new CustomEvent(name, {
			detail: {
				asset_id: asset.id,
				asset_label: asset.label,
				module_id: asset.moduleId,
				module_label: module ? module.label : '',
				category: asset.category,
			},
		}),
	);
}

function WGOSAssetExplorer({ links = {} }) {
	const [activeId, setActiveId] = useState('');
	const [hoveredId, setHoveredId] = useState('');
	const panelRef = useRef(null);
	const assetOrder = wgosAssets.map((asset) => asset.id);
	const activeAsset = activeId ? getAssetById(activeId) : null;
	const activeIndex = activeAsset ? assetOrder.indexOf(activeAsset.id) : -1;

	function openAsset(assetId) {
		const asset = getAssetById(assetId);
		if (!asset) {
			setActiveId('');
			setHoveredId('');

			if (typeof window !== 'undefined' && window.location.hash.startsWith(HASH_PREFIX)) {
				window.history.replaceState(null, '', '#module');
			}

			return;
		}

		setActiveId(asset.id);
		setHoveredId('');

		if (typeof window !== 'undefined') {
			const nextHash = `${HASH_PREFIX}${asset.id}`;
			if (window.location.hash !== nextHash) {
				window.history.replaceState(null, '', nextHash);
			}
		}

		emitExplorerEvent('wgos:asset-open', asset);
	}

	function closeAsset() {
		setActiveId('');
		setHoveredId('');

		if (typeof window !== 'undefined' && window.location.hash.startsWith(HASH_PREFIX)) {
			window.history.replaceState(null, '', '#module');
		}
	}

	function navigateAsset(direction) {
		if (!activeAsset) {
			return;
		}

		const nextIndex = (activeIndex + direction + assetOrder.length) % assetOrder.length;
		openAsset(assetOrder[nextIndex]);
	}

	useEffect(() => {
		function handleHash() {
			if (typeof window === 'undefined' || !window.location.hash.startsWith(HASH_PREFIX)) {
				return;
			}

			const nextId = window.location.hash.replace(HASH_PREFIX, '');

			if (!getAssetById(nextId)) {
				closeAsset();
				return;
			}

			openAsset(nextId);
		}

		handleHash();
		window.addEventListener('hashchange', handleHash);

		return () => {
			window.removeEventListener('hashchange', handleHash);
		};
	}, []);

	useEffect(() => {
		function handleKeydown(event) {
			if (event.key === 'Escape') {
				closeAsset();
			}
		}

		document.addEventListener('keydown', handleKeydown);

		return () => {
			document.removeEventListener('keydown', handleKeydown);
		};
	}, []);

	useEffect(() => {
		if (!activeAsset || !panelRef.current || typeof window === 'undefined') {
			return;
		}

		const panel = panelRef.current;
		const shouldScroll = window.matchMedia('(max-width: 900px)').matches || window.location.hash === `${HASH_PREFIX}${activeAsset.id}`;

		window.requestAnimationFrame(() => {
			if (shouldScroll) {
				panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}

			if (typeof panel.focus === 'function') {
				panel.focus({ preventScroll: true });
			}
		});
	}, [activeAsset]);

	return (
		<div className="wgos-asset-explorer" aria-label="Systembaustein Explorer">
			<div className="wgos-asset-explorer__legend" aria-hidden="true">
				{wgosAssetPhases.map((phase) => {
					const assetCount = wgosAssets.filter((asset) => asset.category === phase.label).length;

					return (
						<div key={phase.id} className="wgos-asset-explorer__legend-item">
							<span className="wgos-asset-explorer__legend-eyebrow">{phase.eyebrow}</span>
							<strong>{phase.label}</strong>
							<span>{assetCount} Assets</span>
						</div>
					);
				})}
			</div>

			<div className="wgos-asset-explorer__lanes">
				{wgosAssetPhases.map((phase) => {
					const modules = wgosAssetModules.filter((module) => module.category === phase.label);

					return (
						<section key={phase.id} className="wgos-asset-explorer__lane" aria-labelledby={`phase-${phase.id}`}>
							<header className="wgos-asset-explorer__lane-head">
								<div>
									<span className="wgos-asset-explorer__lane-kicker">{phase.eyebrow}</span>
									<h3 id={`phase-${phase.id}`}>{phase.label}</h3>
								</div>
								<p>{phase.description}</p>
							</header>

							<div className="wgos-asset-explorer__module-grid">
								{modules.map((module) => {
									const moduleAssets = wgosAssets.filter((asset) => asset.moduleId === module.id);

									return (
										<article
											key={module.id}
											className="wgos-asset-explorer__module-card"
											style={{ '--wgos-module-accent': module.accent }}
											aria-labelledby={`${module.id}-title`}
										>
											<div className="wgos-asset-explorer__module-top">
												<span className="wgos-asset-explorer__module-number">{module.number}</span>
												<div>
													<h4 id={`${module.id}-title`}>{module.label}</h4>
													<p>{module.summary}</p>
												</div>
											</div>

											<div className="wgos-asset-explorer__asset-list" role="list" aria-label={`Assets in ${module.label}`}>
												{moduleAssets.map((asset) => {
													const isActive = activeId === asset.id;
													const isHovered = hoveredId === asset.id;

													return (
														<button
															key={asset.id}
															type="button"
															tabIndex={0}
															className={`wgos-asset-explorer__asset-chip${isActive ? ' is-active' : ''}`}
															aria-controls="wgos-asset-explorer-panel"
															aria-describedby={`tooltip-${asset.id}`}
															aria-expanded={isActive}
															onClick={() => openAsset(asset.id)}
															onMouseEnter={() => setHoveredId(asset.id)}
															onMouseLeave={() => setHoveredId('')}
															onFocus={() => setHoveredId(asset.id)}
															onBlur={() => setHoveredId('')}
														>
															<span className="wgos-asset-explorer__asset-label">{asset.label}</span>
															<span className="wgos-asset-explorer__asset-meta">{asset.credits} Credits</span>
															<span
																id={`tooltip-${asset.id}`}
																role="tooltip"
																aria-hidden={!isHovered}
																className={`wgos-asset-explorer__tooltip${isHovered ? ' is-visible' : ''}`}
															>
																{asset.short}
															</span>
														</button>
													);
												})}
											</div>
										</article>
									);
								})}
							</div>
						</section>
					);
				})}
			</div>

			<div
				id="wgos-asset-explorer-panel"
				ref={panelRef}
				className={`wgos-asset-explorer__panel${activeAsset ? ' is-open' : ''}`}
				tabIndex={-1}
				aria-live="polite"
			>
				{activeAsset ? (
					(() => {
						const activeModule = getModuleById(activeAsset.moduleId);
						const ctaHref = resolveHref(activeAsset.cta, links);

						return (
							<article id={`${HASH_PREFIX.replace('#', '')}${activeAsset.id}`} className="wgos-asset-explorer__panel-card">
								<div className="wgos-asset-explorer__panel-head">
									<div className="wgos-asset-explorer__panel-meta">
										<span>{activeModule ? `Modul ${activeModule.number}` : 'Asset'}</span>
										<span>{activeAsset.category}</span>
										<span>{activeAsset.group}</span>
									</div>
									<button type="button" className="wgos-asset-explorer__close" onClick={closeAsset} aria-label="Detailansicht schließen">
										<span aria-hidden="true">×</span>
									</button>
								</div>

								<div className="wgos-asset-explorer__panel-body">
									<div className="wgos-asset-explorer__panel-copy">
										<h3>{activeAsset.label}</h3>
										<p className="wgos-asset-explorer__panel-short">{activeAsset.short}</p>
										<p className="wgos-asset-explorer__panel-intro">{activeAsset.long.intro}</p>
										<ul className="wgos-asset-explorer__panel-list">
											{activeAsset.long.bullets.map((bullet) => (
												<li key={bullet}>{bullet}</li>
											))}
										</ul>
									</div>

									<aside className="wgos-asset-explorer__panel-side">
										<div className="wgos-asset-explorer__panel-stat">
											<span className="wgos-asset-explorer__panel-stat-label">Credits</span>
											<strong>{activeAsset.credits}</strong>
										</div>
										<div className="wgos-asset-explorer__panel-stat">
											<span className="wgos-asset-explorer__panel-stat-label">Lieferumfang</span>
											<strong>{activeAsset.deliverable}</strong>
										</div>
										{activeModule ? (
											<div className="wgos-asset-explorer__panel-stat">
												<span className="wgos-asset-explorer__panel-stat-label">Modul</span>
												<strong>{activeModule.label}</strong>
											</div>
										) : null}
									</aside>
								</div>

								<div className="wgos-asset-explorer__panel-foot">
									<div className="wgos-asset-explorer__panel-nav">
										<button type="button" onClick={() => navigateAsset(-1)}>
											Vorheriges Asset
										</button>
										<span>
											{activeIndex + 1} / {assetOrder.length}
										</span>
										<button type="button" onClick={() => navigateAsset(1)}>
											Nächstes Asset
										</button>
									</div>

									<a
										href={ctaHref}
										className="wgos-asset-explorer__cta"
										onClick={() => emitExplorerEvent('wgos:asset-cta', activeAsset)}
									>
										{activeAsset.cta.label}
									</a>
								</div>
							</article>
						);
					})()
				) : null}
			</div>
		</div>
	);
}

export function mountWGOSAssetExplorer(config = window.NexusWgosExplorerConfig || {}) {
	if (typeof window === 'undefined' || !window.wp?.element) {
		return;
	}

	const rootNode = document.getElementById('wgos-asset-explorer-root');
	if (!rootNode) {
		return;
	}

	const element = <WGOSAssetExplorer links={config.links || {}} />;

	if (typeof createRoot === 'function') {
		createRoot(rootNode).render(element);
		return;
	}

	render(element, rootNode);
}

if (typeof window !== 'undefined') {
	window.mountWGOSAssetExplorer = mountWGOSAssetExplorer;
	mountWGOSAssetExplorer();
}
