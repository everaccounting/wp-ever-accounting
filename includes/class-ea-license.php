<?php
/**
 * License handler for WP Ever Accounting
 *
 * This class should simplify the process of adding license information
 * to new extensions.
 *
 * @version 1.1.0
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'EverAccounting_License' ) ) :

	/**
	 * EverAccounting_License Class
	 */
	class EverAccounting_License {
		private $file;
		private $license;
		private $item_name;
		private $item_id;
		private $item_shortname;
		private $version;
		private $author;
		private $api_url = 'https://wpeveraccounting.com/';

		/**
		 * Class constructor
		 *
		 * @param string $_file
		 * @param string $_item_name
		 * @param string $_version
		 * @param string $_author
		 * @param string $_optname
		 * @param string $_api_url
		 * @param int    $_item_id
		 */
		function __construct( $_file, $_item_name, $_version, $_author, $_optname = null, $_api_url = null, $_item_id = null ) {
			$this->file      = $_file;
			$this->item_name = $_item_name;

			if ( is_numeric( $_item_id ) ) {
				$this->item_id = absint( $_item_id );
			}

			$this->item_shortname = 'ea_' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->item_name ) ) );
			$this->version        = $_version;
			$this->license        = trim( eaccounting()->settings->get( $this->item_shortname . '_license_key', '' ) );
			$this->author         = $_author;
			$this->api_url        = is_null( $_api_url ) ? $this->api_url : $_api_url;

			/**
			 * Allows for backwards compatibility with old license options,
			 * i.e. if the plugins had license key fields previously, the license
			 * handler will automatically pick these up and use those in lieu of the
			 * user having to reactive their license.
			 */
			if ( ! empty( $_optname ) ) {
				$opt = eaccounting()->settings->get( $_optname, false );

				if ( isset( $opt ) && empty( $this->license ) ) {
					$this->license = trim( $opt );
				}
			}

			// Setup hooks
			$this->includes();
			$this->hooks();

		}

		/**
		 * Include the updater class
		 *
		 * @access  private
		 * @return  void
		 */
		private function includes() {
			if ( ! class_exists( 'EverAccounting_Plugin_Updater' ) ) {
				require_once 'class-ea-plugin-updater.php';
			}
		}

		/**
		 * Setup hooks
		 *
		 * @access  private
		 * @return  void
		 */
		private function hooks() {

			// Register settings
			add_filter( 'eaccounting_settings_licenses', array( $this, 'settings' ), 1 );

			// Display help text at the top of the Licenses tab
			add_action( 'eaccounting_settings_top', array( $this, 'license_help_text' ) );

			// Activate license key on settings save
			add_action( 'admin_init', array( $this, 'activate_license' ) );

			// Deactivate license key
			add_action( 'admin_init', array( $this, 'deactivate_license' ) );

			// Check that license is valid once per week
			add_action( 'eaccounting_weekly_scheduled_events', array( $this, 'weekly_license_check' ) );

			// For testing license notices, uncomment this line to force checks on every page load
			add_action( 'admin_init', array( $this, 'weekly_license_check' ) );

			// Updater
			add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );

			// Display notices to admins
			add_action( 'admin_notices', array( $this, 'notices' ) );

			add_action( 'in_plugin_update_message-' . plugin_basename( $this->file ), array( $this, 'plugin_row_license_missing' ), 10, 2 );
		}

		/**
		 * Auto updater
		 *
		 * @access  private
		 * @return  void
		 */
		public function auto_updater() {
			$betas = eaccounting()->settings->get( 'enabled_betas', array() );

			$args = array(
				'version'   => $this->version,
				'license'   => $this->license,
				'item_name' => $this->item_name,
				'author'    => $this->author,
			);

			if ( ! empty( $this->item_id ) ) {
				$args['item_id'] = $this->item_id;
			} else {
				$args['item_name'] = $this->item_name;
			}

			// Setup the updater
			$updates = new EverAccounting_Plugin_Updater(
				$this->api_url,
				$this->file,
				$args
			);
		}


		/**
		 * Add license field to settings
		 *
		 * @param array $settings
		 *
		 * @return  array
		 */
		public function settings( $settings ) {
			$edd_license_settings = array(
				array(
					'id'      => $this->item_shortname . '_license_key',
					'name'    => sprintf( __( '%1$s', 'wp-ever-accounting' ), $this->item_name ), //phpcs:ignore
					'license' => get_option( $this->item_shortname . '_license_active' ),
					'desc'    => '',
					'type'    => 'license_key',
					'size'    => 'regular',
				),
			);

			return array_merge( $settings, $edd_license_settings );
		}


		/**
		 * Display help text at the top of the Licenses tag
		 *
		 * @since   1.1.0
		 *
		 * @param string $active_tab
		 *
		 * @return  void
		 */
		public function license_help_text( $active_tab = '' ) {
			static $has_ran;
			if ( ! empty( $has_ran ) ) {
				return;
			}

			if ( 'licenses' !== $active_tab ) {
				return;
			}

			echo '<p>' . sprintf(
				__( 'Enter your extension license keys here to receive updates for purchased extensions. If your license key has expired, please <a href="%s" target="_blank">renew your license</a>.', 'wp-ever-accounting' ),
				'http://wpeveraccounting.com/license-renewal'
			) . '</p>';
			$has_ran = true;
		}


		/**
		 * Activate the license key
		 *
		 * @return  void
		 */
		public function activate_license() {
			if ( ! isset( $_POST['eaccounting_settings'] ) ) {
				return;
			}
			if ( ! isset( $_REQUEST[ $this->item_shortname . '_license_key-nonce' ] ) || ! wp_verify_nonce( $_REQUEST[ $this->item_shortname . '_license_key-nonce' ], $this->item_shortname . '_license_key-nonce' ) ) {
				return;
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( empty( $_POST['eaccounting_settings'][ $this->item_shortname . '_license_key' ] ) ) {

				delete_option( $this->item_shortname . '_license_active' );

				return;

			}

			foreach ( $_POST as $key => $value ) {
				if ( false !== strpos( $key, 'license_key_deactivate' ) ) {
					// Don't activate a key when deactivating a different key
					return;
				}
			}
			$details = get_option( $this->item_shortname . '_license_active' );

			if ( is_object( $details ) && 'valid' === $details->license ) {
				return;
			}

			$license = sanitize_text_field( $_POST['eaccounting_settings'][ $this->item_shortname . '_license_key' ] );

			if ( empty( $license ) ) {
				return;
			}

			// Data to send to the API
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_name'  => urlencode( $this->item_name ),
				'url'        => home_url(),
			);

			if ( ! empty( $this->item_id ) ) {
				$api_params['item_id'] = $this->item_id;
			}

			// Call the API
			$response = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			// Make sure there are no errors
			if ( is_wp_error( $response ) ) {
				return;
			}

			// Tell WordPress to look for updates
			set_site_transient( 'update_plugins', null );

			// Decode license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			update_option( $this->item_shortname . '_license_active', $license_data );
		}

		/**
		 * Deactivate the license key
		 *
		 * @return  void
		 */
		public function deactivate_license() {

			if ( ! isset( $_POST['eaccounting_settings'] ) ) {
				return;
			}

			if ( ! isset( $_POST['eaccounting_settings'][ $this->item_shortname . '_license_key' ] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_REQUEST[ $this->item_shortname . '_license_key-nonce' ], $this->item_shortname . '_license_key-nonce' ) ) {

				wp_die( __( 'Nonce verification failed', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );

			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Run on deactivate button press
			if ( isset( $_POST[ $this->item_shortname . '_license_key_deactivate' ] ) ) {

				// Data to send to the API
				$api_params = array(
					'edd_action' => 'deactivate_license',
					'license'    => $this->license,
					'item_name'  => urlencode( $this->item_name ),
					'url'        => home_url(),
				);

				if ( ! empty( $this->item_id ) ) {
					$api_params['item_id'] = $this->item_id;
				}

				// Call the API
				$response = wp_remote_post(
					$this->api_url,
					array(
						'timeout'   => 15,
						'sslverify' => false,
						'body'      => $api_params,
					)
				);

				// Make sure there are no errors
				if ( is_wp_error( $response ) ) {
					return;
				}

				// Decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				eaccounting_update_option( $this->item_shortname . '_license_key', '' );
				delete_option( $this->item_shortname . '_license_active' );

			}
		}

		/**
		 * Check if license key is valid once per week
		 *
		 * @since   2.5
		 * @return  void
		 */
		public function weekly_license_check() {

			if ( ! empty( $_POST['eaccounting_settings'] ) ) {
				return; // Don't fire when saving settings
			}

			if ( empty( $this->license ) ) {
				return;
			}

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'check_license',
				'license'    => $this->license,
				'item_name'  => urlencode( $this->item_name ),
				'url'        => home_url(),
			);

			if ( ! empty( $this->item_id ) ) {
				$api_params['item_id'] = $this->item_id;
			}

			// Call the API
			$response = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);
			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			update_option( $this->item_shortname . '_license_active', $license_data );

		}

		/**
		 * Admin notices for errors
		 *
		 * @return  void
		 */
		public function notices() {
			if ( empty( $this->license ) ) {
				return;
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$messages = array();

			$license = get_option( $this->item_shortname . '_license_active' );

			if ( is_object( $license ) && 'valid' !== $license->license ) {

				if ( empty( $_GET['tab'] ) || 'licenses' !== $_GET['tab'] ) {
					$messages[] = sprintf(
					/* translators: %s plugin link */
						__( 'You have invalid or expired license keys for WP Ever Accounting. Please go to the <a href="%s">License page</a> to correct this issue.', 'wp-ever-accounting' ),
						admin_url( 'admin.php?page=ea-settings&tab=licenses' )
					);
				}
			}
			if ( ! empty( $messages ) ) {

				foreach ( $messages as $message ) {

					echo '<div class="error">';
					echo '<p>' . $message . '</p>';
					echo '</div>';

				}
			}

		}

		/**
		 * Displays message inline on plugin row that the license key is missing
		 *
		 * @since   2.5
		 * @return  void
		 */
		public function plugin_row_license_missing( $plugin_data, $version_info ) {
			$license = get_option( $this->item_shortname . '_license_active' );
			if ( ( ! is_object( $license ) || 'valid' !== $license->license ) ) {
				echo '&nbsp;<strong><a href="' . esc_url( admin_url( 'admin.php?page=ea-settings&tab=licenses' ) ) . '">' . __( 'Enter valid license key for automatic updates.', 'wp-ever-accounting' ) . '</a></strong>';
			}

		}

		/**
		 * Adds this plugin to the beta page
		 *
		 * @since   2.6.11
		 *
		 * @param array $products
		 *
		 * @return  void
		 */
		public function register_beta_support( $products ) {
			$products[ $this->item_shortname ] = $this->item_name;

			return $products;
		}
	}

endif; // end class_exists check
