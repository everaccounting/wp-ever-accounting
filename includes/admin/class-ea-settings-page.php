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
			$this->admin_init();
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

		/**
		 * @since 1.0.0
		 * @return void
		 */
		public function print_settings_pages() {
			$this->settings->show_settings();
		}

		/**
		 * Setup sections
		 *
		 * @since 1.0.0
		 * @return mixed|void
		 */
		public function get_settings_sections() {
			$sections = array();
			return apply_filters( 'ever_accounting_settings_sections', $sections );
		}

		/**
		 * Setup fields
		 *
		 * @since 1.0.0
		 * @return mixed|void
		 */
		public function get_settings_fields() {
			$settings_fields = array();
			return apply_filters( 'ever_accounting_settings_fields', $settings_fields );
		}
	}
endif;
