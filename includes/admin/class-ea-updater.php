<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Updater{
	/**
	 * The upgrades
	 *
	 * @var array
	 */
	private static $upgrades = array(
		'1.0.2' => 'updates/update-1.0.2.php',
	);

	/**
	 * Get the plugin version
	 *
	 * @return string
	 */
	public function get_version() {
		return get_option( 'eaccounting_version' );
	}

	/**
	 * Check if the plugin needs any update
	 *
	 * @return boolean
	 */
	public function needs_update() {

		// may be it's the first install
		if ( ! $this->get_version() ) {
			return false;
		}

		if ( version_compare( $this->get_version(), eaccounting()->version, '<' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Perform all the necessary upgrade routines
	 *
	 * @return void
	 */
	function perform_updates() {
		$installed_version = $this->get_version();
		$path              = trailingslashit( dirname( __FILE__ ) );
		foreach ( self::$upgrades as $version => $file ) {
			if ( version_compare( $installed_version, $version, '<' ) ) {
				include $path . $file;
				update_option( 'eaccounting_version', $version );
			}
		}
	}

}
