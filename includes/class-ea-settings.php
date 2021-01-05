<?php
/**
 * EverAccounting Settings.
 *
 * @since       1.0.2
 * @subpackage  Classes
 * @package     EverAccounting
 */

/**
 * Class Settings
 *
 * @since   1.0.2
 * @package EverAccounting\Admin
 */
class EAccounting_Settings {
	/**
	 * Stores all settings.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Settings constructor.
	 *
	 */
	public function __construct() {
		$this->settings = (array) get_option( 'eaccounting_settings', array() );

		// Set up.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'eaccounting_settings_sanitize_text', 'sanitize_text_field' );
		add_filter( 'eaccounting_settings_sanitize_url', 'wp_http_validate_url' );
		add_filter( 'eaccounting_settings_sanitize_checkbox', 'eaccounting_bool_to_string' );
		add_filter( 'eaccounting_settings_sanitize_number', 'absint' );
		add_filter( 'eaccounting_settings_sanitize_rich_editor', 'wp_kses_post' );
	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 1.0.2
	 * @return array
	 */
	function get_registered_settings() {
		$currencies = eaccounting_get_currencies(
			array(
				'number' => - 1,
				'return' => 'raw',
			)
		);
		$accounts   = eaccounting_get_accounts(
			array(
				'include' => eaccounting()->settings->get( 'default_account' ),
				'return'  => 'raw',
			)
		);

		$settings = array(
			/** General Settings */
			'general' => apply_filters(
				'eaccounting_settings_general',
				array(
					/** General Main Section */
					'main'     => array(
						'company_settings'       => array(
							'name' => __( 'Company Settings', 'wp-ever-accounting' ),
							'desc' => '',
							'type' => 'header',
						),
						'company_name'           => array(
							'name'        => __( 'Name', 'wp-ever-accounting' ),
							'type'        => 'text',
							'tip'         => 'XYZ Company',
							'required'    => 'required',
							'placeholder' => __( 'XYZ Company', 'wp-ever-accounting' ),
						),
						'company_email'          => array(
							'name'              => __( 'Email', 'wp-ever-accounting' ),
							'type'              => 'email',
							'std'               => get_option( 'admin_email' ),
							'sanitize_callback' => 'sanitize_email',
						),
						'company_phone'          => array(
							'name' => __( 'Phone Number', 'wp-ever-accounting' ),
							'type' => 'text',
						),
						'company_vat_number'     => array(
							'name' => __( 'VAT Number', 'wp-ever-accounting' ),
							'type' => 'text',
						),
						'company_address'        => array(
							'name' => __( 'Street', 'wp-ever-accounting' ),
							'type' => 'text',
						),
						'company_city'           => array(
							'name' => __( 'City', 'wp-ever-accounting' ),
							'type' => 'text',
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
							'name'        => __( 'Country', 'wp-ever-accounting' ),
							'type'        => 'select',
							'input_class' => 'ea-select2',
							'options'     => array( '' => __( 'Select Country', 'wp-ever-accounting' ) ) + eaccounting_get_countries(),
						),
						'company_logo'           => array(
							'name' => __( 'Logo', 'wp-ever-accounting' ),
							'type' => 'upload',
						),
						'general_settings'       => array(
							'name' => __( 'General Settings', 'wp-ever-accounting' ),
							'desc' => '',
							'type' => 'header',
						),
						'financial_year_start'   => array(
							'name'  => __( 'Financial Year Start', 'wp-ever-accounting' ),
							'std'   => '01-01',
							'class' => 'ea-financial-start',
							'type'  => 'text',
						),
						'tax_enabled'            => array(
							'name' => __( 'Enable taxes', 'wp-ever-accounting' ),
							'desc' => 'Enable tax rates and calculations',
							'type' => 'checkbox',
						),
						'local_settings'         => array(
							'name' => __( 'Default Settings', 'wp-ever-accounting' ),
							'desc' => '',
							'type' => 'header',
						),
						'default_account'        => array(
							'name'        => __( 'Account', 'wp-ever-accounting' ),
							'type'        => 'select',
							'input_class' => 'ea-select2',
							'options'     => array( '' => __( 'Select default account', 'wp-ever-accounting' ) ) + wp_list_pluck( $accounts, 'name', 'id' ),
							'attr'        => array(
								'data-placeholder' => __( 'Select Account', 'wp-ever-accounting' ),
								'data-url'         => eaccounting()->ajax_url(),
								'data-ajax_action' => 'eaccounting_get_accounts',
								'data-nonce'       => wp_create_nonce( 'ea_get_accounts' ),
								'data-map'         => 'return {text: option.name + " (" + option.currency_code +")"  , id:option.id}',
								'data-modal_id'    => '#ea-modal-add-account',
								'data-add_text'    => __( 'Add New', 'wp-ever-accounting' ),
							),
						),
						'default_currency'       => array(
							'name'        => __( 'Currency', 'wp-ever-accounting' ),
							'type'        => 'select',
							'std'         => 'USD',
							'desc'        => __( 'Default currency rate will update to 1', 'wp-ever-accounting' ),
							'input_class' => 'ea-select2',
							'options'     => array( '' => __( 'Select default currency', 'wp-ever-accounting' ) ) + wp_list_pluck( array_values( $currencies ), 'name', 'code' ),
							'attr'        => array(
								'data-placeholder' => __( 'Select Currency', 'wp-ever-accounting' ),
								'data-url'         => eaccounting()->ajax_url(),
								'data-ajax_action' => 'eaccounting_get_currencies',
								'data-nonce'       => wp_create_nonce( 'ea_get_currencies' ),
								'data-map'         => 'return {text: option.name + " (" + option.symbol +")"  , id:option.code}',
								'data-modal_id'    => '#ea-modal-add-currency',
								'data-add_text'    => __( 'Add New', 'wp-ever-accounting' ),
							),
						),
						'default_payment_method' => array(
							'name'    => __( 'Payment Method', 'wp-ever-accounting' ),
							'std'     => 'cash',
							'type'    => 'select',
							'options' => eaccounting_get_payment_methods(),
						),
					),
					'invoices' => array(
						'invoice_prefix'         => array(
							'name' => __( 'Invoice Prefix', 'wp-ever-accounting' ),
							'std'  => 'INV-',
							'type' => 'text',
						),
						'invoice_digit'          => array(
							'name' => __( 'Minimum Digits', 'wp-ever-accounting' ),
							'std'  => '5',
							'type' => 'number',
						),
						'invoice_terms'          => array(
							'name' => __( 'Invoice Terms', 'wp-ever-accounting' ),
							'std'  => '',
							'type' => 'textarea',
						),
						'invoice_due'            => array(
							'name'    => __( 'Invoice Due', 'wp-ever-accounting' ),
							'std'     => '15',
							'type'    => 'select',
							'options' => array(
								'7'  => __( 'Due within 7 days', 'wp-ever-accounting' ),
								'15' => __( 'Due within 15 days', 'wp-ever-accounting' ),
								'30' => __( 'Due within 30 days', 'wp-ever-accounting' ),
								'45' => __( 'Due within 45 days', 'wp-ever-accounting' ),
								'60' => __( 'Due within 60 days', 'wp-ever-accounting' ),
								'90' => __( 'Due within 90 days', 'wp-ever-accounting' ),
							),
						),
						'invoice_item_label'     => array(
							'name' => __( 'Item Label', 'wp-ever-accounting' ),
							'std'  => __( 'Item', 'wp-ever-accounting' ),
							'type' => 'text',
						),
						'invoice_price_label'    => array(
							'name' => __( 'Price Label', 'wp-ever-accounting' ),
							'std'  => __( 'Price', 'wp-ever-accounting' ),
							'type' => 'text',
						),
						'invoice_quantity_label' => array(
							'name' => __( 'Quantity Label', 'wp-ever-accounting' ),
							'std'  => __( 'Quantity', 'wp-ever-accounting' ),
							'type' => 'text',
						),
					),
					'bills'    => array(
						'bill_prefix'         => array(
							'name' => __( 'Bill Prefix', 'wp-ever-accounting' ),
							'std'  => 'BILL-',
							'type' => 'text',

						),
						'bill_digit'          => array(
							'name' => __( 'Bill Digits', 'wp-ever-accounting' ),
							'std'  => '5',
							'type' => 'number',

						),
						'bill_due'            => array(
							'name'    => __( 'Bill Due', 'wp-ever-accounting' ),
							'std'     => '15',
							'type'    => 'select',
							'options' => array(
								'7'  => __( 'Due within 7 days', 'wp-ever-accounting' ),
								'15' => __( 'Due within 15 days', 'wp-ever-accounting' ),
								'30' => __( 'Due within 30 days', 'wp-ever-accounting' ),
								'45' => __( 'Due within 45 days', 'wp-ever-accounting' ),
								'60' => __( 'Due within 60 days', 'wp-ever-accounting' ),
								'90' => __( 'Due within 90 days', 'wp-ever-accounting' ),
							),
						),
						'bill_note'           => array(
							'name' => __( 'Bill Note', 'wp-ever-accounting' ),
							'std'  => '',
							'type' => 'textarea',
						),
						'bill_terms'          => array(
							'name' => __( 'Bill Terms & Conditions', 'wp-ever-accounting' ),
							'std'  => '',
							'type' => 'textarea',
						),
						'bill_item_label'     => array(
							'name' => __( 'Item Label', 'wp-ever-accounting' ),
							'std'  => __( 'Item', 'wp-ever-accounting' ),
							'type' => 'text',
						),
						'bill_price_label'    => array(
							'name' => __( 'Price Label', 'wp-ever-accounting' ),
							'std'  => __( 'Price', 'wp-ever-accounting' ),
							'type' => 'text',
						),
						'bill_quantity_label' => array(
							'name' => __( 'Quantity Label', 'wp-ever-accounting' ),
							'std'  => __( 'Quantity', 'wp-ever-accounting' ),
							'type' => 'text',

						),
					),
				)
			),
			'emails'  => apply_filters(
				'eaccounting_settings_emails',
				array(
					'main' => array(),
				)
			),
		);

		if ( eaccounting_tax_enabled() ) {
			$settings['general']['taxes'] = array(
				'tax_subtotal_rounding' => array(
					'name' => __( 'Rounding', 'wp-ever-accounting' ),
					'type' => 'checkbox',
					'desc' => __( 'Round tax at subtotal level, instead of rounding per tax rate.', 'wp-ever-accounting' ),
				),
				'prices_include_tax'    => array(
					'name'    => __( 'Prices entered with tax', 'wp-ever-accounting' ),
					'type'    => 'select',
					'std'     => 'yes',
					'options' => array(
						'yes' => __( 'Yes, I will enter prices inclusive of tax', 'wp-ever-accounting' ),
						'no'  => __( 'No, I will enter prices exclusive of tax', 'wp-ever-accounting' ),
					),
				),
				'tax_display_totals'    => array(
					'name'    => __( 'Display tax totals	', 'wp-ever-accounting' ),
					'type'    => 'select',
					'std'     => 'total',
					'options' => array(
						'total'      => __( 'As a single total', 'wp-ever-accounting' ),
						'individual' => __( 'As individual tax rates', 'wp-ever-accounting' ),
					),
				),
			);
		}

		/**
		 * Filters the entire default settings array.
		 * add_filter( 'eaccounting_settings', function( $settings ){
		 *
		 * } )
		 *
		 * @since 1.0.2
		 *
		 * @param array $settings Array of default settings.
		 *
		 */

		return apply_filters( 'eaccounting_settings', $settings );
	}

	/**
	 * Add all settings sections and fields
	 *
	 * @since 1.0.2
	 * @return void
	 */
	function register_settings() {

		foreach ( $this->get_registered_settings() as $tab => $sections ) {
			if ( ! is_array( $sections ) ) {
				continue;
			}

			foreach ( $sections as $section => $settings ) {

				add_settings_section(
					$section,
					__return_null(),
					'__return_false',
					'eaccounting_settings_' . $tab
				);

				foreach ( $settings as $id => $option ) {

					$args = wp_parse_args(
						$option,
						array(
							'section'     => $section,
							'desc'        => '',
							'id'          => $id,
							'tip'         => '',
							'name'        => '',
							'size'        => 'regular',
							'options'     => array(),
							'std'         => '',
							'min'         => null,
							'max'         => null,
							'step'        => null,
							'multiple'    => null,
							'placeholder' => null,
							'required'    => '',
							'disabled'    => '',
							'input_class' => '',
							'class'       => '',
							'callback'    => '',
							'style'       => '',
							'html'        => '',
							'attr'        => array(),
						)
					);

					$callback = ! empty( $args['callback'] ) ? $args['callback'] : array( $this, $args['type'] . '_callback' );
					$tip      = isset( $args['tip'] ) ? eaccounting_help_tip( $args['tip'] ) : '';

					if ( ! in_array( $args['type'], array( 'checkbox', 'multicheck', 'radio', 'header' ), true ) ) {
						$args['name'] = sprintf( '<label for="eaccounting_settings[%1$s]">%2$s</label>%3$s', $id, $args['name'], $tip );
					} elseif ( 'header' === $args['type'] ) {
						$args['name'] = sprintf( '<h3>%s</h3>', esc_html( $args['name'] ) );
					}

					add_settings_field(
						'eaccounting_settings[' . $id . ']',
						$args['name'],
						is_callable( $callback ) ? $callback : array( $this, 'missing_callback' ),
						'eaccounting_settings_' . $tab,
						$section,
						$args
					);

				}
			}
		}
		register_setting( 'eaccounting_settings', 'eaccounting_settings', array( $this, 'sanitize_settings' ) );
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
		if ( ! empty( $args['desc'] ) ) {
			echo $args['desc'];
		}
	}

	/**
	 * Text Callback
	 *
	 * Renders text fields.
	 *
	 * @since 1.0.2
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function text_callback( $args ) {
		$default = isset( $args['std'] ) ? $args['std'] : '';
		$value   = $this->get( $args['id'], $default );
		$attrs   = array( 'required', 'placeholder', 'disabled', 'style' );
		foreach ( $attrs as $attr ) {
			if ( ! empty( $args[ $attr ] ) ) {
				$args['attr'][ $attr ] = $args[ $attr ];
			}
		}

		echo sprintf(
			'<input type="text" class="%1$s-text %2$s" style="%3$s" name="eaccounting_settings[%4$s]" id="eaccounting_settings[%4$s]" value="%5$s" %6$s/>',
			esc_attr( $args['size'] ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			eaccounting_implode_html_attributes( $args['attr'] )
		);

		echo ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

	}

	/**
	 * Text Callback
	 *
	 * Renders text fields.
	 *
	 * @since 1.0.2
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function email_callback( $args ) {
		$default = isset( $args['std'] ) ? $args['std'] : '';
		$value   = $this->get( $args['id'], $default );
		$attrs   = array( 'required', 'placeholder', 'disabled', 'style' );
		foreach ( $attrs as $attr ) {
			if ( ! empty( $args[ $attr ] ) ) {
				$args['attr'][ $attr ] = $args[ $attr ];
			}
		}

		echo sprintf(
			'<input type="email" class="%1$s-text %2$s" style="%3$s" name="eaccounting_settings[%4$s]" id="eaccounting_settings[%4$s]" value="%5$s" %6$s/>',
			esc_attr( $args['size'] ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			eaccounting_implode_html_attributes( $args['attr'] )
		);

		echo ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

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
		$value      = $this->get( $args['id'] );
		$checked    = isset( $value ) ? checked( 'yes', $value, false ) : '';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$id         = 'eaccounting_settings[' . $args['id'] . ']';
		$html       = '<label for="' . $id . '">';
		$html       .= '<input type="checkbox" id="' . $id . '" name="' . $id . '" value="yes" ' . $checked . ' ' . $attributes . '/>&nbsp;';
		$html       .= $args['desc'];
		$html       .= '</label>';

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
				if ( isset( $this->settings[ $args['id'] ][ $key ] ) ) {
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

			if ( isset( $this->settings[ $args['id'] ] ) && $this->settings[ $args['id'] ] == $key ) { //phpcs:ignore
				$checked = true;
			} elseif ( isset( $args['std'] ) && $args['std'] == $key && ! isset( $this->options[ $args['id'] ] ) ) { //phpcs:ignore
				$checked = true;
			}

			echo '<label for="eaccounting_settings[' . $args['id'] . '][' . $key . ']">';
			echo '<input name="eaccounting_settings[' . $args['id'] . ']" id="eaccounting_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( true, $checked, false ) . '/>';
			echo $option . '</label><br/>';
		endforeach;

		echo '</fieldset><p class="description">' . $args['desc'] . '</p>';
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

		if ( isset( $this->settings[ $args['id'] ] ) && ! empty( $this->settings[ $args['id'] ] ) ) {
			$value = $this->settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html = sprintf(
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
		$value = isset( $this->settings[ $args['id'] ] ) ? $this->settings[ $args['id'] ] : null;
		$value = ( ! is_null( $value ) && '' !== $value && floatval( $value ) >= 0 ) ? floatval( $value ) : null;

		// Saving the field empty will revert to std value, if it exists
		$std   = ( isset( $args['std'] ) && ! is_null( $args['std'] ) && '' !== $args['std'] && floatval( $args['std'] ) >= 0 ) ? $args['std'] : null;
		$value = ! is_null( $value ) ? $value : ( ! is_null( $std ) ? $std : null );
		$value = eaccounting_round_number( $value );

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html = sprintf(
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

		if ( isset( $this->settings[ $args['id'] ] ) ) {
			$value = $this->settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html = sprintf(
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

		if ( isset( $this->settings[ $args['id'] ] ) ) {
			$value = $this->settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html = sprintf(
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

		if ( isset( $this->settings[ $args['id'] ] ) ) {
			$value = $this->settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$html = sprintf(
			'<select class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" %s>',
			$args['size'],
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			eaccounting_implode_html_attributes( $args['attr'] )
		);

		foreach ( $args['options'] as $key => $option_value ) {
			$html .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), eaccounting_selected( esc_attr( $key ), esc_attr( $value ) ), esc_html( $option_value ) );
		}

		$html .= '</select>';
		echo $html;

		echo ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';
	}

	/**
	 * Rich Editor Callback
	 *
	 * Renders rich editor fields.
	 *
	 * @since 1.0.2
	 * @global string $wp_version WordPress Version
	 *
	 * @global        $this       ->options Array of all the EverAccounting Options
	 *
	 * @param array   $args       Arguments passed by the setting
	 *
	 */
	function rich_editor_callback( $args ) {

		if ( ! empty( $this->settings[ $args['id'] ] ) ) {
			$value = $this->settings[ $args['id'] ];
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
		if ( isset( $this->settings[ $args['id'] ] ) ) {
			$value = $this->settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html = sprintf(
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


	function html_callback( $args ) {
		$args = wp_parse_args( $args, array( 'html' => '' ) );
		echo sprintf( '<div class="ea-settings-html %s">%s</div>', sanitize_html_class( $args['input_class'] ), wp_kses_post( $args['html'] ) );
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
		/* translators: %s name of the callback */
		printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'wp-ever-accounting' ), $args['id'] );
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
	 * @param string $key
	 *
	 * @param mixed  $default (optional)
	 *
	 * @return mixed
	 */
	public function get( $key, $default = false ) {

		// Only allow non-empty values, otherwise fallback to the default
		$value = ! empty( $this->settings[ $key ] ) ? $this->settings[ $key ] : $default;

		$zero_values_allowed = array();

		/**
		 * Filters settings allowed to accept 0 as a valid value without
		 * falling back to the default.
		 *
		 * @param array $zero_values_allowed Array of setting IDs.
		 */
		$zero_values_allowed = (array) apply_filters( 'eaccounting_settings_zero_values_allowed', $zero_values_allowed );

		// Allow 0 values for specified keys only
		if ( in_array( $key, $zero_values_allowed ) ) { // phpcs:ignore

			$value = isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : null;
			$value = ( ! is_null( $value ) && '' !== $value ) ? $value : $default;

		}

		return $value;
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
		$section  = isset( $referrer['section'] ) ? $referrer['section'] : 'main';
		$settings = isset( $settings[ $tab ] ) ? $settings[ $tab ][ $section ] : array();

		$input = $input ? $input : array();

		// Ensure a value is always passed for every checkbox
		if ( ! empty( $settings ) ) {

			foreach ( $settings as $key => $setting ) {

				// Single checkbox
				if ( 'checkbox' === $setting['type'] ) {
					$input[ $key ] = ! empty( $input[ $key ] );
				}

				// Multicheck list
				if ( 'multicheck' === $settings[ $key ]['type'] ) {
					if ( empty( $input[ $key ] ) ) {
						$input[ $key ] = array();
					}
				}
			}
		}

		// Loop through each setting being saved and pass it through a sanitization filter
		foreach ( $input as $key => $value ) {

			// Get the setting type (checkbox, select, etc)
			$type              = isset( $settings[ $key ]['type'] ) ? $settings[ $key ]['type'] : false;
			$sanitize_callback = isset( $settings[ $key ]['sanitize_callback'] ) ? $settings[ $key ]['sanitize_callback'] : false;
			$input[ $key ]     = $value;

			if ( $type ) {
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
				 * @param array  $value The input array and settings key defined within.
				 *
				 * @param string $key   The settings key.
				 *
				 */
				$input[ $key ] = apply_filters( 'eaccounting_settings_sanitize_' . $type, $input[ $key ], $key );

				if ( $sanitize_callback && is_callable( $sanitize_callback ) ) {
					$input[ $key ] = call_user_func( $sanitize_callback, $value );
				}
			}

			/**
			 * General setting sanitization filter
			 *
			 * @since 1.0
			 *
			 * @param array  $input [ $key ] The input array and settings key defined within.
			 *
			 * @param string $key   The settings key.
			 *
			 */
			$input[ $key ] = apply_filters( 'eaccounting_settings_sanitize', $input[ $key ], $key );
		}

		add_settings_error( 'eaccounting-notices', '', __( 'Settings updated.', 'wp-ever-accounting' ), 'updated' );

		return array_merge( $saved, $input );
	}

	/**
	 * Sets an option (in memory).
	 *
	 * @since  1.0.2
	 * @access public
	 *
	 * @param array $settings An array of `key => value` setting pairs to set.
	 *
	 * @param bool  $save     Optional. Whether to trigger saving the option or options. Default false.
	 *
	 * @return bool If `$save` is not false, whether the options were saved successfully. True otherwise.
	 */
	public function set( $settings, $save = false ) {
		foreach ( $settings as $option => $value ) {
			$this->settings[ $option ] = $value;
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
		$this->settings = get_option( 'eaccounting_settings', array() );

		return $updated;
	}

	/**
	 * Get all settings
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function get_all() {
		return $this->settings;
	}
}
