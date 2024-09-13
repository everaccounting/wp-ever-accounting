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
		'1.2.0'   => 'eac_update_120',
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
						array(
							'callback' => $callback,
							'version'  => $version,
						)
					);
					++$loop;
				}
			}
			++$loop;
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
		self::create_roles();
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
		$wpdb->hide_errors();
		$collate      = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';

		// drop old ea_currencies table if exists.
		$currency_table = $wpdb->prefix . 'ea_currencies';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$currency_table}'" ) === $currency_table &&
			$wpdb->get_var( "SHOW COLUMNS FROM {$currency_table} LIKE 'exchange_rate'" ) != 'exchange_rate' ) {
			$wpdb->query( "DROP TABLE $currency_table" );
		}

		$tables = "
CREATE TABLE {$wpdb->prefix}ea_accounts (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    type VARCHAR(50) NOT NULL,
    name VARCHAR(191) NOT NULL,
    number VARCHAR(100) NOT NULL,
    bank_name VARCHAR(191) DEFAULT NULL,
    bank_phone VARCHAR(20) DEFAULT NULL,
    bank_address VARCHAR(191) DEFAULT NULL,
    currency_code VARCHAR(3) NOT NULL DEFAULT 'USD',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    creator_id BIGINT(20) UNSIGNED DEFAULT NULL,
    thumbnail_id BIGINT(20) UNSIGNED DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY number (number),
    KEY name (name),
    KEY type (type),
    KEY status (status)
) $collate;

CREATE TABLE {$wpdb->prefix}ea_categories (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    type VARCHAR(50) NOT NULL,
    name VARCHAR(191) NOT NULL,
    description TEXT DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY name_type (name, type),
    KEY status (status)
) $collate;

CREATE TABLE {$wpdb->prefix}ea_currencies (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    code VARCHAR(3) NOT NULL,
    name VARCHAR(191) NOT NULL,
    exchange_rate DOUBLE(15, 4) NOT NULL DEFAULT 1.0000,
    decimals INT(2) NOT NULL DEFAULT 0,
    symbol VARCHAR(5) NOT NULL,
    subunit INT(3) NOT NULL DEFAULT 100,
    position ENUM('before', 'after') NOT NULL DEFAULT 'before',
    thousand_separator VARCHAR(5) NOT NULL DEFAULT ',',
    decimal_separator VARCHAR(5) NOT NULL DEFAULT '.',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY code (code),
    KEY status (status)
) $collate;

CREATE TABLE {$wpdb->prefix}ea_contactmeta (
    meta_id BIGINT(20) NOT NULL AUTO_INCREMENT,
    ea_contact_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
    meta_key VARCHAR(191) DEFAULT NULL,
    meta_value LONGTEXT,
    PRIMARY KEY (meta_id),
    KEY ea_contact_id (ea_contact_id),
    KEY meta_key (meta_key(191))
) $collate;

CREATE TABLE {$wpdb->prefix}ea_contacts (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    type VARCHAR(30) DEFAULT 'customer',
    name VARCHAR(191) NOT NULL,
    company VARCHAR(191) NOT NULL,
    email VARCHAR(191) DEFAULT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    website VARCHAR(191) DEFAULT NULL,
    address VARCHAR(191) DEFAULT NULL,
    city VARCHAR(50) DEFAULT NULL,
    state VARCHAR(50) DEFAULT NULL,
    postcode VARCHAR(20) DEFAULT NULL,
    country VARCHAR(3) DEFAULT NULL,
    tax_number VARCHAR(50) DEFAULT NULL,
    currency_code VARCHAR(3) NOT NULL DEFAULT 'USD',
    thumbnail_id BIGINT(20) UNSIGNED DEFAULT NULL,
    user_id BIGINT(20) UNSIGNED DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_via VARCHAR(100) DEFAULT 'manual',
    creator_id BIGINT(20) UNSIGNED DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY name (name(191)),
    KEY type (type),
    KEY email (email(191)),
    KEY phone (phone(50)),
    KEY currency_code (currency_code),
    KEY user_id (user_id),
    KEY status (status)
) $collate;

CREATE TABLE {$wpdb->prefix}ea_document_items (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    type VARCHAR(20) NOT NULL DEFAULT 'standard',
    name VARCHAR(191) NOT NULL,
    description VARCHAR(160) DEFAULT NULL,
    unit VARCHAR(20) DEFAULT NULL,
    price DOUBLE(15, 4) NOT NULL,
    quantity DOUBLE(7, 2) NOT NULL DEFAULT 0.00,
    subtotal DOUBLE(15, 4) NOT NULL DEFAULT 0.00,
    subtotal_tax DOUBLE(15, 4) NOT NULL DEFAULT 0.00,
    discount DOUBLE(15, 4) NOT NULL DEFAULT 0.00,
    discount_tax DOUBLE(15, 4) NOT NULL DEFAULT 0.00,
    tax_total DOUBLE(15, 4) NOT NULL DEFAULT 0.00,
    total DOUBLE(15, 4) NOT NULL DEFAULT 0.00,
    taxable TINYINT(1) NOT NULL DEFAULT 0,
    item_id BIGINT(20) UNSIGNED DEFAULT NULL,
    document_id BIGINT(20) UNSIGNED DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY type (type),
    KEY taxable (taxable),
    KEY name (name),
    KEY price (price),
    KEY quantity (quantity),
    KEY subtotal (subtotal),
    KEY total (total)
) $collate;

CREATE TABLE {$wpdb->prefix}ea_document_item_taxes (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    name VARCHAR(191) NOT NULL,
    rate DOUBLE(15, 4) NOT NULL,
    compound TINYINT(1) NOT NULL DEFAULT 0,
    amount DOUBLE(15, 4) NOT NULL DEFAULT 0.00,
    item_id BIGINT(20) UNSIGNED NOT NULL,
    tax_id BIGINT(20) UNSIGNED NOT NULL,
    document_id BIGINT(20) UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY item_id (item_id),
    KEY tax_id (tax_id),
    KEY document_id (document_id)
) $collate;

CREATE TABLE {$wpdb->prefix}ea_documentmeta (
    meta_id BIGINT(20) NOT NULL AUTO_INCREMENT,
    ea_document_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
    meta_key VARCHAR(191) DEFAULT NULL,
    meta_value LONGTEXT,
    PRIMARY KEY (meta_id),
    KEY ea_document_id (ea_document_id),
    KEY meta_key (meta_key(191))
) $collate;

CREATE TABLE {$wpdb->prefix}ea_documents (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    type VARCHAR(20) NOT NULL DEFAULT 'invoice',
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    number VARCHAR(30) NOT NULL,
    contact_id BIGINT(20) UNSIGNED NOT NULL,
    subtotal DOUBLE(15, 4) DEFAULT 0,
    discount_total DOUBLE(15, 4) DEFAULT 0,
    tax_total DOUBLE(15, 4) DEFAULT 0,
    total DOUBLE(15, 4) DEFAULT 0,
    discount_amount DOUBLE(15, 4) DEFAULT 0,
    discount_type ENUM('fixed', 'percentage') DEFAULT NULL,
    billing_name VARCHAR(191) DEFAULT NULL,
    billing_address VARCHAR(191) DEFAULT NULL,
    billing_company VARCHAR(191) DEFAULT NULL,
    billing_city VARCHAR(50) DEFAULT NULL,
    billing_state VARCHAR(50) DEFAULT NULL,
    billing_postcode VARCHAR(20) DEFAULT NULL,
    billing_country VARCHAR(3) DEFAULT NULL,
    billing_phone VARCHAR(50) DEFAULT NULL,
    billing_email VARCHAR(191) DEFAULT NULL,
    billing_tax_number VARCHAR(50) DEFAULT NULL,
    reference VARCHAR(30) DEFAULT NULL,
    note TEXT DEFAULT NULL,
    vat_exempt TINYINT(1) NOT NULL DEFAULT 0,
    issue_date DATETIME DEFAULT NULL,
    due_date DATETIME DEFAULT NULL,
    sent_date DATETIME DEFAULT NULL,
    payment_date DATETIME DEFAULT NULL,
    currency_code VARCHAR(3) NOT NULL DEFAULT 'USD',
    exchange_rate DOUBLE(15, 4) NOT NULL DEFAULT 1.00,
    parent_id BIGINT(20) UNSIGNED NOT NULL,
    created_via VARCHAR(100) DEFAULT 'manual',
    creator_id BIGINT(20) UNSIGNED NOT NULL,
    uuid VARCHAR(36) DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY number (number),
    UNIQUE KEY uuid (uuid),
    KEY contact_id (contact_id),
    KEY type (type),
    KEY status (status),
    KEY total (total),
    KEY balance (balance)
) $collate;

CREATE TABLE {$wpdb->prefix}ea_items (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    type VARCHAR(50) NOT NULL DEFAULT 'standard',
    name VARCHAR(191) NOT NULL,
    description TEXT DEFAULT NULL,
    unit VARCHAR(50) DEFAULT NULL,
    price DOUBLE(15, 4) NOT NULL,
    cost DOUBLE(15, 4) NOT NULL,
    taxable TINYINT(1) NOT NULL DEFAULT 0,
    tax_ids VARCHAR(191) DEFAULT NULL,
    category_id INT(11) DEFAULT NULL,
    thumbnail_id BIGINT(20) UNSIGNED DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY name (name),
    KEY type (type),
    KEY price (price),
    KEY cost (cost),
    KEY status (status)
) $collate;


CREATE TABLE {$wpdb->prefix}ea_transactionmeta (
    meta_id BIGINT(20) NOT NULL AUTO_INCREMENT,
    ea_transaction_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
    meta_key VARCHAR(191) DEFAULT NULL,
    meta_value LONGTEXT,
    PRIMARY KEY (meta_id),
    KEY ea_transaction_id (ea_transaction_id),
    KEY meta_key (meta_key(191))
) $collate;

CREATE TABLE {$wpdb->prefix}ea_taxes (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    name VARCHAR(191) NOT NULL,
    rate DOUBLE(15, 4) NOT NULL,
    compound TINYINT(1) NOT NULL DEFAULT 0,
    description TEXT DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY name (name),
    KEY rate (rate),
    KEY compound (compound),
    KEY status (status)
) $collate;

CREATE TABLE {$wpdb->prefix}ea_notes (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    parent_id BIGINT(20) UNSIGNED NOT NULL,
    parent_type VARCHAR(20) NOT NULL,
    content TEXT DEFAULT NULL,
    note_metadata LONGTEXT DEFAULT NULL,
    creator_id BIGINT(20) UNSIGNED DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY parent_id (parent_id),
    KEY parent_type (parent_type)
) $collate;

CREATE TABLE {$wpdb->prefix}ea_transactions (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    type VARCHAR(20) DEFAULT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'completed',
    number VARCHAR(30) NOT NULL,
    date DATE NOT NULL DEFAULT '0000-00-00',
    amount DOUBLE(15, 4) NOT NULL,
    currency_code VARCHAR(3) NOT NULL DEFAULT 'USD',
    exchange_rate DOUBLE(15, 8) NOT NULL DEFAULT 1.0,
    reference VARCHAR(191) DEFAULT NULL,
    note TEXT DEFAULT NULL,
    payment_method VARCHAR(100) DEFAULT NULL,
    account_id BIGINT(20) UNSIGNED NOT NULL,
    document_id BIGINT(20) UNSIGNED DEFAULT NULL,
    contact_id BIGINT(20) UNSIGNED DEFAULT NULL,
    category_id BIGINT(20) UNSIGNED NOT NULL,
    attachment_id BIGINT(20) UNSIGNED DEFAULT NULL,
    parent_id BIGINT(20) UNSIGNED DEFAULT NULL,
    reconciled TINYINT(1) NOT NULL DEFAULT 0,
    created_via VARCHAR(100) DEFAULT 'manual',
    creator_id BIGINT(20) UNSIGNED DEFAULT NULL,
    uuid VARCHAR(36) DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uuid (uuid),
	KEY type (type),
    KEY number (number),
    KEY amount (amount),
    KEY currency_code (currency_code),
    KEY exchange_rate (exchange_rate),
    KEY account_id (account_id),
    KEY document_id (document_id),
    KEY category_id (category_id),
    KEY contact_id (contact_id),
    KEY status (status)
) $collate;

CREATE TABLE {$wpdb->prefix}ea_transaction_taxes (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    name VARCHAR(191) NOT NULL,
    rate DOUBLE(15, 4) NOT NULL,
    compound TINYINT(1) NOT NULL DEFAULT 0,
    amount DOUBLE(15, 4) NOT NULL DEFAULT 0.00,
    transaction_id BIGINT(20) UNSIGNED NOT NULL,
    tax_id BIGINT(20) UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY transaction_id (transaction_id),
    KEY tax_id (tax_id)
) $collate;

CREATE TABLE {$wpdb->prefix}ea_transfers (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    payment_id BIGINT(20) UNSIGNED NOT NULL,
    expense_id BIGINT(20) UNSIGNED NOT NULL,
    creator_id BIGINT(20) UNSIGNED DEFAULT NULL,
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY payment_id (payment_id),
    KEY expense_id (expense_id)
) $collate;
";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $tables );
	}

	/**
	 * Create roles and capabilities.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles();
		}

		// Dummy gettext calls to get strings in the catalog.
		_x( 'Accounting Manager', 'User role', 'wp-ever-accounting' );
		_x( 'Accountant', 'User role', 'wp-ever-accounting' );

		// Accountant role.
		add_role(
			'eac_accountant',
			'Accountant',
			array(
				'manage_accounting'   => true,
				'eac_manage_customer' => true,
				'eac_manage_vendor'   => true,
				'eac_manage_account'  => true,
				'eac_manage_payment'  => true,
				'eac_manage_expense'  => true,
				'eac_manage_transfer' => true,
				'eac_manage_category' => true,
				'eac_manage_currency' => true,
				'eac_manage_item'     => true,
				'eac_manage_invoice'  => true,
				'eac_manage_bill'     => true,
				'eac_manage_tax'      => true,
				'read'                => true,
			)
		);

		// Accounting manager role.
		add_role(
			'eac_manager',
			'Accounting Manager',
			array(
				'manage_accounting'   => true,
				'eac_manage_report'   => true,
				'eac_manage_options'  => true,
				'eac_manage_product'  => true,
				'eac_manage_customer' => true,
				'eac_manage_vendor'   => true,
				'eac_manage_account'  => true,
				'eac_manage_payment'  => true,
				'eac_manage_expense'  => true,
				'eac_manage_transfer' => true,
				'eac_manage_category' => true,
				'eac_manage_currency' => true,
				'eac_manage_item'     => true,
				'eac_manage_invoice'  => true,
				'eac_manage_bill'     => true,
				'eac_manage_tax'      => true,
				'eac_manage_import'   => true,
				'eac_manage_export'   => true,
				'read'                => true,
			)
		);

		// add caps to admin.
		global $wp_roles;

		if ( is_object( $wp_roles ) ) {
			$wp_roles->add_cap( 'administrator', 'manage_accounting' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_report' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_options' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_customer' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_vendor' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_account' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_payment' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_expense' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_transfer' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_category' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_currency' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_item' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_invoice' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_bill' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_tax' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_import' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_export' );
		}
	}

	/**
	 * Create currencies.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function create_currencies() {
		$all_currencies = I18n::get_currencies();
		$options        = get_option( 'eaccounting_currencies', array() );
		if ( $options ) {
			foreach ( $options as $option ) {
				$defaults = isset( $all_currencies[ $option['code'] ] ) ? $all_currencies[ $option['code'] ] : array();
				$data     = wp_parse_args( $option, $defaults );
				unset( $data['id'] );
				eac_insert_currency( $data );
			}
			delete_option( 'eaccounting_currencies' );
		}

		// If there is no currency, insert default currencies.
		$currencies = eac_get_currencies();
		if ( empty( $currencies ) ) {
			$usd_currency = isset( $all_currencies['USD'] ) ? $all_currencies['USD'] : array();
			eac_insert_currency(
				wp_parse_args(
					$usd_currency,
					array(
						'code'               => 'USD',
						'name'               => 'US Dollar',
						'exchange_rate'      => 1.0000,
						'decimals'          => 2,
						'symbol'             => '$',
						'subunit'            => 100,
						'position'           => 'before',
						'thousand_separator' => ',',
						'decimal_separator'  => '.',
						'status'             => 'active',
					)
				)
			);
		}

		// now if there is any no active currency, make USD active.
		$active_currencies = eac_get_currencies( array( 'status' => 'active' ) );
		if ( empty( $active_currencies ) ) {
			$usd = eac_get_currency( 'USD' );
			if ( $usd ) {
				eac_insert_currency(
					array(
						'id'     => $usd->id,
						'status' => 'active',
					)
				);
			}
		}
	}
}
