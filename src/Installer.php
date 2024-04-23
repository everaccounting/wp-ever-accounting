<?php

namespace EverAccounting;

use EverAccounting\Utilities\I18n;

defined( 'ABSPATH' ) || exit;

/**
 * Class Installer.
 *
 * @since   1.0.0
 * @package WooCommerceKeyManager
 */
class Installer {
	/**
	 * Update callbacks.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $updates = array(
		'1.2.1.4' => array(
			'eac_update_1214',
			'eac_update_1214_another',
		),
		'1.2.1.5' => array(
			'eac_update_1215',
			'eac_update_1215_another',
		),
		'1.2.1.7' => 'eac_update_1217'
	);

	/**
	 * Construct and initialize the plugin aware trait.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'check_update' ), 5 );
		add_action( 'eac_run_update_callback', array( $this, 'run_update_callback' ), 10, 2 );
		add_action( 'eac_update_db_version', array( $this, 'update_db_version' ) );
	}

	/**
	 * Check the plugin version and run the updater if necessary.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 *
	 * @since 1.2.1
	 * @return void
	 */
	public function check_update() {
		$db_version      = EAC()->get_db_version();
		$current_version = EAC()->get_version();
		$requires_update = version_compare( $db_version, $current_version, '<' );
		$can_install     = ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ! defined( 'IFRAME_REQUEST' );
		if ( $can_install && $requires_update && ! EAC()->queue()->get_next( 'eac_run_update_callback' ) ) {
			error_log( 'Updating Ever Accounting' );
			static::install();
			$update_versions = array_keys( $this->updates );
			usort( $update_versions, 'version_compare' );
			if ( ! is_null( $db_version ) && version_compare( $db_version, end( $update_versions ), '<' ) ) {
				$this->update();
			} else {
				EAC()->update_db_version( $current_version );
			}
		}
	}

	/**
	 * Update the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function update() {
		$db_version = EAC()->get_db_version();
		$loop       = 0;
		foreach ( $this->updates as $version => $callbacks ) {
			$callbacks = (array) $callbacks;
			if ( version_compare( $db_version, $version, '<' ) ) {
				foreach ( $callbacks as $callback ) {
					EAC()->queue()->schedule_single(
						time() + $loop,
						'eac_run_update_callback',
						array( 'callback' => $callback, 'version' => $version )
					);

					error_log( sprintf( 'Scheduling update callback %s of version %s', $callback, $version ) );

					$loop ++;
				}
			}
			$loop ++;
		}

		if ( version_compare( EAC()->get_db_version(), EAC()->get_version(), '<' ) &&
		     ! EAC()->queue()->get_next( 'eac_update_db_version' ) ) {
			EAC()->queue()->schedule_single(
				time() + $loop,
				'eac_update_db_version',
				array(
					'version' => EAC()->get_version(),
				)
			);
			error_log( "Finally scheduling db udpate" );
		}
	}

	/**
	 * Run the update callback.
	 *
	 * @param string $callback The callback to run.
	 * @param string $version The version of the callback.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function run_update_callback( $callback, $version ) {
		require_once __DIR__ . '/Functions/updates.php';
		if ( is_callable( $callback ) ) {
			$result = (bool) call_user_func( $callback );
			if ( $result ) {
				EAC()->queue()->add(
					'eac_run_update_callback',
					array(
						'callback' => $callback,
						'version'  => $version,
					)
				);
			}
		}
	}

	/**
	 * Update the plugin version.
	 *
	 * @param string $version The version to update to.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function update_db_version( $version ) {
		error_log( "Updating db version to $version" );
		EAC()->update_db_version( $version );
	}

	/**
	 * Install the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}
		self::create_tables();
		self::create_currencies();
		EAC()->add_db_version();
	}

	/**
	 * Create tables.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function create_tables() {
		global $wpdb;
		$collate          = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';
		$max_index_length = 191;

		// drop old ea_currencies table if exists.
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ea_currencies" );


		$tables = "
CREATE TABLE {$wpdb->prefix}ea_currencies (
`id` bigINT(20) NOT NULL AUTO_INCREMENT,
`code` VARCHAR(191) NOT NULL,
`name` VARCHAR(191) NOT NULL,
`exchange_rate` DOUBLE(15,4) NOT NULL DEFAULT '1.0000',
`precision` INT(2) NOT NULL DEFAULT 0,
`symbol` VARCHAR(5) NOT NULL,
`subunit` INT(3) NOT NULL DEFAULT 100,
`position` ENUM('before','after') NOT NULL DEFAULT 'before',
`thousand_separator` VARCHAR(5) NOT NULL DEFAULT ',',
`decimal_separator` VARCHAR(5) NOT NULL DEFAULT '.',
`enabled` tinyint(1) NOT NULL DEFAULT '1',
`date_updated` DATETIME NULL DEFAULT NULL,
`date_created` DATETIME NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `name` (`name`),
UNIQUE KEY `code` (`code`),
KEY `exchange_rate` (`exchange_rate`),
KEY `enabled` (`enabled`)
) $collate;
";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $tables );
	}

	/**
	 * Create currencies.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function create_currencies() {
		$options = get_option( 'eaccounting_currencies', array() );
		if ( $options ) {
			foreach ( $options as $option ) {
				eac_insert_currency( $option );
			}
			delete_option( 'eaccounting_currencies' );
		}
		$currencies = eac_get_currencies( [ 'limit' => - 1 ] );
		$codes      = wp_list_pluck( $currencies, 'code' );
		foreach ( I18n::get_currencies() as $code => $currency ) {
			if ( ! in_array( $code, $codes, true ) ) {
				$currency['enabled'] = 0;
				eac_insert_currency( $currency );
			}
		}
	}
}
