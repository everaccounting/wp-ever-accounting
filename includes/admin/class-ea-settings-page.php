<?php

defined( 'ABSPATH' ) || exit();
if ( ! class_exists( 'EAccounting_Settings_Page', false ) ) :
	class EAccounting_Settings_Page {

		/**
		 * @var EAccounting_Settings_Api
		 */
		private $settings;

		/**
		 * EAccounting_Settings_Page constructor.
		 */
		public function __construct() {
			$this->settings = new \EAccounting_Settings_Api();
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		/**
		 * Initialize settings
		 *
		 * @since 1.0.0
		 */
		public function admin_init() {
			//set the settings
			$this->settings->set_sections( $this->get_settings_sections() );
			$this->settings->set_fields( $this->get_settings_fields() );
			//initialize settings
			$this->settings->admin_init();
		}

		function admin_menu() {
			add_submenu_page( 'ever-accounting', __( 'Settings', 'wp-ever-accounting' ), __( 'Settings', 'wp-ever-accounting' ), 'manage_options', 'eaccounting-settings', array( $this, 'settings_page' ) );
		}

		/**
		 * @return void
		 * @since 1.0.0
		 */
		public function settings_page() {
			echo '<div class="wrap">';
			echo sprintf( "<h2>%s</h2>", __( 'Ever Accounting - Settings', 'wp-ever-accounting' ) );
			$this->settings->show_settings();
			echo '</div>';
		}

		/**
		 * Setup sections
		 *
		 * @return mixed|void
		 * @since 1.0.0
		 */
		public function get_settings_sections() {
			$sections = array();

			return apply_filters( 'eaccounting_settings_sections', $sections );
		}

		/**
		 * Setup fields
		 *
		 * @return mixed|void
		 * @since 1.0.0
		 */
		public function get_settings_fields() {
			$settings_fields = array();

			return apply_filters( 'eaccounting_settings_fields', $settings_fields );
		}
	}
	new EAccounting_Settings_Page();
endif;
