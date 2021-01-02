<?php
/**
 * Display notices in admin
 *
 * @package EverAccounting\Admin
 * @version 1.0.2
 */

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

class Admin_Notice {
	/**
	 * All notices.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	private static $notices = array();

	/**
	 * Constructor.
	 */
	public static function init() {
		self::$notices = get_option( 'ea_admin_notices', array() );
		add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );
		if ( current_user_can( 'manage_eaccounting' ) ) {
			//add_action( 'admin_print_styles', array( __CLASS__, 'add_notices' ) );
		}
	}

	/**
	 * Displays a success notice
	 *
	 * @param       string $msg The message to qeue.
	 * @access      public
	 * @since       1.0.19
	 */
	public function show_success( $msg ) {
		$this->save_notice( 'success', $msg );
	}

	/**
	 * Displays a error notice
	 *
	 * @access      public
	 * @param       string $msg The message to qeue.
	 * @since       1.0.19
	 */
	public function show_error( $msg ) {
		$this->save_notice( 'error', $msg );
	}

	/**
	 * Displays a warning notice
	 *
	 * @access      public
	 * @param       string $msg The message to qeue.
	 * @since       1.0.19
	 */
	public function show_warning( $msg ) {
		$this->save_notice( 'warning', $msg );
	}

	/**
	 * Displays a info notice
	 *
	 * @access      public
	 * @param       string $msg The message to qeue.
	 * @since       1.0.19
	 */
	public function show_info( $msg ) {
		$this->save_notice( 'info', $msg );
	}
}

Admin_Notices::init();
