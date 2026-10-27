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
			'greeting' => 'Was habt ihr vor?',
			'chips'    => [
				[ 'label' => 'Projekt besprechen', 'send' => 'Wir brauchen eine kurze Beratung.' ],
				[ 'label' => 'Fit-Gespräch buchen', 'href' => $booking['agency'], 'track' => 'chat_booking' ],
				[ 'label' => 'Ablauf', 'send' => 'Wie läuft ein erstes Projekt mit euch ab?' ],
			],
		];
	}
	$chips = [
		[ 'label' => 'Projekt besprechen', 'send' => 'Ich brauche eine kurze Beratung.' ],
	];
	if ( preg_match( '#solar|waermepumpe|checkfox|aroundhome|leadgenerierung#', $path ) ) {
		$chips[] = [ 'label' => 'Marktcheck', 'href' => home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ), 'track' => 'chat_marktcheck' ];
	} else {
		$chips[] = [ 'label' => 'Kosten einordnen', 'send' => 'Was kostet das bei Ihnen?' ];
	}
	$chips[] = [ 'label' => 'Termin vereinbaren', 'href' => $booking['general'], 'track' => 'chat_booking' ];
	return [
		'greeting' => 'Was haben Sie vor?',
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
<div class="hu-chat" hidden data-hu-chat="<?php echo esc_attr( wp_json_encode( $config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ); ?>">
	<div class="hu-chat__hint tafel tafel--chat" hidden>
		<button type="button" class="hu-chat__hint-open" data-track-action="chat_open" data-track-category="engagement" data-track-section="chat_footer_hint">Noch eine Frage zum Projekt?<span>KI-Assistent öffnen ↗</span></button>
		<button type="button" class="hu-chat__hint-close" aria-label="Chat-Hinweis schließen">×</button>
	</div>
	<button type="button" class="hu-chat__orb tafel tafel--chat" aria-label="KI-Assistent öffnen" aria-expanded="false" aria-controls="hu-chat-bar" data-track-action="chat_open" data-track-category="engagement" data-track-section="chat_orb">
		<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path d="M4.5 6.5A2 2 0 0 1 6.5 4.5h11a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H11l-4.5 3.5v-3.5h0a2 2 0 0 1-2-2z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8.5 9.5h7M8.5 12.5h4.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
		<span>KI · Frage zum Projekt?</span>
	</button>
</div>
<style>
.hu-chat{position:fixed;right:max(var(--s2),env(safe-area-inset-right));bottom:calc(max(var(--s2),env(safe-area-inset-bottom)) + max(var(--hu-sticky-cta-h,0px),var(--hu-chat-keyboard,0px)));z-index:9001;font-family:var(--serif)}
.hu-chat__orb.tafel{position:relative;display:flex;align-items:center;gap:var(--s1);min-height:48px;max-width:calc(100vw - 2 * var(--s2));padding:var(--s1) var(--s2);border:1px solid var(--haar);border-radius:var(--r1);color:var(--tinte);font-family:var(--serif);font-size:14px;line-height:1.4;cursor:pointer;box-shadow:0 12px 28px -12px color-mix(in srgb,var(--papier) 80%,transparent);transition:transform var(--t-norm,.2s) var(--ease-aus,ease),border-color var(--t-norm,.2s) ease}
.hu-chat__orb svg{flex:none;color:var(--stempel)}
.hu-chat__orb:hover{transform:translateY(-2px);border-color:var(--grau)}
.hu-chat button:focus-visible{outline:2px solid var(--stempel);outline-offset:4px}
.hu-chat.is-open .hu-chat__orb,.hu-chat [hidden]{display:none!important}
.hu-chat__hint.tafel{position:absolute;right:0;bottom:calc(100% + var(--s1));display:flex;align-items:flex-start;width:min(290px,calc(100vw - 2 * var(--s2)));padding:var(--s1);border:1px solid var(--haar);border-radius:var(--r1);box-shadow:0 12px 28px -12px color-mix(in srgb,var(--papier) 80%,transparent)}
.hu-chat__hint button{appearance:none;border:0;background:transparent;color:var(--tinte);font:inherit;cursor:pointer}
.hu-chat__hint button.hu-chat__hint-open{flex:1;padding:var(--s0);text-align:left;font-size:14px;line-height:1.5}
.hu-chat__hint-open span{display:block;color:var(--stempel);font-size:12px}
.hu-chat__hint-close{display:grid;place-items:center;min-width:44px;min-height:44px;padding:0}
@media (prefers-reduced-motion:reduce){.hu-chat__orb.tafel{animation:none;transition:none}}
@media print{.hu-chat{display:none}}
</style>
<script>
(()=>{const root=document.querySelector('[data-hu-chat]');if(!root)return;const cfg=JSON.parse(root.dataset.huChat);root.hidden=false;const orb=root.querySelector('.hu-chat__orb');const hint=root.querySelector('.hu-chat__hint');const KEY='hu-chat-auto';let loading;
const seen=()=>{try{return sessionStorage.getItem(KEY)}catch(e){return '1'}};const mark=()=>{try{sessionStorage.setItem(KEY,'1')}catch(e){}};
const load=()=>loading||(loading=new Promise((ok,fail)=>{const l=document.createElement('link');l.rel='stylesheet';l.href=cfg.css;l.onload=ok;l.onerror=fail;document.head.appendChild(l)}).then(()=>import(cfg.js)).then(m=>m.mount(root,cfg)).catch(e=>{loading=null;throw e}));
const warm=()=>{load().catch(()=>{})};orb.addEventListener('pointerenter',warm,{once:true});orb.addEventListener('focus',warm,{once:true});
const open=()=>{mark();hint.hidden=true;load().then(c=>c.open()).catch(()=>{location.href='mailto:'+cfg.contact})};
orb.addEventListener('click',open);hint.querySelector('.hu-chat__hint-open').addEventListener('click',open);
hint.querySelector('.hu-chat__hint-close').addEventListener('click',()=>{mark();hint.hidden=true;orb.focus({preventScroll:true})});
// The footer offers help; scrolling never opens the chat or takes focus.
const footer=document.querySelector('footer.fuss, #colophon, footer[role="contentinfo"]');
if(seen()||!footer||!('IntersectionObserver' in window))return;let footerVisible=false;
const offer=()=>{if(!footerVisible||seen()||root.classList.contains('is-open'))return;if(document.activeElement?.closest('input,textarea,select,[contenteditable="true"]'))return;mark();hint.hidden=false};
const observer=new IntersectionObserver(entries=>{footerVisible=entries.some(entry=>entry.isIntersecting);if(!footerVisible)hint.hidden=true;else offer()});
observer.observe(footer);document.addEventListener('focusout',()=>requestAnimationFrame(offer));})();
</script>
	<?php
}
add_action( 'wp_footer', 'hu_chat_render_facade', 50 );
