<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin.
 *
 * @since   1.0.0
 * @package EverAccounting
 */
class Plugin extends Singleton {

	/**
	 * Plugin constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @since 1.0.0
	 */
	protected function includes() {
		require_once __DIR__ . '/Functions.php';
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	protected function init_hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );

		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			$this->admin_init();
		}
	}

	/**
	 * Init WP Ever Accounting when WordPress Initialises.
	 *
	 * @since 1.0.0
	 */
	public function init() {

	}

	/**
	 * Init admin related classes.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_init() {
		\EverAccounting\Admin\Menus::instantiate();
	}

}
