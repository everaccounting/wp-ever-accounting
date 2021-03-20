<?php
/**
 * Load assets.
 *
 * @package     EverAccounting
 * @version     1.0.2
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit();

/**
 * Class Assets
 * @since   1.0.2
 * @package EverAccounting\Admin
 */
class Assets extends \EverAccounting\Abstracts\Assets {

	/**
	 * Assets constructor.
	 */
	public function __construct() {
		parent::__construct( EACCOUNTING_PLUGIN_FILE );
	}

	/**
	 * Enqueue public styles.
	 *
	 * @version 1.0.3
	 */
	public function public_styles() {

	}

	/**
	 * Enqueue public scripts.
	 *
	 * @version 1.0.3
	 */
	public function public_scripts() {

	}

	/**
	 * Enqueue admin styles.
	 *
	 * @version 1.0.3
	 */
	public function admin_styles() {

	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @version 1.0.3
	 */
	public function admin_scripts() {
		$this->register_script('ea-admin');
		wp_enqueue_script( 'ea-admin' );
	}

}

return new \EverAccounting\Assets();
