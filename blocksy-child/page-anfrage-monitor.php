<?php
/**
 * Virtuelle Route /anfrage-monitor/ (inc/anfrage-monitor.php).
 *
 * Zeigt oeffentlich, ob die taegliche Testanfrage ankommt. Serverseitig
 * gerendert, ohne JavaScript, ohne personenbezogene Daten.
 *
 * @package Blocksy_Child
 */

get_header();

$hu_mon_status  = function_exists( 'hu_monitor_cached_status' ) ? hu_monitor_cached_status() : [];
$hu_mon_enabled = ! empty( $hu_mon_status['enabled'] );
$hu_mon_glyphs  = [
	'ok'   => '✓',
	'warn' => '!',
	'fail' => '✕',
	'none' => '–',
];
$hu_mon_words   = [
	'ok'   => 'in Ordnung',
	'warn' => 'Achtung',
	'fail' => 'Störung',
	'none' => 'keine Aussage',
];

$hu_mon_lights = [];
if ( $hu_mon_enabled ) {
	$inquiries = (array) $hu_mon_status['anfragen'];
	$updates   = (array) $hu_mon_status['aktuell'];
	$silence   = (array) $hu_mon_status['stille'];

	$inquiry_meta = '' !== hu_monitor_human_time( $inquiries['at'] ?? null ) ? 'Letzter Lauf: ' . hu_monitor_human_time( $inquiries['at'] ?? null ) : '';
	if ( ! empty( $inquiries['failedAt'] ) ) {
		$inquiry_meta .= ' · Abbruch bei: ' . hu_monitor_check_label( $inquiries['failedAt'] );
	}

	$hu_mon_lights = [
		[
			'title' => 'Anfragen kommen an',
			'state' => (string) $inquiries['state'],
			'label' => (string) $inquiries['label'],
			'meta'  => $inquiry_meta,
		],
		[
			'title' => 'Website ist aktuell',
			'state' => (string) $updates['state'],
			'label' => (string) $updates['label'],
			'meta'  => ! empty( $updates['days'] ) ? 'Offen seit ' . (int) $updates['days'] . ' Tagen' : '',
		],
		[
			'title' => 'Keine ungewöhnliche Stille',
			'state' => (string) $silence['state'],
			'label' => (string) $silence['label'],
			'meta'  => isset( $silence['threshold'] ) ? 'Alarm ab ' . (int) $silence['threshold'] . ' Tagen ohne Anfrage' : 'Ab sechs Anfragen in 180 Tagen',
		],
	];
}
?>
<div class="doku anfrage-monitor" data-track-section="anfrage_monitor">
	<div class="anfrage-monitor__shell">
		<header class="anfrage-monitor__kopf">
			<p class="anfrage-monitor__kicker">Anfrage-Monitor · hasimuener.de</p>
			<h1>Kommen Anfragen über diese Website an?</h1>
			<p class="anfrage-monitor__lead">Jeden Morgen läuft eine echte Testanfrage durch das Kontaktformular dieser Website. Geprüft wird, ob sie ankommt: im Postfach, im CRM und im Anfrageprotokoll.</p>
		</header>

		<?php if ( ! $hu_mon_enabled ) : ?>
			<section class="tafel tafel--graphit anfrage-monitor__geraet" aria-label="Status">
				<p class="anfrage-monitor__leer">Der Monitor ist noch nicht eingerichtet.</p>
			</section>
		<?php else : ?>
			<section class="tafel tafel--graphit anfrage-monitor__geraet" aria-labelledby="anfrage-monitor-ampeln">
				<h2 id="anfrage-monitor-ampeln" class="anfrage-monitor__geraet-titel">Stand <?php echo esc_html( hu_monitor_human_time( $hu_mon_status['generatedAt'] ?? null ) ); ?></h2>
				<ul class="anfrage-monitor__ampeln">
					<?php foreach ( $hu_mon_lights as $light ) : ?>
						<?php $state = isset( $hu_mon_glyphs[ $light['state'] ] ) ? $light['state'] : 'none'; ?>
						<li class="anfrage-monitor__ampel" data-state="<?php echo esc_attr( $state ); ?>">
							<span class="anfrage-monitor__zeichen" aria-hidden="true"><?php echo esc_html( $hu_mon_glyphs[ $state ] ); ?></span>
							<span class="anfrage-monitor__text">
								<span class="anfrage-monitor__titel"><?php echo esc_html( $light['title'] ); ?><span class="screen-reader-text">: <?php echo esc_html( $hu_mon_words[ $state ] ); ?></span></span>
								<span class="anfrage-monitor__wert"><?php echo esc_html( $light['label'] ); ?></span>
								<?php if ( '' !== $light['meta'] ) : ?>
									<span class="anfrage-monitor__meta"><?php echo esc_html( $light['meta'] ); ?></span>
								<?php endif; ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>

			<section class="anfrage-monitor__verlauf" aria-labelledby="anfrage-monitor-verlauf">
				<h2 id="anfrage-monitor-verlauf">Die letzten Läufe</h2>
				<?php if ( empty( $hu_mon_status['verlauf'] ) ) : ?>
					<p class="anfrage-monitor__hinweis">Noch kein Lauf aufgezeichnet.</p>
				<?php else : ?>
					<ol class="anfrage-monitor__punkte">
						<?php foreach ( array_reverse( (array) $hu_mon_status['verlauf'] ) as $run ) : ?>
							<?php
							$run_ok    = ! empty( $run['ok'] );
							$run_label = hu_monitor_human_time( $run['datum'] ?? null ) . ': ' . ( $run_ok ? 'angekommen' : 'Störung bei ' . hu_monitor_check_label( $run['failedAt'] ?? null ) );
							?>
							<li class="anfrage-monitor__punkt" data-ok="<?php echo $run_ok ? '1' : '0'; ?>" title="<?php echo esc_attr( $run_label ); ?>"><span class="screen-reader-text"><?php echo esc_html( $run_label ); ?></span></li>
						<?php endforeach; ?>
					</ol>
					<p class="anfrage-monitor__hinweis">Älteste links, neueste rechts. Ein Kreuz markiert einen Lauf, bei dem die Testanfrage nicht vollständig ankam.</p>
				<?php endif; ?>
			</section>
		<?php endif; ?>

		<section class="anfrage-monitor__pruefung" aria-labelledby="anfrage-monitor-pruefung">
			<h2 id="anfrage-monitor-pruefung">Was jeden Morgen geprüft wird</h2>
			<ol class="anfrage-monitor__schritte">
				<?php foreach ( [ 'pageLoad', 'formSubmit', 'uiSuccess', 'crmStored', 'eventRecorded', 'mailAdmin', 'mailConfirmation' ] as $check ) : ?>
					<li><?php echo esc_html( hu_monitor_check_label( $check ) ); ?></li>
				<?php endforeach; ?>
			</ol>
			<p class="anfrage-monitor__hinweis">Die Testanfrage ist markiert: Sie zählt in keiner Auswertung und landet archiviert im CRM. Die Stille-Ampel zählt nur echte Anfragen und meldet sich, wenn ungewöhnlich lange keine kommt.</p>
		</section>
	</div>
</div>
<?php
get_footer();
