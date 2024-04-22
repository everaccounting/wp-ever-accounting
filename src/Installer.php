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
class Installer extends \ByteKit\Core\Installer {
	/**
	 * Update callbacks.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $updates = array(
		'1.2.1' => array(
			'eac_update_121_currency',
		)
	);

	/**
	 * Install the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function install() {
		if ( ! is_blog_installed() ) {
			return;
		}
		$this->create_tables();
		$this->create_currencies();
		// Implement the plugin installation.
		//$this->plugin->update_db_version( $this->plugin->get_version() );
	}

	/**
	 * Create tables.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function create_tables() {
		global $wpdb;
		$collate          = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';
		$max_index_length = 191;

		// drop old ea_currencies table if exists.
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ea_currencies" );


		$tables = "
CREATE TABLE {$wpdb->prefix}eac_currencies (
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
	public function create_currencies() {
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
