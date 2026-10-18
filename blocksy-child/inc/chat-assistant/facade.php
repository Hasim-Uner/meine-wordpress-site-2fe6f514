<?php
/**
 * Chat facade: one inline button in the footer, no network request before the
 * first click. The click (or hover/focus as preload) loads chat-assistant.css
 * and the chat-assistant.js module. Visitors in `preview` mode: administrators
 * only; `live`: everyone.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Routes where the assistant never appears. */
function hu_chat_facade_allowed_here(): bool {
	if ( is_admin() || is_customize_preview() || is_feed() || is_embed() || wp_doing_ajax() ) {
		return false;
	}
	if ( is_page( [ 'datenschutz', 'impressum' ] ) ) {
		return false;
	}
	if ( function_exists( 'hu_get_noindex_nofollow_slugs' ) && is_page( hu_get_noindex_nofollow_slugs() ) ) {
		return false;
	}
	if ( function_exists( 'hu_get_noindex_nofollow_templates' ) && is_page_template( hu_get_noindex_nofollow_templates() ) ) {
		return false;
	}
	return true;
}

/** Three starter questions by page family. @return string[] */
function hu_chat_starter_questions( string $path ): array {
	if ( 0 === strpos( $path, '/whitelabel-retainer/' ) ) {
		return [
			'Wie läuft ein erstes Projekt mit euch ab?',
			'Was kostet White-Label-Umsetzung?',
			'Wer hat die Zugänge und den Kundenkontakt?',
		];
	}
	if ( preg_match( '#solar|waermepumpe|checkfox|aroundhome|leadgenerierung#', $path ) ) {
		return [
			'Lohnt sich ein eigenes Anfragesystem statt Portal-Leads?',
			'Was ist der Marktcheck?',
			'Was kostet ein Anfragesystem?',
		];
	}
	return [
		'Was kostet eine Website bei Ihnen?',
		'Brauche ich Server-Side Tracking?',
		'Wie läuft ein Projekt ab?',
	];
}

function hu_chat_asset_url( string $relative ): string {
	$path    = get_stylesheet_directory() . '/assets/' . $relative;
	$version = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $path ) : (string) filemtime( $path );
	return add_query_arg( 'ver', $version, get_stylesheet_directory_uri() . '/assets/' . $relative );
}

function hu_chat_render_facade(): void {
	if ( ! hu_chat_visitor_allowed() || ! hu_chat_facade_allowed_here() ) {
		return;
	}
	$path   = (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '/' ), PHP_URL_PATH );
	$path   = hu_chat_normalize_page( '' !== $path ? $path : '/' );
	$config = [
		'endpoint' => rest_url( 'nexus/v1/chat/messages' ),
		'handover' => rest_url( 'nexus/v1/chat/handover' ),
		// Cookie requests need the REST nonce; anonymous pages stay cacheable without one.
		'nonce'    => is_user_logged_in() ? wp_create_nonce( 'wp_rest' ) : '',
		'css'      => hu_chat_asset_url( 'css/chat-assistant.css' ),
		'js'       => hu_chat_asset_url( 'js/chat-assistant.js' ),
		'privacy'  => home_url( '/datenschutz/' ),
		'page'     => $path,
		'starters' => hu_chat_starter_questions( $path ),
		'contact'  => hu_get_contact_email(),
		'preview'  => 'preview' === hu_chat_config()['mode'],
	];
	?>
<div class="hu-chat" data-hu-chat="<?php echo esc_attr( wp_json_encode( $config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ); ?>">
	<button type="button" class="hu-chat__orb tafel" aria-label="KI-Assistent öffnen" aria-expanded="false" aria-controls="hu-chat-panel" data-track-action="chat_open" data-track-category="engagement" data-track-section="chat_orb">
		<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v8a2.5 2.5 0 0 1-2.5 2.5H10l-4.2 3.6c-.5.4-1.3.1-1.3-.6V16A2.5 2.5 0 0 1 4 13.5z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="8.5" cy="9.5" r="1.1" fill="currentColor"/><circle cx="12" cy="9.5" r="1.1" fill="currentColor"/><circle cx="15.5" cy="9.5" r="1.1" fill="currentColor"/></svg>
		<span class="hu-chat__label">Fragen?</span>
	</button>
</div>
<style>
.hu-chat{position:fixed;right:16px;bottom:calc(16px + var(--hu-sticky-cta-h,0px));z-index:9001}
.hu-chat__orb.tafel{display:inline-flex;align-items:center;gap:8px;min-height:48px;padding:0 16px 0 13px;border:1px solid var(--haar);border-radius:var(--r1);font-family:var(--serif);font-size:15px;font-weight:600;line-height:1;cursor:pointer;transition:transform .2s ease}
.hu-chat__orb svg{color:var(--stempel);flex:none}
.hu-chat__orb:hover{transform:translateY(-2px)}
.hu-chat__orb:focus-visible{outline:2px solid var(--stempel);outline-offset:3px}
.hu-chat.is-open .hu-chat__orb{opacity:0;pointer-events:none}
@media (max-width:480px){.hu-chat__label{display:none}.hu-chat__orb.tafel{padding:0;width:52px;min-height:52px;justify-content:center}}
@media (prefers-reduced-motion:reduce){.hu-chat__orb.tafel{transition:none}.hu-chat__orb:hover{transform:none}}
@media print{.hu-chat{display:none}}
</style>
<script>
(()=>{const root=document.querySelector('[data-hu-chat]');if(!root)return;const cfg=JSON.parse(root.dataset.huChat);const orb=root.querySelector('.hu-chat__orb');let loading;
const load=()=>loading||(loading=new Promise((ok,fail)=>{const l=document.createElement('link');l.rel='stylesheet';l.href=cfg.css;l.onload=ok;l.onerror=fail;document.head.appendChild(l)}).then(()=>import(cfg.js)).then(m=>m.mount(root,cfg)).catch(e=>{loading=null;throw e}));
const warm=()=>{load().catch(()=>{})};orb.addEventListener('pointerenter',warm,{once:true});orb.addEventListener('focus',warm,{once:true});
orb.addEventListener('click',()=>{load().then(chat=>chat.open()).catch(()=>{location.href='mailto:'+cfg.contact})});})();
</script>
	<?php
}
add_action( 'wp_footer', 'hu_chat_render_facade', 50 );
