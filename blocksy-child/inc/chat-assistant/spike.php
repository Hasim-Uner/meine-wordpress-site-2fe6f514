<?php
/** Admin-only, click-triggered staging probe. No public hooks or assets. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hu_chat_spike_admin_menu(): void {
	add_management_page( 'Chat: Streaming-Test', 'Chat: Streaming-Test', 'manage_options', 'hu-chat-spike', 'hu_chat_spike_admin_page' );
}
add_action( 'admin_menu', 'hu_chat_spike_admin_menu' );

function hu_chat_spike_admin_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$ready = 'staging' === wp_get_environment_type() && hu_chat_config_valid( hu_chat_config() ) && function_exists( 'curl_init' );
	?>
	<div class="wrap">
		<h1>Chat: Streaming-Test</h1>
		<p>Nur Staging, Vorschau-Modus und eingeloggte Administratoren. Der Server sendet ausschließlich einen festen Hallo-Test an Bedrock.</p>
		<p>Das Ergebnis bleibt im Browser. Das heruntergeladene Protokoll enthält Zeitpunkte und Transportstatus, keine Zugangsdaten.</p>
		<p><button type="button" class="button button-primary" id="hu-chat-spike" <?php disabled( ! $ready ); ?>>Streaming testen</button>
		<button type="button" class="button" id="hu-chat-report" disabled>Protokoll herunterladen</button></p>
		<?php if ( ! $ready ) : ?>
			<p>Voraussetzungen fehlen: Staging-Umgebung, Vorschau-Modus, Bedrock-Konfiguration, geprüfte Tokenpreise oder cURL.</p>
		<?php endif; ?>
		<pre id="hu-chat-result" role="status" aria-live="polite"></pre>
	</div>
	<script>
	(() => {
		const trigger = document.getElementById('hu-chat-spike');
		const download = document.getElementById('hu-chat-report');
		const output = document.getElementById('hu-chat-result');
		let report;
		trigger.addEventListener('click', async () => {
			trigger.disabled = true;
			download.disabled = true;
			output.textContent = 'Streaming-Test läuft …';
			const started = performance.now();
			const controller = new AbortController();
			const timer = setTimeout(() => controller.abort(), 80000);
			report = { tested_at: new Date().toISOString(), status: 'unresolved', chunks: [], events: [] };
			try {
				const response = await fetch(<?php echo wp_json_encode( rest_url( 'nexus/v1/chat' ) ); ?>, {
					method: 'POST', credentials: 'same-origin', cache: 'no-store', signal: controller.signal,
					headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': <?php echo wp_json_encode( wp_create_nonce( 'wp_rest' ) ); ?> },
					body: '{}'
				});
				report.http_status = response.status;
				report.content_type = response.headers.get('Content-Type');
				report.cache_control = response.headers.get('Cache-Control');
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
						report.events.push({ event, stage: data.stage, transport: data.transport, server_ms: data.elapsed_ms, arrival_ms: arrival });
						output.textContent = JSON.stringify(report, null, 2);
					}
				}
				const ready = report.events.find(item => item.stage === 'ready');
				const finish = report.events.find(item => item.event === 'done');
				if (!finish || report.events.some(item => item.event === 'error')) report.status = 'failed';
				else if (finish.transport !== 'stream') report.status = 'fallback_only';
				else if (ready && finish.server_ms >= 300 && finish.arrival_ms - ready.arrival_ms >= 200) report.status = 'incremental_delivery_observed';
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
			link.download = 'chat-staging-stream.json';
			link.click();
			setTimeout(() => URL.revokeObjectURL(url), 1000);
		});
	})();
	</script>
	<?php
}
