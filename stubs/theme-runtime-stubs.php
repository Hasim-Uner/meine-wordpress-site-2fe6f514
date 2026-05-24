<?php

define( 'ABSPATH', '/var/www/html/' );
define( 'WPINC', 'wp-includes' );
define( 'WP_DEBUG', false );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'DAY_IN_SECONDS', 86400 );

/**
 * Blocksy parent theme hook used by the child footer template.
 *
 * @return void
 */
function blocksy_after_current_template() {}

/**
 * Minimal PHPMailer runtime shape exposed by WordPress during mail setup.
 */
class PHPMailer {
	/** @var string */
	public $Host;

	/** @var int|string */
	public $Port;

	/** @var bool */
	public $SMTPAuth;

	/** @var string */
	public $Username;

	/** @var string */
	public $Password;

	/** @var string */
	public $CharSet;

	/** @var bool */
	public $SMTPAutoTLS;

	/** @var string */
	public $SMTPSecure;

	/** @var string */
	public $Sender;

	/**
	 * @return void
	 */
	public function isSMTP() {}

	/**
	 * @param string $address
	 * @param string $name
	 * @param bool   $auto
	 * @return void
	 */
	public function setFrom( $address, $name = '', $auto = true ) {}
}
