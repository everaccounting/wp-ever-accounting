<?php
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'EAccounting_Admin_Settings', false ) ) :
	class EAccounting_Admin_Settings {
		/**
		 * Setting pages.
		 *
		 * @var array
		 */
		private static $settings = array();

		/**
		 * Error messages.
		 *
		 * @var array
		 */
		private static $errors = array();

		/**
		 * Update messages.
		 *
		 * @var array
		 */
		private static $messages = array();

		/**
		 * Include the settings page classes.
		 */
		public static function get_settings_pages() {
			if ( empty( self::$settings ) ) {
				$settings = array();

				include_once dirname( __FILE__ ) . '/settings/class-ea-settings-page.php';
				$settings[] = include 'settings/class-wc-settings-general.php';
				$settings[] = include 'settings/class-wc-settings-products.php';
				self::$settings = apply_filters( 'woocommerce_get_settings_pages', $settings );
			}

			return self::$settings;
		}

		/**
		 * Add a message.
		 *
		 * @param string $text Message.
		 */
		public static function add_message( $text ) {
			self::$messages[] = $text;
		}

		/**
		 * Add an error.
		 *
		 * @param string $text Message.
		 */
		public static function add_error( $text ) {
			self::$errors[] = $text;
		}

		/**
		 * Output messages + errors.
		 */
		public static function show_messages() {
			if ( count( self::$errors ) > 0 ) {
				foreach ( self::$errors as $error ) {
					echo '<div id="message" class="error inline"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
				}
			} elseif ( count( self::$messages ) > 0 ) {
				foreach ( self::$messages as $message ) {
					echo '<div id="message" class="updated inline"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
				}
			}
		}

		/**
		 * Settings page.
		 *
		 * Handles the display of the main woocommerce settings page in admin.
		 */
		public static function output() {
			global $current_tab, $current_section;
			if ( ! is_admin() || ! isset( $_GET['page'] ) || 'eaccounting-settings' !== $_GET['page'] ) {
				return;
			}

			self::get_settings_pages();
			// Get current tab/section.
			$current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['tab'] ) ); // WPCS: input var okay, CSRF ok.
			$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) ); // WPCS: input var okay, CSRF ok.


			// Get tabs for the settings page.
			$tabs = apply_filters( 'woocommerce_settings_tabs_array', array() );

			include dirname( __FILE__ ) . '/settings/html-admin-settings.php';
		}

		/**
		 * Save the settings.
		 */
		public static function save() {
			global $current_tab;

			check_admin_referer( 'eaccounting-settings' );

			// Trigger actions.
			do_action( 'woocommerce_settings_save_' . $current_tab );
			do_action( 'woocommerce_update_options_' . $current_tab );
			do_action( 'woocommerce_update_options' );

			self::add_message( __( 'Your settings have been saved.', 'woocommerce' ) );
			self::check_download_folder_protection();

			do_action( 'woocommerce_settings_saved' );
		}

		/**
		 * Get a setting from the settings API.
		 *
		 * @param string $option_name Option name.
		 * @param mixed  $default     Default value.
		 * @return mixed
		 */
		public static function get_option( $option_name, $default = '' ) {
			if ( ! $option_name ) {
				return $default;
			}

			// Array value.
			if ( strstr( $option_name, '[' ) ) {

				parse_str( $option_name, $option_array );

				// Option name is first key.
				$option_name = current( array_keys( $option_array ) );

				// Get value.
				$option_values = get_option( $option_name, '' );

				$key = key( $option_array[ $option_name ] );

				if ( isset( $option_values[ $key ] ) ) {
					$option_value = $option_values[ $key ];
				} else {
					$option_value = null;
				}
			} else {
				// Single value.
				$option_value = get_option( $option_name, null );
			}

			if ( is_array( $option_value ) ) {
				$option_value = array_map( 'stripslashes', $option_value );
			} elseif ( ! is_null( $option_value ) ) {
				$option_value = stripslashes( $option_value );
			}

			return ( null === $option_value ) ? $default : $option_value;
		}


		/**
		 * Checks which method we're using to serve downloads.
		 *
		 * If using force or x-sendfile, this ensures the .htaccess is in place.
		 */
		public static function check_download_folder_protection() {
			$upload_dir    = wp_upload_dir();
			$downloads_url = $upload_dir['basedir'] . '/eaccounting_uploads';
			if(!is_dir($downloads_url)){
				wp_mkdir_p($downloads_url);
			}
			if ( ! file_exists( $downloads_url . '/.htaccess' ) ) {
				$file_handle = @fopen( $downloads_url . '/.htaccess', 'w' );
				if ( $file_handle ) {
					fwrite( $file_handle, 'deny from all' );
					fclose( $file_handle );
				}
			}
		}

	}
endif;
