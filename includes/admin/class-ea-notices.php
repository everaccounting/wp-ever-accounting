<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Notices {
	/**
	 * The single instance of the class.
	 *
	 * @var EAccounting_Admin_Notices
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main EverAccounting Instance.
	 *
	 * Ensures only one instance of EverAccounting is loaded or can be loaded.
	 *
	 * @return EAccounting_Admin_Notices - Main instance.
	 * @since 1.0.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


	/**
	 * EAccounting_Notice constructor.
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'admin_notices' ), 99 );
		//add_action( 'shutdown', array( $this, 'shutdown' ), 99 );
	}

	/**
	 * Show success message
	 *
	 * @param $message
	 *
	 * @since 1.0.0
	 */
	public function success( $message ) {
		$this->add( $message, 'warning' );
	}

	/**
	 * Show info message
	 *
	 * @param $message
	 *
	 * @since 1.0.0
	 */
	public function info( $message ) {
		$this->add( $message, 'info' );
	}

	/**
	 * show error message
	 *
	 * @param $message
	 *
	 * @since 1.0.0
	 */
	public function error( $message ) {
		$this->add( $message, 'error' );
	}


	/**
	 * since 1.0.0
	 *
	 * @param $notice
	 * @param string $type
	 * @param bool $dismissible
	 */
	public function add( $notice, $type = 'success', $dismissible = true ) {
		$notices          = get_option( '_eaccounting_admin_notices', array() );
		$dismissible_text = ( $dismissible ) ? "is-dismissible" : "";
		array_push( $notices, array(
			"notice"      => wp_kses( $notice, array(
				'strong' => array(),
				'span'   => array( 'class' => true ),
				'i'      => array( 'class' => true ),
				'a'      => array( 'class' => true, 'href' => true ),
			) ),
			"type"        => $type,
			"dismissible" => $dismissible_text
		) );

		update_option( "_eaccounting_admin_notices", $notices );
	}

	/**
	 * since 1.0.0
	 */
	public function admin_notices() {

		$notices = get_option( '_eaccounting_admin_notices', array() );

		foreach ( $notices as $notice ) {
			echo sprintf( '<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
				$notice['type'],
				$notice['dismissible'],
				$notice['notice']
			);
		}

	}

	public function shutdown() {
		$notices = get_option( '_eaccounting_admin_notices', array() );
		if ( ! empty( $notices ) ) {
			update_option( '_eaccounting_admin_notices', array() );
		}
	}
}
