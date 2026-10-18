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

/**
 * Greeting and quick replies by page family. A chip either sends a message
 * to the assistant (`send`) or opens a link (`href`, booking or Marktcheck).
 *
 * @return array{greeting: string, chips: array<int, array<string, string>>}
 */
function hu_chat_intro( string $path ): array {
	$booking = hu_chat_booking_urls();
	if ( 0 === strpos( $path, '/whitelabel-retainer/' ) ) {
		return [
			'greeting' => 'Wie kann ich euch helfen? Kurz beraten oder direkt ein Fit-Gespräch?',
			'chips'    => [
				[ 'label' => 'Beratung', 'send' => 'Wir brauchen eine kurze Beratung.' ],
				[ 'label' => 'Fit-Gespräch buchen', 'href' => $booking['agency'], 'track' => 'chat_booking' ],
				[ 'label' => 'Ablauf', 'send' => 'Wie läuft ein erstes Projekt mit euch ab?' ],
			],
		];
	}
	$chips = [
		[ 'label' => 'Beratung', 'send' => 'Ich brauche eine kurze Beratung.' ],
		[ 'label' => 'Termin buchen', 'href' => $booking['general'], 'track' => 'chat_booking' ],
	];
	if ( preg_match( '#solar|waermepumpe|checkfox|aroundhome|leadgenerierung#', $path ) ) {
		$chips[] = [ 'label' => 'Marktcheck', 'href' => home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ), 'track' => 'chat_marktcheck' ];
	} else {
		$chips[] = [ 'label' => 'Preise', 'send' => 'Was kostet das bei Ihnen?' ];
	}
	return [
		'greeting' => 'Wie kann ich Ihnen helfen? Kurz beraten oder direkt ein Termin?',
		'chips'    => $chips,
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
		'privacy'  => home_url( '/datenschutz/#ki-chat' ),
		'page'     => $path,
		'intro'    => hu_chat_intro( $path ),
		'booking'  => array_values( array_unique( hu_chat_booking_urls() ) ),
		'contact'  => hu_get_contact_email(),
		'preview'  => 'preview' === hu_chat_config()['mode'],
	];
	?>
<div class="hu-chat" data-hu-chat="<?php echo esc_attr( wp_json_encode( $config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ); ?>">
	<button type="button" class="hu-chat__orb tafel" aria-label="KI-Assistent öffnen" aria-expanded="false" aria-controls="hu-chat-bar" data-track-action="chat_open" data-track-category="engagement" data-track-section="chat_orb">
		<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path d="M4.5 6.5A2 2 0 0 1 6.5 4.5h11a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H11l-4.5 3.5v-3.5h0a2 2 0 0 1-2-2z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8.5 9.5h7M8.5 12.5h4.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
	</button>
</div>
<style>
.hu-chat{position:fixed;right:24px;bottom:calc(24px + var(--hu-sticky-cta-h,0px));z-index:9001}
.hu-chat__orb.tafel{position:relative;display:grid;place-items:center;width:56px;height:56px;padding:0;border:1px solid var(--haar);border-radius:50%;color:var(--tinte);cursor:pointer;box-shadow:0 12px 28px -12px color-mix(in srgb,var(--papier) 80%,transparent);transition:transform var(--t-norm,.2s) var(--ease-aus,ease),border-color var(--t-norm,.2s) ease;animation:hu-orb-in .5s var(--ease-aus,ease) .6s both}
.hu-chat__orb::after{content:"";position:absolute;top:9px;right:9px;width:8px;height:8px;border-radius:50%;background:var(--stempel);box-shadow:0 0 0 2px var(--papier)}
.hu-chat__orb:hover{transform:translateY(-2px);border-color:var(--grau)}
.hu-chat__orb:focus-visible{outline:2px solid var(--stempel);outline-offset:4px}
.hu-chat.is-open .hu-chat__orb{opacity:0;pointer-events:none;transform:scale(.9)}
@keyframes hu-orb-in{from{opacity:0;transform:translateY(8px)}}
@media (prefers-reduced-motion:reduce){.hu-chat__orb.tafel{animation:none;transition:none}}
@media (max-width:560px){.hu-chat{right:16px;bottom:calc(16px + var(--hu-sticky-cta-h,0px))}}
@media print{.hu-chat{display:none}}
</style>
<script>
(()=>{const root=document.querySelector('[data-hu-chat]');if(!root)return;const cfg=JSON.parse(root.dataset.huChat);const orb=root.querySelector('.hu-chat__orb');const KEY='hu-chat-auto';let loading;
const seen=()=>{try{return sessionStorage.getItem(KEY)}catch(e){return '1'}};const mark=()=>{try{sessionStorage.setItem(KEY,'1')}catch(e){}};
const load=()=>loading||(loading=new Promise((ok,fail)=>{const l=document.createElement('link');l.rel='stylesheet';l.href=cfg.css;l.onload=ok;l.onerror=fail;document.head.appendChild(l)}).then(()=>import(cfg.js)).then(m=>m.mount(root,cfg)).catch(e=>{loading=null;throw e}));
const warm=()=>{load().catch(()=>{})};orb.addEventListener('pointerenter',warm,{once:true});orb.addEventListener('focus',warm,{once:true});
orb.addEventListener('click',()=>{mark();load().then(c=>c.open()).catch(()=>{location.href='mailto:'+cfg.contact})});
if(seen())return;let tick=0;const check=()=>{tick=0;const d=document.documentElement;if(d.scrollHeight<innerHeight*1.6)return;if((scrollY+innerHeight)/d.scrollHeight<.78)return;removeEventListener('scroll',onScroll);if(!root.classList.contains('is-open'))load().then(c=>c.open({auto:true})).catch(()=>{})};
const onScroll=()=>{if(!tick)tick=requestAnimationFrame(check)};addEventListener('scroll',onScroll,{passive:true});})();
</script>
	<?php
}
add_action( 'wp_footer', 'hu_chat_render_facade', 50 );
