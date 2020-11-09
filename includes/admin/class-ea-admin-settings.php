<?php
/**
 * Admin Settings.
 *
 * @since       1.0.2
 * @subpackage  Admin
 * @package     EverAccounting
 */

namespace EverAccounting\Admin;

use EverAccounting\Exception;
use EverAccounting\Query_Account;
use EverAccounting\Query_Currency;

/**
 * Class Settings
 *
 * @since   1.0.2
 * @package EverAccounting\Admin
 */
class Settings {
	/**
	 * Contains all settings option.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	private $options;

	/**
	 * Get things started
	 *
	 * @since 1.0
	 * @return void
	 */
	public function __construct() {

		$this->options = (array) get_option( 'eaccounting_settings', array() );

		// Set up.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'update_option_eaccounting_settings', array( $this, 'eaccounting_settings_updated' ), 10, 3 );

		// Sanitization.
		add_filter( 'eaccounting_settings_sanitize_text', array( $this, 'sanitize_text_fields' ), 10, 2 );
		add_filter( 'eaccounting_settings_sanitize_url', array( $this, 'sanitize_url_fields' ), 10, 2 );
		add_filter( 'eaccounting_settings_sanitize_checkbox', array( $this, 'sanitize_cb_fields' ), 10, 2 );
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
	 * @since  1.0.2
	 *
	 * @param mixed  $default (optional)
	 *
	 * @param string $key
	 *
	 * @return mixed
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
	 * @since  1.0.2
	 * @access public
	 *
	 * @param bool  $save     Optional. Whether to trigger saving the option or options. Default false.
	 *
	 * @param array $settings An array of `key => value` setting pairs to set.
	 *
	 * @return bool If `$save` is not false, whether the options were saved successfully. True otherwise.
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
	 * @since 1.0.2
	 *
	 * @param array $options Optional. Options to save/overwrite directly. Default empty array.
	 *
	 * @return bool False if the options were not updated (saved) successfully, true otherwise.
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
	 * @since 1.0.2
	 * @return array
	 */
	public function get_all() {
		return $this->options;
	}

	/**
	 * Add all settings sections and fields
	 *
	 * @since 1.0.2
	 * @return void
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
						'class'       => isset( $option['wrap_class'] ) ? $option['wrap_class'] : '',
						'input_class' => isset( $option['class'] ) ? $option['class'] : '',
						'placeholder' => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
						'tooltip'     => isset( $option['tooltip'] ) ? $option['tooltip'] : '',
						'style'       => isset( $option['style'] ) ? $option['style'] : '',
					)
				);
			}
		}

		// Creates our settings in the options table
		register_setting( 'eaccounting_settings', 'eaccounting_settings', array( $this, 'sanitize_settings' ) );

	}


	function eaccounting_settings_updated( $old_value, $value, $option ) {
		//update currency code.
		if ( ! empty( $value['default_currency'] ) ) {
			$currency = eaccounting_get_currency( eaccounting_clean( $value['default_currency'] ) );
			if ( $currency->exists() ) {
				try {
					$currency->set_rate( 1 );
					$currency->save();
				} catch ( Exception $exception ) {
					eaccounting_logger()->error( __( 'Failed updating default currency code rate', 'wp-ever-accounting' ) );
				}
			}
		}

		/**
		 * Hook when update plugin settings.
		 *
		 * @since 1.0.2
		 *
		 * @param array $old_value Old settings value.
		 *
		 * @param array $value     The new settings value is being saved.
		 */
		do_action( 'eaccounting_settings_updated', $value, $old_value );
	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 1.0.2
	 * @return array
	 */
	function sanitize_settings( $input = array() ) {
		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}

		parse_str( $_POST['_wp_http_referer'], $referrer );

		$saved = get_option( 'eaccounting_settings', array() );
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
		 * @since 1.0.2
		 *
		 * @param mixed $input The settings tab content to sanitize.
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
				 * @since 1.0.2
				 *
				 * @param string $key   The settings key.
				 *
				 * @param array  $value The input array and settings key defined within.
				 */
				$input[ $key ] = apply_filters( 'eaccounting_settings_sanitize_' . $type, $input[ $key ], $key );
			}

			/**
			 * General setting sanitization filter
			 *
			 * @since 1.0
			 *
			 * @param string $key   The settings key.
			 *
			 * @param array  $input [ $key ] The input array and settings key defined within.
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
	 * @since 1.0.2
	 * @return string
	 */
	public function sanitize_text_fields( $value = '', $key = '' ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize URL fields
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function sanitize_url_fields( $value = '', $key = '' ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize checkbox fields
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function sanitize_cb_fields( $value = '', $key = '' ) {
		return absint( $value );
	}

	/**
	 * Sanitize number fields
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function sanitize_number_fields( $value = '', $key = '' ) {
		return floatval( $value );
	}

	/**
	 * Sanitize rich editor fields
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function sanitize_rich_editor_fields( $value = '', $key = '' ) {
		return wp_kses_post( $value );
	}

	/**
	 * Set the capability needed to save settings
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function option_page_capability( $capability ) {
		return 'ea_manage_options';
	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 1.0.2
	 * @return array
	 */
	function get_registered_settings() {
		/**
		 * Fires before attempting to retrieve registered settings.
		 *
		 * @since 1.0.2
		 *
		 * @param Settings $this Settings instance.
		 *
		 */
		do_action( 'eaccounting_pre_get_registered_settings', $this );
		$accounts   = array();
		$currencies = array();
		if ( eaccounting_is_admin_page( 'ea-settings' ) ) {
			//          $accounts   = \EverAccounting\Accounts\query()->select( 'id, name' )->get_results();
			//          $currencies = \EverAccounting\Currencies\query()->select( 'code, CONCAT(name,"(", symbol, ")") as name' )->get_results();
		}

		$settings = array(
			/**
			 * Filters the default "General" settings.
			 *
			 * @since 1.0.2
			 *
			 * @param array $settings General settings.
			 *
			 */
			'general' => apply_filters(
				'eaccounting_settings_general',
				array(
					'company_settings'       => array(
						'name' => '<strong>' . __( 'Company Settings', 'wp-ever-accounting' ) . '</strong>',
						'desc' => '',
						'type' => 'header',
					),
					'company_name'           => array(
						'name' => __( 'Name', 'wp-ever-accounting' ),
						'type' => 'text',
						'attr' => array(
							'required'    => 'required',
							'placeholder' => __( 'XYZ Company', 'wp-ever-accounting' ),
						),
					),
					'company_email'          => array(
						'name'              => __( 'Email', 'wp-ever-accounting' ),
						'type'              => 'text',
						'std'               => get_option( 'admin_email' ),
						'sanitize_callback' => 'sanitize_email',
					),
					'company_phone'          => array(
						'name' => __( 'Phone Number', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_tax_number'     => array(
						'name' => __( 'Tax Number', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_city'           => array(
						'name' => __( 'City', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_address'        => array(
						'name' => __( 'Address', 'wp-ever-accounting' ),
						'type' => 'textarea',
					),
					'company_state'          => array(
						'name' => __( 'State', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_postcode'       => array(
						'name' => __( 'Postcode', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_country'        => array(
						'name'    => __( 'Country', 'wp-ever-accounting' ),
						'type'    => 'select',
						'class'   => 'ea-select2',
						'options' => array( '' => __( 'Select Country', 'wp-ever-accounting' ) ) + eaccounting_get_countries(),
					),
					'company_logo'           => array(
						'name' => __( 'Logo', 'wp-ever-accounting' ),
						'type' => 'upload',
					),
					'local_settings'         => array(
						'name' => '<strong>' . __( 'Localisation Settings', 'wp-ever-accounting' ) . '</strong>',
						'desc' => '',
						'type' => 'header',
					),
					'financial_year_start'   => array(
						'name'  => __( 'Financial Year Start', 'wp-ever-accounting' ),
						'std'   => '01-01',
						'class' => 'ea-financial-start',
						'type'  => 'text',
					),
					'default_settings'       => array(
						'name' => '<strong>' . __( 'Default Settings', 'wp-ever-accounting' ) . '</strong>',
						'desc' => '',
						'type' => 'header',
					),
					'default_account'        => array(
						'name'    => __( 'Account', 'wp-ever-accounting' ),
						'type'    => 'select',
						'class'   => 'ea-select2',
						'options' => array( '' => __( 'Select default account', 'wp-ever-accounting' ) ) + wp_list_pluck( $accounts, 'name', 'id' ),
						'attr'    => array(
							'data-placeholder' => __( 'Select Account', 'wp-ever-accounting' ),
						),
					),
					'default_currency'       => array(
						'name'    => __( 'Currency', 'wp-ever-accounting' ),
						'type'    => 'select',
						//'std'     => 'USD',
						'desc'    => __( 'Default currency rate will update to 1', 'wp-ever-accounting' ),
						'class'   => 'ea-select2',
						'options' => array( '' => __( 'Select default currency', 'wp-ever-accounting' ) ) + wp_list_pluck( $currencies, 'name', 'code' ),
						'attr'    => array(
							'data-placeholder' => __( 'Select Currency', 'wp-ever-accounting' ),
						),
					),
					'default_payment_method' => array(
						'name'    => __( 'Payment Method', 'wp-ever-accounting' ),
						'std'     => 'cash',
						'type'    => 'select',
						'options' => eaccounting_get_payment_methods(),
					),
				)
			),
			'misc'    => array(),
		);

		/**
		 * Filters the entire default settings array.
		 *
		 * @since 1.0.2
		 *
		 * @param array $settings Array of default settings.
		 *
		 */
		return apply_filters( 'eaccounting_settings', $settings );
	}


	/**
	 * Header Callback
	 *
	 * Renders the header.
	 *
	 * @since 1.0.2
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function header_callback( $args ) {
		echo '<hr/>';
	}

	/**
	 * Checkbox Callback
	 *
	 * Renders checkboxes.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function checkbox_callback( $args ) {

		$checked    = isset( $this->options[ $args['id'] ] ) ? checked( '1', $this->options[ $args['id'] ], false ) : '';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$html       = '<label for="eaccounting_settings[' . $args['id'] . ']">';
		$html      .= '<input type="checkbox" id="eaccounting_settings[' . $args['id'] . ']" name="eaccounting_settings[' . $args['id'] . ']" value="yes" ' . $checked . ' ' . $attributes . '/>&nbsp;';
		$html      .= $args['desc'];
		$html      .= '</label>';

		echo $html;
	}

	/**
	 * Multicheck Callback
	 *
	 * Renders multiple checkboxes.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
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
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
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
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function text_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) && ! empty( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$tooltip    = ! empty( $args['tooltip'] ) ? eaccounting_help_tip( $args['tooltip'] ) : '';
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = $tooltip;
		$html .= sprintf(
			'<input type="text" class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" value="%s" %s/>',
			esc_attr( $size ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			$attributes
		);
		$html .= $desc;

		echo $html;
	}

	/**
	 * URL Callback
	 *
	 * Renders URL fields.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function url_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) && ! empty( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$tooltip    = ! empty( $args['tooltip'] ) ? eaccounting_help_tip( $args['tooltip'] ) : '';
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = $tooltip;
		$html .= sprintf(
			'<input type="url" class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" value="%s" %s/>',
			esc_attr( $size ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			$attributes
		);
		$html .= $desc;

		echo $html;
	}


	/**
	 * Number Callback
	 *
	 * Renders number fields.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function number_callback( $args ) {

		// Get value, with special consideration for 0 values, and never allowing negative values
		$value = isset( $this->options[ $args['id'] ] ) ? $this->options[ $args['id'] ] : null;
		$value = ( ! is_null( $value ) && '' !== $value && floatval( $value ) >= 0 ) ? floatval( $value ) : null;

		// Saving the field empty will revert to std value, if it exists
		$std   = ( isset( $args['std'] ) && ! is_null( $args['std'] ) && '' !== $args['std'] && floatval( $args['std'] ) >= 0 ) ? $args['std'] : null;
		$value = ! is_null( $value ) ? $value : ( ! is_null( $std ) ? $std : null );
		$value = eaccounting_round_number( $value );

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$tooltip    = ! empty( $args['tooltip'] ) ? eaccounting_help_tip( $args['tooltip'] ) : '';
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = $tooltip;
		$html .= sprintf(
			'<input type="number" class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" value="%s" %s/>',
			esc_attr( $size ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			$attributes
		);
		$html .= $desc;

		echo $html;
	}

	/**
	 * Textarea Callback
	 *
	 * Renders textarea fields.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function textarea_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$tooltip    = ! empty( $args['tooltip'] ) ? eaccounting_help_tip( $args['tooltip'] ) : '';
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = $tooltip;
		$html .= sprintf(
			'<textarea type="text" class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" %s>%s</textarea>',
			esc_attr( $size ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			$attributes,
			esc_textarea( stripslashes( $value ) )
		);
		$html .= $desc;

		echo $html;

	}

	/**
	 * Password Callback
	 *
	 * Renders password fields.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function password_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$tooltip    = ! empty( $args['tooltip'] ) ? eaccounting_help_tip( $args['tooltip'] ) : '';
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = $tooltip;
		$html .= sprintf(
			'<input type="password" class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" value="%s" %s/>',
			esc_attr( $size ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			$attributes
		);
		$html .= $desc;

		echo $html;
	}

	/**
	 * Missing Callback
	 *
	 * If a function is missing for settings callbacks alert the user.
	 *
	 * @since 1.0.2
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function missing_callback( $args ) {
		printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'wp-ever-accounting' ), $args['id'] );
	}

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function select_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$tooltip    = ! empty( $args['tooltip'] ) ? eaccounting_help_tip( $args['tooltip'] ) : '';
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = $tooltip;
		$html .= sprintf(
			'<select class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" %s>',
			$size,
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			$attributes
		);

		foreach ( $args['options'] as $key => $option_value ) {
			$html .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), eaccounting_selected( esc_attr( $key ), esc_attr( $value ) ), esc_html( $option_value ) );
		}

		$html .= '</select>';
		$html .= $desc;

		echo $html;
	}

	/**
	 * Rich Editor Callback
	 *
	 * Renders rich editor fields.
	 *
	 * @since 1.0.2
	 * @global        $this       ->options Array of all the EverAccounting Options
	 * @global string $wp_version WordPress Version
	 *
	 * @param array   $args       Arguments passed by the setting
	 *
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
	 * @since 1.0.2
	 *
	 * @param array $args Arguements passed by the setting
	 *
	 */
	function upload_callback( $args ) {
		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$tooltip    = ! empty( $args['tooltip'] ) ? eaccounting_help_tip( $args['tooltip'] ) : '';
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = $tooltip;
		$html .= sprintf(
			'<input type="text" class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" value="%s" %s/>',
			esc_attr( $size ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			$attributes
		);
		$html .= sprintf( '<span>&nbsp;<input type="button" class="ea_settings_upload_button button-secondary" value="%s"/></span>', __( 'Upload File', 'wp-ever-accounting' ) );
		$html .= $desc;

		echo $html;
	}

}
