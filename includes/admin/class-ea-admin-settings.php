<?php
/**
 * Admin Settings
 *
 * @package     EverAccounting
 * @subpackage  EverAccounting/Classes
 * @since       1.0.2
 */

namespace EverAccounting\Admin;

/**
 * Class Settings
 * @since 1.0.2
 * @package EverAccounting
 */
class Settings {
	/**
	 * Contains all settings option.
	 *
	 * @var array
	 * @since 1.0.2
	 */
	private $options;

	/**
	 * Get things started
	 *
	 * @return void
	 * @since 1.0
	 */
	public function __construct() {

		$this->options = get_option( 'eaccounting_settings', array() );

		// Set up.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		//add_action( 'admin_init', array( $this, 'activate_license' ) );
		//add_action( 'admin_init', array( $this, 'deactivate_license' ) );
		//add_action( 'admin_init', array( $this, 'check_license' ) );

		// Global settings.
		//add_action( 'eaccounting_pre_get_registered_settings', array( $this, 'handle_global_license_setting' ) );
		//add_action( 'eaccounting_pre_get_registered_settings', array( $this, 'handle_global_debug_mode_setting' ) );

		// Sanitization.
		add_filter( 'eaccounting_settings_sanitize', array( $this, 'sanitize_referral_variable' ), 10, 2 );
		add_filter( 'eaccounting_settings_sanitize_text', array( $this, 'sanitize_text_fields' ), 10, 2 );
		add_filter( 'eaccounting_settings_sanitize_url', array( $this, 'sanitize_url_fields' ), 10, 2 );
		add_filter( 'eaccountingsettings_sanitize_checkbox', array( $this, 'sanitize_cb_fields' ), 10, 2 );
		add_filter( 'eaccounting_settings_sanitize_number', array( $this, 'sanitize_number_fields' ), 10, 2 );
		add_filter( 'eaccounting_settings_sanitize_rich_editor', array( $this, 'sanitize_rich_editor_fields' ), 10, 2 );

		// Capabilities
		add_filter( 'option_page_capability_eaccounting_settings', array( $this, 'option_page_capability' ) );


		// Filter the email settings
		add_filter( 'eaccounting_settings_emails', array( $this, 'email_approval_settings' ) );
	}

	/**
	 * Get the value of a specific setting
	 *
	 * Note: By default, zero values are not allowed. If you have a custom
	 * setting that needs to allow 0 as a valid value, but sure to add its
	 * key to the filtered array seen in this method.
	 *
	 * @param string $key
	 * @param mixed $default (optional)
	 *
	 * @return mixed
	 * @since  1.0.2
	 */
	public function get( $key, $default = false ) {

		// Only allow non-empty values, otherwise fallback to the default
		$value = ! empty( $this->options[ $key ] ) ? $this->options[ $key ] : $default;

		$zero_values_allowed = array();

		/**
		 * Filters settings allowed to accept 0 as a valid value without
		 * falling back to the default.
		 *
		 * @param array $zero_values_allowed Array of setting IDs.
		 */
		$zero_values_allowed = (array) apply_filters( 'eaccounting_settings_zero_values_allowed', $zero_values_allowed );

		// Allow 0 values for specified keys only
		if ( in_array( $key, $zero_values_allowed ) ) {

			$value = isset( $this->options[ $key ] ) ? $this->options[ $key ] : null;
			$value = ( ! is_null( $value ) && '' !== $value ) ? $value : $default;

		}

		return $value;
	}

	/**
	 * Sets an option (in memory).
	 *
	 * @param array $settings An array of `key => value` setting pairs to set.
	 * @param bool $save Optional. Whether to trigger saving the option or options. Default false.
	 *
	 * @return bool If `$save` is not false, whether the options were saved successfully. True otherwise.
	 * @since 1.0.2
	 * @access public
	 *
	 */
	public function set( $settings, $save = false ) {
		foreach ( $settings as $option => $value ) {
			$this->options[ $option ] = $value;
		}

		if ( false !== $save ) {
			return $this->save();
		}

		return true;
	}

	/**
	 * Saves option values queued in memory.
	 *
	 * Note: If posting separately from the main settings submission process, this method should
	 * be called directly for direct saving to prevent memory pollution. Otherwise, this method
	 * is only accessible via the optional `$save` parameter in the set() method.
	 *
	 * @param array $options Optional. Options to save/overwrite directly. Default empty array.
	 *
	 * @return bool False if the options were not updated (saved) successfully, true otherwise.
	 * @since 1.0.2
	 *
	 */
	protected function save( $options = array() ) {
		$all_options = $this->get_all();

		if ( ! empty( $options ) ) {
			$all_options = array_merge( $all_options, $options );
		}

		$updated = update_option( 'eaccounting_settings', $all_options );

		// Refresh the options array available in memory (prevents unexpected race conditions).
		$this->options = get_option( 'eaccounting_settings', array() );

		return $updated;
	}

	/**
	 * Get all settings
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_all() {
		return $this->options;
	}

	/**
	 * Add all settings sections and fields
	 *
	 * @return void
	 * @since 1.0
	 */
	function register_settings() {

		if ( false == get_option( 'eaccounting_settings' ) ) {
			add_option( 'eaccounting_settings' );
		}

		foreach ( $this->get_registered_settings() as $tab => $settings ) {

			add_settings_section(
				'eaccounting_settings_' . $tab,
				__return_null(),
				'__return_false',
				'eaccounting_settings_' . $tab
			);

			foreach ( $settings as $key => $option ) {

				if ( $option['type'] == 'checkbox' || $option['type'] == 'multicheck' || $option['type'] == 'radio' ) {
					$name = isset( $option['name'] ) ? $option['name'] : '';
				} else {
					$name = isset( $option['name'] ) ? '<label for="eaccounting_settings[' . $key . ']">' . $option['name'] . '</label>' : '';
				}

				$callback = ! empty( $option['callback'] ) ? $option['callback'] : array( $this, $option['type'] . '_callback' );

				add_settings_field(
					'eaccounting_settings[' . $key . ']',
					$name,
					is_callable( $callback ) ? $callback : array( $this, 'missing_callback' ),
					'eaccounting_settings_' . $tab,
					'eaccounting_settings_' . $tab,
					array(
						'id'          => $key,
						'desc'        => ! empty( $option['desc'] ) ? $option['desc'] : '',
						'name'        => isset( $option['name'] ) ? $option['name'] : null,
						'section'     => $tab,
						'size'        => isset( $option['size'] ) ? $option['size'] : null,
						'max'         => isset( $option['max'] ) ? $option['max'] : null,
						'min'         => isset( $option['min'] ) ? $option['min'] : null,
						'step'        => isset( $option['step'] ) ? $option['step'] : null,
						'options'     => isset( $option['options'] ) ? $option['options'] : array(),
						'attr'        => isset( $option['attr'] ) ? $option['attr'] : array(),
						'std'         => isset( $option['std'] ) ? $option['std'] : '',
						'disabled'    => isset( $option['disabled'] ) ? $option['disabled'] : '',
						'class'       => isset( $option['class'] ) ? $option['class'] : '',
						'placeholder' => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
						'tooltip'     => isset( $option['tooltip'] ) ? $option['tooltip'] : '',
					)
				);
			}

		}

		// Creates our settings in the options table
		register_setting( 'eaccounting_settings', 'eaccounting_settings', array( $this, 'sanitize_settings' ) );

	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @return array
	 * @since 1.0.2
	 */
	function sanitize_settings( $input = array() ) {

		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}

		parse_str( $_POST['_wp_http_referer'], $referrer );

		$saved = get_option( 'affwp_settings', array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		$settings = $this->get_registered_settings();
		$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';

		$input = $input ? $input : array();

		/**
		 * Filters the input value for the settings tab.
		 *
		 * This filter is appended with the tab name, followed by the string `_sanitize`, for example:
		 *
		 *     `eaccounting_settings_misc_sanitize`
		 *     `eaccounting_settings_integrations_sanitize`
		 *
		 * @param mixed $input The settings tab content to sanitize.
		 *
		 * @since 1.0.2
		 *
		 */
		$input = apply_filters( 'eaccounting_settings_' . $tab . '_sanitize', $input );

		// Ensure a value is always passed for every checkbox
		if ( ! empty( $settings[ $tab ] ) ) {
			foreach ( $settings[ $tab ] as $key => $setting ) {

				// Single checkbox
				if ( isset( $settings[ $tab ][ $key ]['type'] ) && 'checkbox' == $settings[ $tab ][ $key ]['type'] ) {
					$input[ $key ] = ! empty( $input[ $key ] );
				}

				// Multicheck list
				if ( isset( $settings[ $tab ][ $key ]['type'] ) && 'multicheck' == $settings[ $tab ][ $key ]['type'] ) {
					if ( empty( $input[ $key ] ) ) {
						$input[ $key ] = array();
					}
				}
			}
		}

		// Loop through each setting being saved and pass it through a sanitization filter
		foreach ( $input as $key => $value ) {

			// Don't overwrite the global license key.
			if ( 'license_key' === $key ) {
				$value = self::get_license_key( $value, true );
			}

			// Get the setting type (checkbox, select, etc)
			$type              = isset( $settings[ $tab ][ $key ]['type'] ) ? $settings[ $tab ][ $key ]['type'] : false;
			$sanitize_callback = isset( $settings[ $tab ][ $key ]['sanitize_callback'] ) ? $settings[ $tab ][ $key ]['sanitize_callback'] : false;
			$input[ $key ]     = $value;

			if ( $type ) {

				if ( $sanitize_callback && is_callable( $sanitize_callback ) ) {

					add_filter( 'eaccounting_settings_sanitize_' . $type, $sanitize_callback, 10, 2 );

				}

				/**
				 * Filters the sanitized value for a setting of a given type.
				 *
				 * This filter is appended with the setting type (checkbox, select, etc), for example:
				 *
				 *     `eaccounting_settings_sanitize_checkbox`
				 *     `eaccounting_settings_sanitize_select`
				 *
				 * @param array $value The input array and settings key defined within.
				 * @param string $key The settings key.
				 *
				 * @since 1.0.2
				 *
				 */
				$input[ $key ] = apply_filters( 'eaccounting_settings_sanitize_' . $type, $input[ $key ], $key );
			}

			/**
			 * General setting sanitization filter
			 *
			 * @param array $input [ $key ] The input array and settings key defined within.
			 * @param string $key The settings key.
			 *
			 * @since 1.0
			 *
			 */
			$input[ $key ] = apply_filters( 'eaccounting_settings_sanitize', $input[ $key ], $key );

			// Now remove the filter
			if ( $sanitize_callback && is_callable( $sanitize_callback ) ) {

				remove_filter( 'eaccounting_settings_sanitize_' . $type, $sanitize_callback, 10 );

			}
		}

		add_settings_error( 'eaccounting-notices', '', __( 'Settings updated.', 'wp-ever-accounting' ), 'updated' );

		return array_merge( $saved, $input );

	}

	/**
	 * Sanitize text fields
	 *
	 * @return string
	 * @since 1.7
	 */
	public function sanitize_text_fields( $value = '', $key = '' ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize URL fields
	 *
	 * @return string
	 * @since 1.7.15
	 */
	public function sanitize_url_fields( $value = '', $key = '' ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize checkbox fields
	 *
	 * @return int
	 * @since 1.7
	 */
	public function sanitize_cb_fields( $value = '', $key = '' ) {
		return absint( $value );
	}

	/**
	 * Sanitize number fields
	 *
	 * @return int
	 * @since 1.7
	 */
	public function sanitize_number_fields( $value = '', $key = '' ) {
		return floatval( $value );
	}

	/**
	 * Sanitize rich editor fields
	 *
	 * @return int
	 * @since 1.7
	 */
	public function sanitize_rich_editor_fields( $value = '', $key = '' ) {
		return wp_kses_post( $value );
	}

	/**
	 * Set the capability needed to save affiliate settings
	 *
	 * @return string
	 * @since 1.9
	 */
	public function option_page_capability( $capability ) {
		return 'manage_options';
	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @return array
	 * @since 1.0
	 */
	function get_registered_settings() {

		// get currently logged in username
		$user_info = get_userdata( get_current_user_id() );
		$username  = $user_info ? esc_html( $user_info->user_login ) : '';

		/**
		 * Fires before attempting to retrieve registered settings.
		 *
		 * @param Settings $this Settings instance.
		 *
		 * @since 1.0.2
		 *
		 */
		do_action( 'eaccounting_pre_get_registered_settings', $this );

		$settings = array(
			/**
			 * Filters the default "General" settings.
			 *
			 * @param array $settings General settings.
			 *
			 * @since 1.0.2
			 *
			 */
			'general'         => apply_filters( 'eaccounting_settings_general',
				array(
					'license'              => array(
						'name' => '<strong>' . __( 'License Settings', 'wp-ever-accounting' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'license_key'          => array(
						'name'              => __( 'License Key', 'wp-ever-accounting' ),
						'desc'              => sprintf( __( 'Please enter your license key. An active license key is needed for automatic plugin updates and <a href="%s" target="_blank">support</a>.', 'wp-ever-accounting' ), 'https://affiliatewp.com/support/' ),
						'type'              => 'license',
						'sanitize_callback' => 'sanitize_text_field'
					),
					'pages'                => array(
						'name' => '<strong>' . __( 'Pages', 'wp-ever-accounting' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'terms_of_use_label'   => array(
						'name' => __( 'Terms of Use Label', 'wp-ever-accounting' ),
						'desc' => __( 'Enter the text you would like shown for the Terms of Use checkbox.', 'wp-ever-accounting' ),
						'type' => 'text',
						'std'  => __( 'Agree to our Terms of Use and Privacy Policy', 'wp-ever-accounting' )
					),
					'referrals'            => array(
						'name' => '<strong>' . __( 'Referral Settings', 'wp-ever-accounting' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'referral_rate'        => array(
						'name' => __( 'Referral Rate', 'wp-ever-accounting' ),
						'desc' => __( 'The default referral rate. A percentage if the Referral Rate Type is set to Percentage, a flat amount otherwise. Referral rates can also be set for each individual affiliate.', 'wp-ever-accounting' ),
						'type' => 'number',
						'size' => 'small',
						'step' => '0.01',
						'std'  => '20'
					),
					'exclude_shipping'     => array(
						'name' => __( 'Exclude Shipping', 'wp-ever-accounting' ),
						'desc' => __( 'Exclude shipping costs from referral calculations.', 'wp-ever-accounting' ),
						'type' => 'checkbox'
					),
					'exclude_tax'          => array(
						'name' => __( 'Exclude Tax', 'wp-ever-accounting' ),
						'desc' => __( 'Exclude taxes from referral calculations.', 'wp-ever-accounting' ),
						'type' => 'checkbox'
					),
					'cookie_exp'           => array(
						'name' => __( 'Cookie Expiration', 'wp-ever-accounting' ),
						'desc' => __( 'Enter how many days the referral tracking cookie should be valid for.', 'wp-ever-accounting' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '1',
					),
					'cookie_sharing'       => array(
						'name' => __( 'Cookie Sharing', 'wp-ever-accounting' ),
						'desc' => __( 'Share tracking cookies with sub-domains in a multisite install. When enabled, tracking cookies created on domain.com will also be available on sub.domain.com. Note: this only applies to WordPress Multisite installs.', 'wp-ever-accounting' ),
						'type' => 'checkbox',
					),
					'currency_settings'    => array(
						'name' => '<strong>' . __( 'Currency Settings', 'wp-ever-accounting' ) . '</strong>',
						'desc' => __( 'Configure the currency options', 'wp-ever-accounting' ),
						'type' => 'header'
					),
					'currency_position'    => array(
						'name'    => __( 'Currency Symbol Position', 'wp-ever-accounting' ),
						'desc'    => __( 'Choose the location of the currency symbol.', 'wp-ever-accounting' ),
						'type'    => 'select',
						'options' => array(
							'before' => __( 'Before - $10', 'wp-ever-accounting' ),
							'after'  => __( 'After - 10$', 'wp-ever-accounting' )
						)
					),
					'thousands_separator'  => array(
						'name' => __( 'Thousands Separator', 'wp-ever-accounting' ),
						'desc' => __( 'The symbol (usually , or .) to separate thousands', 'wp-ever-accounting' ),
						'type' => 'text',
						'size' => 'small',
						'std'  => ','
					),
					'decimal_separator'    => array(
						'name' => __( 'Decimal Separator', 'wp-ever-accounting' ),
						'desc' => __( 'The symbol (usually , or .) to separate decimal points', 'wp-ever-accounting' ),
						'type' => 'text',
						'size' => 'small',
						'std'  => '.'
					),
					'form_settings'        => array(
						'name' => '<strong>' . __( 'Affiliate Form Settings', 'wp-ever-accounting' ) . '</strong>',
						'type' => 'header'
					),
					'affiliate_area_forms' => array(
						'name'    => __( 'Affiliate Area Forms', 'wp-ever-accounting' ),
						'desc'    => sprintf( __( 'Select which form(s) to show on the Affiliate Area page. The affiliate registration form will only show if <a href="%s">Allow Affiliate Registration</a> is enabled.', 'wp-ever-accounting' ), admin_url( 'admin.php?page=affiliate-wp-settings&tab=misc' ) ),
						'type'    => 'select',
						'options' => array(),
						'attr'    => array(
							'data-nonce'       => wp_create_nonce( 'get_account' ),
							'data-footer'      => true,
							'data-search'      => eaccounting_esc_json( json_encode( array(
								'nonce'  => wp_create_nonce( 'dropdown-search' ),
								'type'   => 'account',
								'action' => 'eaccounting_dropdown_search',
							) ), true ),
							'data-modal'       => eaccounting_esc_json( json_encode( array(
								'event' => 'ea-init-account-modal',
								'type'  => 'account',
								'nonce' => 'edit_account',
							) ), true ),
							'data-placeholder' => __( 'Select Account', 'wp-ever-accounting' ),
						)
					),
				)
			),
			/** Opt-In Settings */

			/**
			 * Filters the default opt-in settings.
			 *
			 * @param array $opt_in_forms The opt in form settings.
			 *
			 * @since 1.0
			 *
			 */
			'opt_in_forms'    => apply_filters( 'affwp_settings_opt_in_forms',
				array(
					'opt_in_referral_amount' => array(
						'name' => __( 'Opt-In Referral Amount', 'wp-ever-accounting' ),
						'type' => 'number',
						'size' => 'small',
						'step' => '0.01',
						'std'  => '0.00',
						'desc' => __( 'Enter the amount affiliates should receive for each opt-in referral. Default is 0.00.', 'wp-ever-accounting' ),
					),
					'opt_in_referral_status' => array(
						'name'    => __( 'Opt-In Referral Status', 'wp-ever-accounting' ),
						'type'    => 'radio',
						'options' => array(
							'pending' => __( 'Pending', 'wp-ever-accounting' ),
							'unpaid'  => __( 'Unpaid', 'wp-ever-accounting' ),
						),
						'std'     => 'pending',
						'desc'    => __( 'Select the status that should be assigned to opt-in referrals by default.', 'wp-ever-accounting' ),
					),
					'opt_in_success_message' => array(
						'name' => __( 'Message shown upon opt-in success', 'wp-ever-accounting' ),
						'type' => 'rich_editor',
						'std'  => 'You have subscribed successfully.',
						'desc' => __( 'Enter the message you would like to show subscribers after they have opted-in successfully.', 'wp-ever-accounting' ),
					),
				)
			),
			/** Email Settings */

			/**
			 * Filters the default "Email" settings.
			 *
			 * @param array $settings Array of email settings.
			 *
			 * @since 1.0
			 *
			 */
			'emails'          => apply_filters( 'affwp_settings_emails',
				array(
					'email_options_header'              => array(
						'name' => '<strong>' . __( 'Email Options', 'wp-ever-accounting' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'email_logo'                        => array(
						'name' => __( 'Logo', 'wp-ever-accounting' ),
						'desc' => __( 'Upload or choose a logo to be displayed at the top of emails.', 'wp-ever-accounting' ),
						'type' => 'upload'
					),
					'from_name'                         => array(
						'name' => __( 'From Name', 'wp-ever-accounting' ),
						'desc' => __( 'The name that emails come from. This is usually your site name.', 'wp-ever-accounting' ),
						'type' => 'text',
						'std'  => get_bloginfo( 'name' )
					),
					'from_email'                        => array(
						'name' => __( 'From Email', 'wp-ever-accounting' ),
						'desc' => __( 'The email address to send emails from. This will act as the "from" and "reply-to" address.', 'wp-ever-accounting' ),
						'type' => 'text',
						'std'  => get_bloginfo( 'admin_email' )
					),
					'affiliate_manager_email'           => array(
						'name' => __( 'Affiliate Manager Email', 'wp-ever-accounting' ),
						'desc' => __( 'The email address(es) to receive affiliate manager notifications. Separate multiple email addresses with a comma (,). The admin email address will be used unless overridden.', 'wp-ever-accounting' ),
						'type' => 'text',
						'std'  => get_bloginfo( 'admin_email' ),
					),
					'registration_options_header'       => array(
						'name' => '<strong>' . __( 'Registration Email Options For Affiliate Manager', 'wp-ever-accounting' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'registration_subject'              => array(
						'name' => __( 'Registration Email Subject', 'wp-ever-accounting' ),
						'desc' => __( 'Enter the subject line for the registration email sent to affiliate managers when new affiliates register.', 'wp-ever-accounting' ),
						'type' => 'text',
						'std'  => __( 'New Affiliate Registration', 'wp-ever-accounting' )
					),
					'new_admin_referral_options_header' => array(
						'name' => '<strong>' . __( 'New Referral Email Options for Affiliate Manager', 'wp-ever-accounting' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'new_admin_referral_subject'        => array(
						'name' => __( 'New Referral Email Subject', 'wp-ever-accounting' ),
						'desc' => __( 'Enter the subject line for the email sent to site the site affiliate manager when affiliates earn referrals.', 'wp-ever-accounting' ),
						'type' => 'text',
						'std'  => __( 'Referral Earned!', 'wp-ever-accounting' )
					),
					'new_referral_options_header'       => array(
						'name' => '<strong>' . __( 'New Referral Email Options For Affiliate', 'wp-ever-accounting' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'referral_subject'                  => array(
						'name' => __( 'New Referral Email Subject', 'wp-ever-accounting' ),
						'desc' => __( 'Enter the subject line for new referral emails sent when affiliates earn referrals.', 'wp-ever-accounting' ),
						'type' => 'text',
						'std'  => __( 'Referral Awarded!', 'wp-ever-accounting' )
					),
					'accepted_options_header'           => array(
						'name' => '<strong>' . __( 'Application Accepted Email Options For Affiliate', 'wp-ever-accounting' ) . '</strong>',
						'desc' => '',
						'type' => 'header'
					),
					'accepted_subject'                  => array(
						'name' => __( 'Application Accepted Email Subject', 'wp-ever-accounting' ),
						'desc' => __( 'Enter the subject line for accepted application emails sent to affiliates when their account is approved.', 'wp-ever-accounting' ),
						'type' => 'text',
						'std'  => __( 'Affiliate Application Accepted', 'wp-ever-accounting' )
					),
				)
			),
			/** Misc Settings */

			/**
			 * Filters the default "Misc" settings.
			 *
			 * @param array $settings Array of misc settings.
			 *
			 * @since 1.0
			 *
			 */
			'misc'            => apply_filters( 'affwp_settings_misc',
				array(
					'allow_affiliate_registration' => array(
						'name' => __( 'Allow Affiliate Registration', 'wp-ever-accounting' ),
						'desc' => __( 'Allow users to register affiliate accounts for themselves.', 'wp-ever-accounting' ),
						'type' => 'checkbox'
					),
					'require_approval'             => array(
						'name' => __( 'Require Approval', 'wp-ever-accounting' ),
						'desc' => __( 'Require that Pending affiliate accounts must be approved before they can begin earning referrals.', 'wp-ever-accounting' ),
						'type' => 'checkbox'
					),
					'auto_register'                => array(
						'name' => __( 'Auto Register New Users', 'wp-ever-accounting' ),
						'desc' => __( 'Automatically register new users as affiliates.', 'wp-ever-accounting' ),
						'type' => 'checkbox'
					),
					'logout_link'                  => array(
						'name' => __( 'Logout Link', 'wp-ever-accounting' ),
						'desc' => __( 'Add a logout link to the Affiliate Area.', 'wp-ever-accounting' ),
						'type' => 'checkbox'
					),
					'default_referral_url'         => array(
						'name' => __( 'Default Referral URL', 'wp-ever-accounting' ),
						'desc' => __( 'The default referral URL shown in the Affiliate Area. Also changes the URL shown in the Referral URL Generator and the {referral_url} email tag.', 'wp-ever-accounting' ),
						'type' => 'url'
					),
					'recaptcha_enabled'            => array(
						'name' => __( 'Enable reCAPTCHA', 'wp-ever-accounting' ),
						'desc' => __( 'Prevent bots from registering affiliate accounts using Google reCAPTCHA.', 'wp-ever-accounting' ),
						'type' => 'checkbox'
					),
					'recaptcha_site_key'           => array(
						'name' => __( 'reCAPTCHA Site Key', 'wp-ever-accounting' ),
						'desc' => __( 'This is used to identify your site to Google reCAPTCHA.', 'wp-ever-accounting' ),
						'type' => 'text'
					),
					'recaptcha_secret_key'         => array(
						'name' => __( 'reCAPTCHA Secret Key', 'wp-ever-accounting' ),
						'desc' => __( 'This is used for communication between your site and Google reCAPTCHA. Be sure to keep it a secret.', 'wp-ever-accounting' ),
						'type' => 'text'
					),
					'revoke_on_refund'             => array(
						'name' => __( 'Reject Unpaid Referrals on Refund', 'wp-ever-accounting' ),
						'desc' => __( 'Automatically reject Unpaid referrals when the originating purchase is refunded or revoked.', 'wp-ever-accounting' ),
						'type' => 'checkbox'
					),
					'tracking_fallback'            => array(
						'name' => __( 'Use Fallback Referral Tracking Method', 'wp-ever-accounting' ),
						'desc' => __( 'The method used to track referral links can fail on sites that have jQuery errors. Enable Fallback Tracking if referrals are not being tracked properly.', 'wp-ever-accounting' ),
						'type' => 'checkbox'
					),
					'ignore_zero_referrals'        => array(
						'name' => __( 'Ignore Referrals with Zero Amount', 'wp-ever-accounting' ),
						'desc' => __( 'Ignore referrals with a zero amount. This can be useful for multi-price products that start at zero, or if a discount was used which resulted in a zero amount. NOTE: If this setting is enabled and a visit results in a zero referral, the visit will be considered not converted.', 'wp-ever-accounting' ),
						'type' => 'checkbox'
					),
					'disable_ip_logging'           => array(
						'name' => __( 'Disable IP Address Logging', 'wp-ever-accounting' ),
						'desc' => __( 'Disable logging of the customer IP address.', 'wp-ever-accounting' ),
						'type' => 'checkbox'
					),
					'referral_url_blacklist'       => array(
						'name' => __( 'Referral URL Blacklist', 'wp-ever-accounting' ),
						'desc' => __( 'URLs placed here will be blocked from generating referrals. Enter one URL per line. NOTE: This will only apply to new visits after the URL has been saved.', 'wp-ever-accounting' ),
						'type' => 'textarea'
					),
					'betas'                        => array(
						'name' => __( 'Opt into Beta Versions', 'wp-ever-accounting' ),
						'desc' => __( 'Receive update notifications for beta releases. When beta versions are available, an update notification will be shown on your Plugins page.', 'wp-ever-accounting' ),
						'type' => 'checkbox'
					),
					'uninstall_on_delete'          => array(
						'name' => __( 'Remove Data on Uninstall', 'wp-ever-accounting' ),
						'desc' => __( 'Remove all saved data for AffiliateWP when the plugin is deleted.', 'wp-ever-accounting' ),
						'type' => 'checkbox'
					)
				)
			),

			/**
			 * Filters the default "Payouts Service" settings.
			 *
			 * @param array $settings Array of settings.
			 *
			 * @since 2.4
			 *
			 */
			'payouts_service' => apply_filters( 'affwp_settings_payouts_service',
				array(

					'enable_payouts_service'      => array(
						'name' => __( 'Enable Payouts Service', 'wp-ever-accounting' ),
						'desc' => __( 'Enable the AffiliateWP Payouts Service.', 'wp-ever-accounting' ),
						'type' => 'checkbox',
					),
					'payouts_service_description' => array(
						'name' => __( 'Registration Form Description', 'wp-ever-accounting' ),
						'desc' => __( 'This will be displayed above the Payouts Service registration form fields. Here you can explain to your affiliates how/why to register for the Payouts Service.', 'wp-ever-accounting' ),
						'type' => 'textarea',
					),
					'payouts_service_notice'      => array(
						'name' => __( 'Payouts Service Notice', 'wp-ever-accounting' ),
						'desc' => __( 'This will be displayed at the top of each tab of the Affiliate Area for affiliates that have not registered their payout account.', 'wp-ever-accounting' ),
						'type' => 'textarea',
					),
				)
			),
		);

		/**
		 * Filters the entire default settings array.
		 *
		 * @param array $settings Array of default settings.
		 *
		 * @since 1.0.2
		 *
		 */
		return apply_filters( 'eaccounting_settings', $settings );
	}

	/**
	 * Email notifications
	 *
	 * @param boolean $install Whether or not the install script has been run.
	 *
	 * @return array $emails
	 * @since 2.2
	 */
	public function email_notifications( $install = false ) {

		$emails = array(
			'admin_affiliate_registration_email'   => __( 'Notify affiliate manager when a new affiliate has registered', 'wp-ever-accounting' ),
			'admin_new_referral_email'             => __( 'Notify affiliate manager when a new referral has been created', 'wp-ever-accounting' ),
			'affiliate_new_referral_email'         => __( 'Notify affiliate when they earn a new referral', 'wp-ever-accounting' ),
			'affiliate_application_accepted_email' => __( 'Notify affiliate when their affiliate application is accepted', 'wp-ever-accounting' ),
		);

		if ( $this->get( 'require_approval' ) || true === $install ) {
			$emails['affiliate_application_pending_email']  = __( 'Notify affiliate when their affiliate application is pending', 'wp-ever-accounting' );
			$emails['affiliate_application_rejected_email'] = __( 'Notify affiliate when their affiliate application is rejected', 'wp-ever-accounting' );
		}

		return $emails;

	}

	/**
	 * Header Callback
	 *
	 * Renders the header.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 * @since 1.0.2
	 */
	function header_callback( $args ) {
		echo '<hr/>';
	}

	/**
	 * Checkbox Callback
	 *
	 * Renders checkboxes.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 * @global $this ->options Array of all the AffiliateWP Options
	 * @since 1.0
	 */
	function checkbox_callback( $args ) {

		$checked    = isset( $this->options[ $args['id'] ] ) ? checked( 1, $this->options[ $args['id'] ], false ) : '';
		$disabled   = $this->is_setting_disabled( $args ) ? disabled( $args['disabled'], true, false ) : '';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$html       = '<label for="eaccounting_settings[' . $args['id'] . ']">';
		$html       .= '<input type="checkbox" id="eaccounting_settings[' . $args['id'] . ']" name="eaccounting_settings[' . $args['id'] . ']" value="1" ' . $checked . ' ' . $disabled . ' ' . $attributes . '/>&nbsp;';
		$html       .= $args['desc'];
		$html       .= '</label>';

		echo $html;
	}

	/**
	 * Multicheck Callback
	 *
	 * Renders multiple checkboxes.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 * @global $this ->options Array of all the AffiliateWP Options
	 * @since 1.0
	 */
	function multicheck_callback( $args ) {

		if ( ! empty( $args['options'] ) ) {
			foreach ( $args['options'] as $key => $option ) {
				if ( isset( $this->options[ $args['id'] ][ $key ] ) ) {
					$enabled = $option;
				} else {
					$enabled = null;
				}
				echo '<label for="eaccounting_settings[' . $args['id'] . '][' . $key . ']">';
				echo '<input name="eaccounting_settings[' . $args['id'] . '][' . $key . ']" id="eaccounting_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked( $option, $enabled, false ) . '/>&nbsp;';
				echo $option . '</label><br/>';
			}
			echo '<p class="description">' . $args['desc'] . '</p>';
		}
	}

	/**
	 * Radio Callback
	 *
	 * Renders radio boxes.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 * @global $this ->options Array of all the AffiliateWP Options
	 * @since 1.0
	 */
	function radio_callback( $args ) {

		echo '<fieldset id="eaccounting_settings[' . $args['id'] . ']">';
		echo '<legend class="screen-reader-text">' . $args['name'] . '</legend>';

		foreach ( $args['options'] as $key => $option ) :
			$checked = false;

			if ( isset( $this->options[ $args['id'] ] ) && $this->options[ $args['id'] ] == $key ) {
				$checked = true;
			} elseif ( isset( $args['std'] ) && $args['std'] == $key && ! isset( $this->options[ $args['id'] ] ) ) {
				$checked = true;
			}

			echo '<label for="eaccounting_settings[' . $args['id'] . '][' . $key . ']">';
			echo '<input name="eaccounting_settings[' . $args['id'] . ']" id="eaccounting_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( true, $checked, false ) . '/>';
			echo $option . '</label><br/>';
		endforeach;

		echo '</fieldset><p class="description">' . $args['desc'] . '</p>';
	}

	/**
	 * Text Callback
	 *
	 * Renders text fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 * @global $this ->options Array of all the AffiliateWP Options
	 * @since 1.0
	 */
	function text_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) && ! empty( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		// Must use a 'readonly' attribute over disabled to ensure the value is passed in $_POST.
		$readonly   = $this->is_setting_disabled( $args ) ? __checked_selected_helper( $args['disabled'], true, false, 'readonly' ) : '';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html       = '<input type="text" class="' . $size . '-text" id="eaccounting_settings[' . $args['id'] . ']" name="eaccounting_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" ' . $readonly . ' ' . $attributes . '/>';
		$html       .= '<p class="description">' . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * URL Callback
	 *
	 * Renders URL fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 * @global $this ->options Array of all the AffiliateWP Options
	 * @since 1.7.15
	 */
	function url_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html       = '<input type="url" class="' . $size . '-text" id="eaccounting_settings[' . $args['id'] . ']" name="eaccounting_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" ' . $attributes . '/>';
		$html       .= '<p class="description">' . $args['desc'] . '</p>';

		echo $html;
	}


	/**
	 * Number Callback
	 *
	 * Renders number fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 * @global $this ->options Array of all the AffiliateWP Options
	 * @since 1.9
	 */
	function number_callback( $args ) {

		// Get value, with special consideration for 0 values, and never allowing negative values
		$value = isset( $this->options[ $args['id'] ] ) ? $this->options[ $args['id'] ] : null;
		$value = ( ! is_null( $value ) && '' !== $value && floatval( $value ) >= 0 ) ? floatval( $value ) : null;

		// Saving the field empty will revert to std value, if it exists
		$std   = ( isset( $args['std'] ) && ! is_null( $args['std'] ) && '' !== $args['std'] && floatval( $args['std'] ) >= 0 ) ? $args['std'] : null;
		$value = ! is_null( $value ) ? $value : ( ! is_null( $std ) ? $std : null );
		$value = eaccounting_round_number( $value );

		// Other attributes and their defaults
		$max        = isset( $args['max'] ) ? $args['max'] : 999999999;
		$min        = isset( $args['min'] ) ? $args['min'] : 0;
		$step       = isset( $args['step'] ) ? $args['step'] : 1;
		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$html       = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="eaccounting_settings[' . $args['id'] . ']" name="eaccounting_settings[' . $args['id'] . ']" placeholder="' . esc_attr( $std ) . '" value="' . esc_attr( stripslashes( $value ) ) . '" ' . $attributes . '/>';
		$html       .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Textarea Callback
	 *
	 * Renders textarea fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 * @global $this ->options Array of all the AffiliateWP Options
	 * @since 1.0
	 */
	function textarea_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html       = '<textarea class="large-text" cols="50" rows="5" id="eaccounting_settings_' . $args['id'] . '" name="eaccounting_settings[' . $args['id'] . ']" ' . $attributes . '>' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
		$html       .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Password Callback
	 *
	 * Renders password fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 * @global $this ->options Array of all the AffiliateWP Options
	 * @since 1.3
	 */
	function password_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html       = '<input type="password" class="' . $size . '-text" id="eaccounting_settings[' . $args['id'] . ']" name="eaccounting_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . ' ' . $attributes . '"/>';
		$html       .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Missing Callback
	 *
	 * If a function is missing for settings callbacks alert the user.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 * @since 1.3.1
	 */
	function missing_callback( $args ) {
		printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'wp-ever-accounting' ), $args['id'] );
	}

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 * @global $this ->options Array of all the AffiliateWP Options
	 * @since 1.0
	 */
	function select_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$html       = '<select class="large-text" id="eaccounting_settings[' . $args['id'] . ']" name="eaccounting_settings[' . $args['id'] . ']" ' . $attributes . '/>';

		foreach ( $args['options'] as $option => $name ) :
			$selected = selected( $option, $value, false );
			$html     .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
		endforeach;

		$html .= '</select>';
		$html .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Rich Editor Callback
	 *
	 * Renders rich editor fields.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @since 1.0
	 * @global $this ->options Array of all the AffiliateWP Options
	 * @global $wp_version WordPress Version
	 */
	function rich_editor_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		ob_start();
		wp_editor( stripslashes( $value ), 'eaccounting_settings_' . $args['id'], array( 'textarea_name' => 'eaccounting_settings[' . $args['id'] . ']' ) );
		$html = ob_get_clean();

		$html .= '<br/><p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Upload Callback
	 *
	 * Renders file upload fields.
	 *
	 * @param array $args Arguements passed by the setting
	 *
	 * @since 1.6
	 */
	function upload_callback( $args ) {
		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $size . '-text" id="eaccounting_settings[' . $args['id'] . ']" name="eaccounting_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . ' ' . $attibutes . '"/>';
		$html .= '<span>&nbsp;<input type="button" class="eaccounting_settings_upload_button button-secondary" value="' . __( 'Upload File', 'wp-ever-accounting' ) . '"/></span>';
		$html .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Descriptive text callback.
	 *
	 * Renders descriptive text onto the settings field.
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 * @since 2.4
	 */
	function descriptive_text_callback( $args ) {
		$html = wp_kses_post( $args['desc'] );

		echo $html;
	}

	/**
	 * Determines whether a setting is disabled.
	 *
	 * @param array $args Setting arguments.
	 *
	 * @return bool True or false if the setting is disabled, otherwise false.
	 * @since 1.8.3
	 * @access public
	 *
	 */
	public function is_setting_disabled( $args ) {
		if ( isset( $args['disabled'] ) ) {
			return $args['disabled'];
		}

		return false;
	}
}
