<?php
/**
 * KI-Assistent. Standard `off`; `preview` zeigt ihn nur eingeloggten Admins,
 * `live` allen Besuchern. Der Admin-Streaming-Test bleibt auf `preview`.
 * Contract: docs/architecture/CHAT_ASSISTANT.md.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( [ 'config.php', 'sigv4.php', 'eventstream.php', 'bedrock-client.php', 'sse.php', 'budget.php', 'prompt.php', 'knowledge.php', 'rest.php', 'spike.php', 'conversation.php', 'handover.php', 'facade.php', 'admin.php' ] as $hu_chat_module ) {
	require_once __DIR__ . '/' . $hu_chat_module;
}
unset( $hu_chat_module );
