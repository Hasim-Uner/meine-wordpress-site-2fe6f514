(function () {
	'use strict';

	if (!window.wp || !window.wp.element) {
		return;
	}

	var elementApi = window.wp.element;
	var assetData = window.WGOSAssetData || {};
	var wgosAssetModules = Array.isArray(assetData.wgosAssetModules) ? assetData.wgosAssetModules : [];
	var wgosAssetPhases = Array.isArray(assetData.wgosAssetPhases) ? assetData.wgosAssetPhases : [];
	var wgosAssets = Array.isArray(assetData.wgosAssets) ? assetData.wgosAssets : [];
	var createElement = elementApi.createElement;
	var useEffect = elementApi.useEffect;
	var useRef = elementApi.useRef;
	var useState = elementApi.useState;
	var createRoot = elementApi.createRoot;
	var render = elementApi.render;
	var HASH_PREFIX = '#asset-';

	function h(type, props) {
		var children = Array.prototype.slice.call(arguments, 2);
		return createElement.apply(null, [ type, props ].concat(children));
	}

	function getAssetById(assetId) {
		for (var index = 0; index < wgosAssets.length; index += 1) {
			if (wgosAssets[index].id === assetId) {
				return wgosAssets[index];
			}

			if (Array.isArray(wgosAssets[index].aliases) && wgosAssets[index].aliases.indexOf(assetId) !== -1) {
				return wgosAssets[index];
			}
		}

		return null;
	}

	function getModuleById(moduleId) {
		for (var index = 0; index < wgosAssetModules.length; index += 1) {
			if (wgosAssetModules[index].id === moduleId) {
				return wgosAssetModules[index];
			}
		}

		return null;
	}

	function resolveHref(cta, links) {
		if (cta && cta.href) {
			return cta.href;
		}

		if (cta && cta.hrefKey && links && links[cta.hrefKey]) {
			return links[cta.hrefKey];
		}

		return '#audit';
	}

	function emitExplorerEvent(name, asset) {
		var module;

		if (!asset || typeof document === 'undefined') {
			return;
		}

		module = getModuleById(asset.moduleId);

		document.dispatchEvent(
			new CustomEvent(name, {
				detail: {
					asset_id: asset.id,
					asset_label: asset.label,
					module_id: asset.moduleId,
					module_label: module ? module.label : '',
					category: asset.category,
				},
			})
		);
	}

	function Explorer(props) {
		var links = props && props.links ? props.links : {};
		var activeState = useState('');
		var activeId = activeState[0];
		var setActiveId = activeState[1];
		var hoveredState = useState('');
		var hoveredId = hoveredState[0];
		var setHoveredId = hoveredState[1];
		var panelRef = useRef(null);
		var assetOrder = wgosAssets.map(function (asset) {
			return asset.id;
		});
		var activeAsset = activeId ? getAssetById(activeId) : null;
		var activeIndex = activeAsset ? assetOrder.indexOf(activeAsset.id) : -1;

		function openAsset(assetId) {
			var asset = getAssetById(assetId);
			var nextHash;

			if (!asset) {
					setActiveId('');
					setHoveredId('');

					if (typeof window !== 'undefined' && window.location.hash.indexOf(HASH_PREFIX) === 0) {
						window.history.replaceState(null, '', '#explorer');
					}

				return;
			}

			setActiveId(asset.id);
			setHoveredId('');

			if (typeof window !== 'undefined') {
				nextHash = HASH_PREFIX + asset.id;
				if (window.location.hash !== nextHash) {
					window.history.replaceState(null, '', nextHash);
				}
			}

			emitExplorerEvent('wgos:asset-open', asset);
		}

		function closeAsset() {
				setActiveId('');
				setHoveredId('');

				if (typeof window !== 'undefined' && window.location.hash.indexOf(HASH_PREFIX) === 0) {
					window.history.replaceState(null, '', '#explorer');
				}
			}

		function navigateAsset(direction) {
			var nextIndex;

			if (!activeAsset) {
				return;
			}

			nextIndex = (activeIndex + direction + assetOrder.length) % assetOrder.length;
			openAsset(assetOrder[nextIndex]);
		}

		useEffect(function () {
			function handleHash() {
				var nextId;

				if (typeof window === 'undefined' || window.location.hash.indexOf(HASH_PREFIX) !== 0) {
					return;
				}

				nextId = window.location.hash.replace(HASH_PREFIX, '');

				if (!getAssetById(nextId)) {
					closeAsset();
					return;
				}

				openAsset(nextId);
			}

			handleHash();
			window.addEventListener('hashchange', handleHash);

			return function () {
				window.removeEventListener('hashchange', handleHash);
			};
		}, []);

		useEffect(function () {
			function handleKeydown(event) {
				if (event.key === 'Escape') {
					closeAsset();
				}
			}

			document.addEventListener('keydown', handleKeydown);

			return function () {
				document.removeEventListener('keydown', handleKeydown);
			};
		}, []);

		useEffect(function () {
			var panel;
			var shouldScroll;

			if (!activeAsset || !panelRef.current || typeof window === 'undefined') {
				return;
			}

			panel = panelRef.current;
			shouldScroll = window.matchMedia('(max-width: 900px)').matches || window.location.hash === HASH_PREFIX + activeAsset.id;

			window.requestAnimationFrame(function () {
				if (shouldScroll) {
					panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
				}

				if (typeof panel.focus === 'function') {
					try {
						panel.focus({ preventScroll: true });
					} catch (error) {
						panel.focus();
					}
				}
			});
		}, [ activeAsset ]);

		return h(
			'div',
			{
				className: 'wgos-asset-explorer',
				'aria-label': 'Systembaustein Explorer',
			},
			h(
				'div',
				{
					className: 'wgos-asset-explorer__legend',
					'aria-hidden': 'true',
				},
				wgosAssetPhases.map(function (phase) {
					var assetCount = wgosAssets.filter(function (asset) {
						return asset.category === phase.label;
					}).length;

					return h(
						'div',
						{
							key: phase.id,
							className: 'wgos-asset-explorer__legend-item',
						},
						h('span', { className: 'wgos-asset-explorer__legend-eyebrow' }, phase.eyebrow),
						h('strong', null, phase.label),
						h('span', null, assetCount + ' Assets')
					);
				})
			),
			h(
				'div',
				{ className: 'wgos-asset-explorer__lanes' },
				wgosAssetPhases.map(function (phase) {
					var modules = wgosAssetModules.filter(function (module) {
						return module.category === phase.label;
					});

					return h(
						'section',
						{
							key: phase.id,
							className: 'wgos-asset-explorer__lane',
							'aria-labelledby': 'phase-' + phase.id,
						},
						h(
							'header',
							{ className: 'wgos-asset-explorer__lane-head' },
							h(
								'div',
								null,
								h('span', { className: 'wgos-asset-explorer__lane-kicker' }, phase.eyebrow),
								h('h3', { id: 'phase-' + phase.id }, phase.label)
							),
							h('p', null, phase.description)
						),
						h(
							'div',
							{ className: 'wgos-asset-explorer__module-grid' },
							modules.map(function (module) {
								var moduleAssets = wgosAssets.filter(function (asset) {
									return asset.moduleId === module.id;
								});

								return h(
									'article',
									{
										key: module.id,
										className: 'wgos-asset-explorer__module-card',
										style: { '--wgos-module-accent': module.accent },
										'aria-labelledby': module.id + '-title',
									},
									h(
										'div',
										{ className: 'wgos-asset-explorer__module-top' },
										h('span', { className: 'wgos-asset-explorer__module-number' }, module.number),
										h(
											'div',
											null,
											h('h4', { id: module.id + '-title' }, module.label),
											h('p', null, module.summary)
										)
									),
									h(
										'div',
										{
											className: 'wgos-asset-explorer__asset-list',
											role: 'list',
											'aria-label': 'Assets in ' + module.label,
										},
										moduleAssets.map(function (asset) {
											var isActive = activeId === asset.id;
											var isHovered = hoveredId === asset.id;

											return h(
												'button',
												{
													key: asset.id,
													type: 'button',
													role: 'button',
													tabIndex: 0,
													className: 'wgos-asset-explorer__asset-chip' + (isActive ? ' is-active' : ''),
													'aria-controls': 'wgos-asset-explorer-panel',
													'aria-describedby': 'tooltip-' + asset.id,
													'aria-expanded': isActive,
													onClick: function () {
														openAsset(asset.id);
													},
													onMouseEnter: function () {
														setHoveredId(asset.id);
													},
													onMouseLeave: function () {
														setHoveredId('');
													},
													onFocus: function () {
														setHoveredId(asset.id);
													},
													onBlur: function () {
														setHoveredId('');
													},
												},
												h('span', { className: 'wgos-asset-explorer__asset-label' }, asset.label),
												h('span', { className: 'wgos-asset-explorer__asset-meta' }, asset.credits + ' Credits'),
												h(
													'span',
													{
														id: 'tooltip-' + asset.id,
														role: 'tooltip',
														'aria-hidden': !isHovered,
														className: 'wgos-asset-explorer__tooltip' + (isHovered ? ' is-visible' : ''),
													},
													asset.short
												)
											);
										})
									)
								);
							})
						)
					);
				})
			),
			h(
				'div',
				{
					id: 'wgos-asset-explorer-panel',
					ref: panelRef,
					className: 'wgos-asset-explorer__panel' + (activeAsset ? ' is-open' : ''),
					tabIndex: -1,
					'aria-live': 'polite',
				},
				activeAsset
					? (function () {
						var activeModule = getModuleById(activeAsset.moduleId);
						var ctaHref = resolveHref(activeAsset.cta, links);

						return h(
							'article',
							{
								id: HASH_PREFIX.replace('#', '') + activeAsset.id,
								className: 'wgos-asset-explorer__panel-card',
							},
							h(
								'div',
								{ className: 'wgos-asset-explorer__panel-head' },
								h(
									'div',
									{ className: 'wgos-asset-explorer__panel-meta' },
									h('span', null, activeModule ? 'Modul ' + activeModule.number : 'Asset'),
									h('span', null, activeAsset.category),
									h('span', null, activeAsset.group)
								),
								h(
									'button',
									{
										type: 'button',
										className: 'wgos-asset-explorer__close',
										onClick: closeAsset,
										'aria-label': 'Detailansicht schließen',
									},
									'×'
								)
							),
							h(
								'div',
								{ className: 'wgos-asset-explorer__panel-body' },
								h(
									'div',
									{ className: 'wgos-asset-explorer__panel-copy' },
									h('h3', null, activeAsset.label),
									h('p', { className: 'wgos-asset-explorer__panel-short' }, activeAsset.short),
									h('p', { className: 'wgos-asset-explorer__panel-intro' }, activeAsset.long.intro),
									h(
										'ul',
										{ className: 'wgos-asset-explorer__panel-list' },
										activeAsset.long.bullets.map(function (bullet) {
											return h('li', { key: bullet }, bullet);
										})
									)
								),
								h(
									'aside',
									{ className: 'wgos-asset-explorer__panel-side' },
									h(
										'div',
										{ className: 'wgos-asset-explorer__panel-stat' },
										h('span', { className: 'wgos-asset-explorer__panel-stat-label' }, 'Credits'),
										h('strong', null, activeAsset.credits)
									),
									h(
										'div',
										{ className: 'wgos-asset-explorer__panel-stat' },
										h('span', { className: 'wgos-asset-explorer__panel-stat-label' }, 'Lieferumfang'),
										h('strong', null, activeAsset.deliverable)
									),
									activeModule
										? h(
											'div',
											{ className: 'wgos-asset-explorer__panel-stat' },
											h('span', { className: 'wgos-asset-explorer__panel-stat-label' }, 'Modul'),
											h('strong', null, activeModule.label)
										)
										: null
								)
							),
							h(
								'div',
								{ className: 'wgos-asset-explorer__panel-foot' },
								h(
									'div',
									{ className: 'wgos-asset-explorer__panel-nav' },
									h(
										'button',
										{
											type: 'button',
											onClick: function () {
												navigateAsset(-1);
											},
										},
										'Vorheriges Asset'
									),
									h('span', null, (activeIndex + 1) + ' / ' + assetOrder.length),
									h(
										'button',
										{
											type: 'button',
											onClick: function () {
												navigateAsset(1);
											},
										},
										'Nächstes Asset'
									)
								),
								h(
									'a',
									{
										href: ctaHref,
										className: 'wgos-asset-explorer__cta',
										onClick: function () {
											emitExplorerEvent('wgos:asset-cta', activeAsset);
										},
									},
									activeAsset.cta.label
								)
							)
						);
					})()
					: null
			)
		);
	}

	function mountWGOSAssetExplorer(config) {
		var rootNode;
		var element;

		if (typeof window === 'undefined' || !window.wp || !window.wp.element) {
			return;
		}

		rootNode = document.getElementById('wgos-asset-explorer-root');

		if (!rootNode) {
			return;
		}

		element = h(Explorer, { links: config && config.links ? config.links : {} });

		if (typeof createRoot === 'function') {
			createRoot(rootNode).render(element);
			return;
		}

		render(element, rootNode);
	}

	window.mountWGOSAssetExplorer = mountWGOSAssetExplorer;
	mountWGOSAssetExplorer(window.NexusWgosExplorerConfig || {});
})();
