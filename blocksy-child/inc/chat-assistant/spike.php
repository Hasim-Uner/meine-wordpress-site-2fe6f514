<?php
/** Admin-only, click-triggered preview probe. No public hooks or assets. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hu_chat_spike_admin_menu(): void {
	if ( ! hu_chat_spike_enabled() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	add_management_page( 'Chat: Streaming-Test', 'Chat: Streaming-Test', 'manage_options', 'hu-chat-spike', 'hu_chat_spike_admin_page' );
}
add_action( 'admin_menu', 'hu_chat_spike_admin_menu' );

function hu_chat_spike_admin_page(): void {
	if ( ! hu_chat_spike_enabled() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1>Chat: Streaming-Test</h1>
		<p>Nur Vorschau-Modus und eingeloggte Administratoren. Der Durchlauf ist auch auf der Live-Box möglich. Der Server sendet ausschließlich den festen Zähltest an Bedrock: <?php echo esc_html( HU_CHAT_SPIKE_PROMPT ); ?></p>
		<p>Das Ergebnis bleibt im Browser. Das heruntergeladene Protokoll enthält Zeitpunkte, Transportstatus und unkritische Antwort-Header, keine Zugangsdaten.</p>
		<p><label><input type="checkbox" id="hu-chat-padding" checked> Jede Sendung auf volle <?php echo esc_html( (string) hu_chat_flush_pad() ); ?>-Byte-Blöcke auffüllen (<code>HU_CHAT_FLUSH_PAD</code>; Text höchstens alle <?php echo esc_html( (string) HU_CHAT_FLUSH_INTERVAL_MS ); ?> ms). Ohne Haken zum Vergleich ungepolstert.</label></p>
		<p><button type="button" class="button button-primary" id="hu-chat-spike">Streaming testen</button>
		<button type="button" class="button" id="hu-chat-report" disabled>Protokoll herunterladen</button></p>
		<pre id="hu-chat-result" role="status" aria-live="polite"></pre>
	</div>
	<script>
	(() => {
		const trigger = document.getElementById('hu-chat-spike');
		const download = document.getElementById('hu-chat-report');
		const output = document.getElementById('hu-chat-result');
		const padding = document.getElementById('hu-chat-padding');
		const endpoint = <?php echo wp_json_encode( rest_url( 'nexus/v1/chat' ) ); ?>;
		// Transport evidence only; anything that could carry a credential stays out.
		const named = /^(content-encoding|content-length|transfer-encoding|server|via|vary|x-cache)$/;
		const secret = /nonce|token|auth|key|secret|session|cookie/i;
		let report;
		trigger.addEventListener('click', async () => {
			trigger.disabled = true;
			download.disabled = true;
			output.textContent = 'Streaming-Test läuft …';
			const started = performance.now();
			const controller = new AbortController();
			const timer = setTimeout(() => controller.abort(), 80000);
			const variant = padding.checked ? 'padded' : 'plain';
			report = { tested_at: new Date().toISOString(), variant, status: 'unresolved', headers: {}, chunks: [], events: [] };
			try {
				const response = await fetch(endpoint, {
					method: 'POST', credentials: 'same-origin', cache: 'no-store', signal: controller.signal,
					headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': <?php echo wp_json_encode( wp_create_nonce( 'wp_rest' ) ); ?> },
					body: variant === 'padded' ? '{"padding":true}' : '{}'
				});
				report.http_status = response.status;
				report.content_type = response.headers.get('Content-Type');
				report.cache_control = response.headers.get('Cache-Control');
				response.headers.forEach((value, name) => {
					if ((named.test(name) || name.startsWith('x-')) && !secret.test(name)) report.headers[name] = value;
				});
				if (!response.ok || !response.body || !report.content_type.startsWith('text/event-stream')) throw new Error('unavailable');
				const reader = response.body.getReader();
				const decoder = new TextDecoder();
				let buffer = '';
				while (true) {
					const { value, done } = await reader.read();
					if (done) break;
					const arrival = Math.round(performance.now() - started);
					report.chunks.push({ arrival_ms: arrival, bytes: value.length });
					buffer += decoder.decode(value, { stream: true });
					let boundary;
					while ((boundary = buffer.indexOf('\n\n')) !== -1) {
						const frame = buffer.slice(0, boundary);
						buffer = buffer.slice(boundary + 2);
						const event = /^event: (.+)$/m.exec(frame)?.[1];
						const data = JSON.parse(/^data: (.+)$/m.exec(frame)?.[1] || '{}');
						report.events.push({ event, stage: data.stage, variant: data.variant, flush_pad: data.flush_pad, transport: data.transport, server_ms: data.elapsed_ms, arrival_ms: arrival });
						output.textContent = JSON.stringify(report, null, 2);
					}
				}
				const ready = report.events.find(item => item.stage === 'ready');
				const finish = report.events.find(item => item.event === 'done');
				const texts = report.events.filter(item => item.event === 'text');
				// Resource Timing is queued after the body ends; encoded vs decoded size shows compression.
				await new Promise(resolve => setTimeout(resolve, 50));
				const timing = performance.getEntriesByName(new URL(endpoint, location.href).href).pop();
				report.summary = {
					flush_pad: ready?.flush_pad,
					chunks: report.chunks.length,
					text_events: texts.length,
					text_arrivals_distinct: new Set(texts.map(item => item.arrival_ms)).size,
					ready_arrival_ms: ready?.arrival_ms,
					first_text_arrival_ms: texts[0]?.arrival_ms,
					done_server_ms: finish?.server_ms,
					done_arrival_ms: finish?.arrival_ms,
					next_hop_protocol: timing?.nextHopProtocol,
					encoded_body_size: timing?.encodedBodySize,
					decoded_body_size: timing?.decodedBodySize,
				};
				if (!finish || report.events.some(item => item.event === 'error')) report.status = 'failed';
				else if (finish.transport !== 'stream') report.status = 'fallback_only';
				else if (ready && finish.server_ms >= 300 && finish.arrival_ms - ready.arrival_ms >= 200 && report.summary.text_arrivals_distinct >= 2) report.status = 'incremental_delivery_observed';
				else report.status = 'buffering_or_timing_unresolved';
			} catch (error) {
				report.status = 'failed';
			} finally {
				clearTimeout(timer);
				trigger.disabled = false;
				download.disabled = false;
				output.textContent = JSON.stringify(report, null, 2);
			}
		});
		download.addEventListener('click', () => {
			const url = URL.createObjectURL(new Blob([JSON.stringify(report, null, 2)], { type: 'application/json' }));
			const link = document.createElement('a');
			link.href = url;
			link.download = 'chat-live-stream-' + report.variant + '.json';
			link.click();
			setTimeout(() => URL.revokeObjectURL(url), 1000);
		});
	})();
	</script>
	<?php
}
