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
		'privacy'  => home_url( '/datenschutz/' ),
		'page'     => $path,
		'intro'    => hu_chat_intro( $path ),
		'booking'  => array_values( array_unique( hu_chat_booking_urls() ) ),
		'contact'  => hu_get_contact_email(),
		'preview'  => 'preview' === hu_chat_config()['mode'],
	];
	?>
<div class="hu-chat" data-hu-chat="<?php echo esc_attr( wp_json_encode( $config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ); ?>">
	<button type="button" class="hu-chat__orb tafel" aria-label="KI-Assistent öffnen" aria-expanded="false" aria-controls="hu-chat-bar" data-track-action="chat_open" data-track-category="engagement" data-track-section="chat_orb">
		<svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true" focusable="false"><path d="M12 2.5l1.9 5.6 5.6 1.9-5.6 1.9L12 17.5l-1.9-5.6L4.5 10l5.6-1.9z" fill="currentColor"/><path d="M18.5 15l.8 2.2 2.2.8-2.2.8-.8 2.2-.8-2.2-2.2-.8 2.2-.8z" fill="currentColor" opacity=".7"/></svg>
	</button>
</div>
<style>
.hu-chat{position:fixed;right:20px;bottom:calc(20px + var(--hu-sticky-cta-h,0px));z-index:9001}
.hu-chat__orb.tafel{position:relative;display:grid;place-items:center;width:60px;height:60px;padding:0;border:0;border-radius:50%;cursor:pointer;transition:transform var(--t-norm,.2s) var(--ease-aus,ease)}
.hu-chat__orb::before{content:"";position:absolute;inset:-3px;border-radius:50%;background:conic-gradient(from 0deg,var(--stempel),transparent 35%,transparent 65%,var(--stempel));-webkit-mask:radial-gradient(closest-side,transparent calc(100% - 3px),#000 calc(100% - 2px));mask:radial-gradient(closest-side,transparent calc(100% - 3px),#000 calc(100% - 2px));animation:hu-orb 4s linear infinite}
.hu-chat__orb svg{color:var(--stempel)}
.hu-chat__orb:hover{transform:scale(1.06)}
.hu-chat__orb:focus-visible{outline:2px solid var(--stempel);outline-offset:4px}
.hu-chat.is-open .hu-chat__orb{opacity:0;pointer-events:none;transform:scale(.6)}
@keyframes hu-orb{to{transform:rotate(1turn)}}
@media (prefers-reduced-motion:reduce){.hu-chat__orb::before{animation:none}.hu-chat__orb.tafel{transition:none}}
@media print{.hu-chat{display:none}}
</style>
<script>
(()=>{const root=document.querySelector('[data-hu-chat]');if(!root)return;const cfg=JSON.parse(root.dataset.huChat);const orb=root.querySelector('.hu-chat__orb');const KEY='hu-chat-auto';let loading;
const seen=()=>{try{return sessionStorage.getItem(KEY)}catch(e){return '1'}};const mark=()=>{try{sessionStorage.setItem(KEY,'1')}catch(e){}};
const load=()=>loading||(loading=new Promise((ok,fail)=>{const l=document.createElement('link');l.rel='stylesheet';l.href=cfg.css;l.onload=ok;l.onerror=fail;document.head.appendChild(l)}).then(()=>import(cfg.js)).then(m=>m.mount(root,cfg)).catch(e=>{loading=null;throw e}));
const warm=()=>{load().catch(()=>{})};orb.addEventListener('pointerenter',warm,{once:true});orb.addEventListener('focus',warm,{once:true});
orb.addEventListener('click',()=>{mark();load().then(c=>c.open()).catch(()=>{location.href='mailto:'+cfg.contact})});
if(seen())return;let tick=0;const check=()=>{tick=0;const d=document.documentElement;if(d.scrollHeight<innerHeight*1.6)return;if((scrollY+innerHeight)/d.scrollHeight<.78)return;removeEventListener('scroll',onScroll);mark();if(!root.classList.contains('is-open'))load().then(c=>c.open({auto:true})).catch(()=>{})};
const onScroll=()=>{if(!tick)tick=requestAnimationFrame(check)};addEventListener('scroll',onScroll,{passive:true});})();
</script>
	<?php
}
add_action( 'wp_footer', 'hu_chat_render_facade', 50 );
