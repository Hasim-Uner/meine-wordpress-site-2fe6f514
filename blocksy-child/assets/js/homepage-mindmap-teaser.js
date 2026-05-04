/*
 * Homepage WGOS Mindmap Teaser (runtime, no build step required)
 * Mounts into #homepage-mindmap-teaser-root on the front page.
 */
(function () {
	"use strict";

	var root = document.getElementById("homepage-mindmap-teaser-root");
	if (!root) {
		return;
	}

	var wgosUrl =
		window.NexusHomeMindmapConfig && window.NexusHomeMindmapConfig.wgosUrl
			? String(window.NexusHomeMindmapConfig.wgosUrl)
			: "/wordpress-growth-operating-system/";

	var phases = [
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
			stat: "−85,3%",
			statLabel: "Kosten pro Lead",
		},
	];

	if (!document.getElementById("hmt-teaser-styles")) {
		var style = document.createElement("style");
		style.id = "hmt-teaser-styles";
		style.textContent = [
			".hmt-root{background:#070709;border:1px solid rgba(255,176,32,.2);border-radius:14px;padding:28px 20px;margin:28px 0;}",
			".hmt-shell{max-width:980px;margin:0 auto;}",
			".hmt-kicker{margin:0 0 8px;color:#ffb020;text-transform:uppercase;letter-spacing:.08em;font-size:12px;font-weight:700;}",
			".hmt-root h3{margin:0;color:#f8fafc;font-size:clamp(1.25rem,2.6vw,1.9rem);line-height:1.2;}",
			".hmt-subtitle{margin:10px 0 0;color:rgba(248,250,252,.75);font-size:.98rem;}",
			".hmt-map{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-top:22px;}",
			".hmt-node{appearance:none;border:1px solid rgba(255,255,255,.14);border-top:3px solid var(--node-color);border-radius:12px;background:linear-gradient(180deg,rgba(255,255,255,.03),rgba(255,255,255,.015));color:#f8fafc;text-align:left;cursor:pointer;padding:14px 14px 12px;min-height:132px;position:relative;opacity:0;transform:translateY(12px);transition:border-color .25s ease,transform .25s ease,box-shadow .25s ease;}",
			".hmt-node.is-mounted{animation:hmtFadeUp .42s ease forwards;}",
			".hmt-node:hover{border-color:rgba(255,176,32,.45);transform:translateY(-2px);box-shadow:0 16px 36px rgba(0,0,0,.35);}",
			".hmt-node.is-active{border-color:rgba(255,176,32,.6);}",
			".hmt-icon{display:inline-flex;width:30px;height:30px;align-items:center;justify-content:center;border-radius:999px;background:rgba(255,255,255,.09);font-size:16px;}",
			".hmt-label{display:block;margin-top:10px;font-size:1rem;font-weight:700;color:#f8fafc;}",
			".hmt-tooltip{display:grid;gap:5px;margin-top:10px;opacity:0;max-height:0;overflow:hidden;transition:opacity .24s ease,max-height .24s ease;}",
			".hmt-node:hover .hmt-tooltip,.hmt-node.is-active .hmt-tooltip{opacity:1;max-height:90px;}",
			".hmt-tip-item{color:rgba(248,250,252,.82);font-size:.84rem;line-height:1.35;}",
			".hmt-stat{display:grid;margin-top:8px;opacity:0;max-height:0;overflow:hidden;transform:scale(.98);transform-origin:top left;transition:opacity .22s ease,max-height .22s ease,transform .22s ease;}",
			".hmt-node.is-active .hmt-stat{opacity:1;max-height:72px;transform:scale(1);}",
			".hmt-stat strong{color:#ffb020;font-size:1.35rem;line-height:1;margin-top:2px;}",
			".hmt-stat em{color:rgba(248,250,252,.82);font-size:.78rem;font-style:normal;margin-top:4px;}",
			".hmt-cta{display:inline-flex;margin-top:18px;color:#ffb020;text-decoration:none;font-weight:700;letter-spacing:.02em;}",
			".hmt-cta:hover{color:#ffc14d;}",
			"@keyframes hmtFadeUp{to{opacity:1;transform:translateY(0);}}",
			"@media (max-width:900px){.hmt-map{grid-template-columns:1fr;}.hmt-node{min-height:0;}}",
		].join("");
		document.head.appendChild(style);
	}

	function renderNode(phase, index) {
		var moduleItems = phase.modules
			.map(function (item) {
				return '<span class="hmt-tip-item">' + item + "</span>";
			})
			.join("");

		return (
			'<button type="button" class="hmt-node" data-node-id="' +
			phase.id +
			'" aria-label="' +
			phase.label +
			": " +
			phase.modules.join(", ") +
			'" style="--node-color:' +
			phase.color +
			';animation-delay:' +
			index * 140 +
			'ms">' +
			'<span class="hmt-icon" aria-hidden="true">' +
			phase.icon +
			"</span>" +
			'<span class="hmt-label">' +
			phase.label +
			"</span>" +
			'<span class="hmt-tooltip" role="tooltip">' +
			moduleItems +
			"</span>" +
			'<span class="hmt-stat"><strong>' +
			phase.stat +
			"</strong><em>" +
			phase.statLabel +
			"</em></span>" +
			"</button>"
		);
	}

	root.innerHTML =
		'<section class="hmt-root" aria-labelledby="hmt-title">' +
		'<div class="hmt-shell">' +
		'<p class="hmt-kicker">System-Vorschau</p>' +
		'<h3 id="hmt-title">WGOS in 3 Phasen</h3>' +
		'<p class="hmt-subtitle">Fundament aufbauen, Wachstum stabilisieren, Skalierung kontrolliert hochfahren.</p>' +
		'<div class="hmt-map" aria-label="WGOS Teaser Mindmap">' +
		phases.map(renderNode).join("") +
		"</div>" +
		'<a class="hmt-cta" href="' + wgosUrl + '">Das vollständige System →</a>' +
		"</div>" +
		"</section>";

	var nodes = root.querySelectorAll(".hmt-node");
	var activeId = null;

	Array.prototype.forEach.call(nodes, function (node, index) {
		setTimeout(function () {
			node.classList.add("is-mounted");
		}, index * 140 + 80);

		node.addEventListener("click", function () {
			var id = node.getAttribute("data-node-id");
			activeId = activeId === id ? null : id;

			Array.prototype.forEach.call(nodes, function (n) {
				n.classList.remove("is-active");
			});

			if (activeId) {
				node.classList.add("is-active");
			}
		});
	});
})();
