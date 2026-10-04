<?php
/** Admin-only preview transport spike. Public assistant waits for the SSE gate. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( [ 'config.php', 'sigv4.php', 'eventstream.php', 'bedrock-client.php', 'sse.php', 'budget.php', 'prompt.php', 'rest.php', 'spike.php' ] as $hu_chat_module ) {
	require_once __DIR__ . '/' . $hu_chat_module;
}
unset( $hu_chat_module );
