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
class EverAccounting_Settings {
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
		/**
		 * Fires before attempting to retrieve registered settings.
		 *
		 * @since 1.0.2
		 *
		 * @param self $this Settings instance.
		 *
		 */
		do_action( 'eaccounting_pre_get_registered_settings', $this );

		$settings = array();

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
		$settings = apply_filters( 'eaccounting_settings', $settings );

		$registered = array();
		foreach ( $settings as $tab => $options ) {
			foreach ( $options as $key => $option ) {
				$registered[ $key ] = wp_parse_args(
					$option,
					array(
						'section' => 'main',
						'tab'     => $tab,
					)
				);
			}
		}

		return $registered;
	}

	/**
	 * Get settings tabs.
	 *
	 * @since 1.1.0
	 * @return array list of tabs.
	 */
	public static function get_tabs() {
		return apply_filters(
			'eaccounting_settings_tabs',
			array(
				'general'    => __( 'General', 'wp-ever-accounting' ),
				'currencies' => __( 'Currencies', 'wp-ever-accounting' ),
				'categories' => __( 'Categories', 'wp-ever-accounting' ),
				'taxes'      => __( 'Taxes', 'wp-ever-accounting' ),
				'advanced'   => __( 'Advanced', 'wp-ever-accounting' ),
				'emails'     => __( 'Emails', 'wp-ever-accounting' ),
				'misc'       => __( 'Misc', 'wp-ever-accounting' ),
			)
		);
	}

	/**
	 * Get settings tabs.
	 *
	 * @since 1.1.0
	 * @return array list of sections.
	 */
	public static function get_sections() {
		$sections = array();
		$defaults = array(
			'general'  => array(
				'main'    => __( 'General Settings', 'wp-ever-accounting' ),
				'tax'     => __( 'Tax Settings', 'wp-ever-accounting' ),
				'invoice' => __( 'Invoice Settings', 'wp-ever-accounting' ),
				'bill'    => __( 'Bill Settings', 'wp-ever-accounting' ),
			),
			'advanced' => array(
				'keys' => __( 'REST API', 'wp-ever-accounting' ),
			),
		);

		foreach ( self::get_tabs() as $tab_id => $tab_label ) {
			$sections[ $tab_id ] = apply_filters( 'eaccounting_settings_tab_sections_' . $tab_id, isset( $defaults[ $tab_id ] ) ? $defaults[ $tab_id ] : array( 'main' => '' ) );
		}

		return $sections;
	}

	/**
	 * Add all settings sections and fields
	 *
	 * @since 1.0.2
	 * @return void
	 */
	function register_settings() {
		$options  = $this->get_registered_settings();
		$settings = array();

		foreach ( $options as $key => $option ) {
			if ( ! isset( $settings[ $option['tab'] ] ) ) {
				$settings[ $option['tab'] ] = array();
			}

			if ( ! isset( $settings[ $option['tab'] ] [ $option['section'] ] ) ) {
				add_settings_section(
					$option['section'],
					__return_null(),
					'__return_false',
					'eaccounting_settings_' . $option['tab']
				);
			}

			$title    = isset( $option['name'] ) ? $option['name'] : '';
			$callback = ! empty( $option['callback'] ) ? $option['callback'] : array( $this, $option['type'] . '_callback' );
			$tip      = isset( $option['tip'] ) ? eaccounting_help_tip( $option['tip'] ) : '';

			if ( ! in_array( $option['type'], array( 'checkbox', 'multicheck', 'radio', 'header' ), true ) ) {
				$title = sprintf( '<label for="eaccounting_settings[%1$s]">%2$s</label>%3$s', $key, $title, $tip );
			} elseif ( 'header' === $option['type'] ) {
				$title = sprintf( '<h3>%s</h3>', esc_html( $title ) );
			}

			add_settings_field(
				'eaccounting_settings[' . $key . ']',
				$title,
				is_callable( $callback ) ? $callback : array( $this, 'missing_callback' ),
				'eaccounting_settings_' . $option['tab'],
				$option['section'],
				array(
					'id'          => $key,
					'section'     => $option['section'],
					'desc'        => ! empty( $option['desc'] ) ? $option['desc'] : '',
					'name'        => isset( $option['name'] ) ? $option['name'] : null,
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
					'style'       => isset( $option['style'] ) ? $option['style'] : '',
					'html'        => isset( $option['html'] ) ? $option['html'] : '',
				)
			);
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
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function text_callback( $args ) {
		$default = isset( $args['std'] ) ? $args['std'] : '';
		$value   = $this->get( $args['id'], $default );

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = sprintf(
			'<input type="text" class="%1$s-text %2$s" style="%3$s" name="eaccounting_settings[%4$s]" id="eaccounting_settings[%4$s]" value="%5$s" %6$s/>',
			esc_attr( $size ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			$attributes
		);
		$html .= $desc;

		echo $html;
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
		$html      .= '<input type="checkbox" id="' . $id . '" name="' . $id . '" value="yes" ' . $checked . ' ' . $attributes . '/>&nbsp;';
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

		$html  = sprintf(
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

		$html  = sprintf(
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

		$html  = sprintf(
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

		$html  = sprintf(
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

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';
		$html       = sprintf(
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

		$html  = sprintf(
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
		$settings = isset( $settings[ $tab ] ) ? wp_list_filter( $settings[ $tab ], array( 'section' => $section ) ) : array();

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

	/**
	 * Get sections for a specific tab.
	 *
	 * @since 1.1.0
	 *
	 * @param $tab
	 *
	 * @return array|mixed
	 */
	public static function get_tab_sections( $tab ) {
		$sections = self::get_sections();

		return array_key_exists( $tab, $sections ) ? $sections[ $tab ] : array();
	}
}
