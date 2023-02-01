<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Settings.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Settings {
	/**
	 * Settings tabs.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $tabs = array();

	/**
	 * Get settings tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_tabs() {
		if ( empty( self::$tabs ) ) {
			$tabs   = array();
			$tabs[] = new \EverAccounting\Admin\Settings\GeneralTab();
			$tabs[] = new \EverAccounting\Admin\Settings\SalesTab();
			$tabs[] = new \EverAccounting\Admin\Settings\ExpensesTab();

			/**
			 * Filter settings tabs.
			 *
			 * @param array $tabs
			 *
			 * @since 1.0.0
			 */
			self::$tabs = apply_filters( 'ever_accounting_settings_tabs', $tabs );
		}

		return self::$tabs;
	}

	/**
	 * Save settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function save() {
		global $current_tab;
		//check_admin_referer( 'ever-accounting-settings' );

		// Trigger actions.
		do_action( 'ever_accounting_settings_save_' . $current_tab );
		do_action( 'ever_accounting_update_options_' . $current_tab );
		do_action( 'ever_accounting_update_options' );

		do_action( 'ever_accounting_settings_saved' );
	}

	/**
	 * Output settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		global $current_section, $current_tab;

		do_action( 'ever_accounting_settings_start' );

		// Get tabs.
		$tabs = apply_filters( 'ever_accounting_settings_tabs_array', array() );

		include_once __DIR__ . '/Views/html-settings.php';
	}

	/**
	 * Get a setting from the settings API.
	 *
	 * @param string $option_name Option name.
	 * @param mixed $default Default value.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public static function get_option( $option_name, $default = '' ) {
		// Array value.
		if ( strstr( $option_name, '[' ) ) {
			parse_str( $option_name, $option_array );

			// Option name is first key.
			$option_name = current( array_keys( $option_array ) );

			// Get value.
			$option_values = get_option( $option_name, '' );
			$key           = key( $option_array[ $option_name ] );

			$option_value = isset( $option_values[ $key ] ) ? $option_values[ $key ] : $default;
		} else {
			// Single value.
			$option_value = get_option( $option_name, $default );
		}

		if ( is_array( $option_value ) ) {
			$option_value = wp_unslash( $option_value );
		} elseif ( ! is_null( $option_value ) ) {
			$option_value = stripslashes( $option_value );
		}

		return ( null === $option_value ) ? $default : $option_value;
	}

	/**
	 * Output admin fields.
	 *
	 * Loops though the ever_accounting options array and outputs each field.
	 *
	 * @param array $options Options array.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_fields( $options ) {
		foreach ( $options as $value ) {
			$defaults = array(
				'title'       => '',
				'desc'        => '',
				'id'          => '',
				'class'       => '',
				'css'         => '',
				'default'     => '',
				'type'        => 'text',
				'desc_tip'    => false,
				'attributes'  => array(),
				'placeholder' => '',
				'autoload'    => false,
				'suffix'      => false,
			);
			$value    = wp_parse_args( $value, $defaults );
			if ( empty( $value['type'] ) ) {
				continue;
			}
			// The 'field_name' key can be used when it is useful to specify an input field name that is different
			// from the input field ID. We use the key 'field_name' because 'name' is already in use for a different
			// purpose.
			if ( empty( $value['field_name'] ) ) {
				$value['field_name'] = $value['id'];
			}
			if ( ! isset( $value['value'] ) ) {
				$value['value'] = self::get_option( $value['id'], $value['default'] );
			}

			// Custom attribute handling.
			$attributes = array();
			if ( ! empty( $value['attributes'] ) && is_array( $value['attributes'] ) ) {
				foreach ( $value['attributes'] as $attribute => $attribute_value ) {
					$attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
				}
			}

			// Description handling.
			$field_description = self::get_field_description( $value );
			$description       = $field_description['description'];
			$tooltip_html      = $field_description['tooltip_html'];

			// Switch based on type.
			switch ( $value['type'] ) {
				// Section Titles.
				case 'title':
					if ( ! empty( $value['title'] ) ) {
						echo '<h2>' . esc_html( $value['title'] ) . '</h2>';
					}
					if ( ! empty( $value['desc'] ) ) {
						echo wpautop( wptexturize( wp_kses_post( $value['desc'] ) ) );
					}
					echo '<table class="form-table">' . "\n\n";
					if ( ! empty( $value['id'] ) ) {
						do_action( 'ever_accounting_settings_' . sanitize_title( $value['id'] ) );
					}
					break;
				case 'info':
					echo '<tr><th scope="row" class="titledesc"/><td style="' . esc_attr( $value['css'] ) . '">';
					echo wp_kses_post( wpautop( wptexturize( $value['text'] ) ) );
					echo '</td></tr>';
					break;

				// Section Ends.
				case 'sectionend':
					if ( ! empty( $value['id'] ) ) {
						do_action( 'ever_accounting_settings_' . sanitize_title( $value['id'] ) . '_end' );
					}
					echo '</table>';
					if ( ! empty( $value['id'] ) ) {
						do_action( 'ever_accounting_settings_' . sanitize_title( $value['id'] ) . '_after' );
					}
					break;
				// Standard text inputs and subtypes like 'number'.
				case 'text':
				case 'password':
				case 'datetime':
				case 'datetime-local':
				case 'date':
				case 'month':
				case 'time':
				case 'week':
				case 'number':
				case 'email':
				case 'url':
				case 'tel':
				default:
					$option_value = $value['value'];

					?><tr valign="top">
					<th scope="row" class="titledesc">
						<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip_html ); // WPCS: XSS ok. ?></label>
					</th>
					<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
						<input
							name="<?php echo esc_attr( $value['field_name'] ); ?>"
							id="<?php echo esc_attr( $value['id'] ); ?>"
							type="<?php echo esc_attr( $value['type'] ); ?>"
							style="<?php echo esc_attr( $value['css'] ); ?>"
							value="<?php echo esc_attr( $option_value ); ?>"
							class="<?php echo esc_attr( $value['class'] ); ?>"
							placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
							<?php echo wp_kses_post( implode( ' ', $attributes ) ); ?>
						/>
						<?php echo wp_kses_post( $value['suffix'] ); ?>
						<?php echo wp_kses_post( $description ); ?>
					</td>
					</tr>
					<?php
					break;
			}
		}
	}

	/**
	 * Save admin fields.
	 *
	 * Loops through the woocommerce options array and outputs each field.
	 *
	 * @param array $options Options array to output.
	 * @param array $data Optional. Data to use for saving. Defaults to $_POST.
	 *
	 * @return bool
	 */
	public static function save_fields( $options, $data = null ) {
		if ( is_null( $data ) ) {
			$data = $_POST; // WPCS: CSRF ok, input var ok.
		}

		if ( empty( $data ) ) {
			return false;
		}

		// Options to update will be stored here and saved later.
		$update_options   = array();
		$autoload_options = array();

		// Loop options and get values to save.
		foreach ( $options as $option ) {
			if ( ! isset( $option['id'], $option['type'] ) ) {
				continue;
			}
			$option_name = isset( $option['field_name'] ) ? $option['field_name'] : $option['id'];

			// Get posted value.
			if ( strstr( $option_name, '[' ) ) {
				parse_str( $option_name, $option_name_array );
				$option_name  = current( array_keys( $option_name_array ) );
				$setting_name = key( $option_name_array[ $option_name ] );
				$raw_value    = isset( $data[ $option_name ][ $setting_name ] ) ? wp_unslash( $data[ $option_name ][ $setting_name ] ) : null;
			} else {
				$setting_name = '';
				$raw_value    = isset( $data[ $option_name ] ) ? wp_unslash( $data[ $option_name ] ) : null;
			}

			// Format the value based on option type.
			switch ( $option['type'] ) {
				case 'checkbox':
					$value = '1' === $raw_value || 'yes' === $raw_value ? 'yes' : 'no';
					break;
				case 'textarea':
					$value = wp_kses_post( trim( $raw_value ) );
					break;
				case 'multiselect':
					$value = array_filter( array_map( 'ea_clean', (array) $raw_value ) );
					break;
				case 'select':
				case 'multicheck':
					$allowed_values = empty( $option['options'] ) ? array() : array_map( 'strval', array_keys( $option['options'] ) );
					if ( empty( $option['default'] ) && empty( $allowed_values ) ) {
						$value = null;
						break;
					}
					$default = ( empty( $option['default'] ) ? $allowed_values[0] : $option['default'] );
					$value   = in_array( $raw_value, $allowed_values, true ) ? $raw_value : $default;
					break;
				case 'relative_date_selector':
					$value = ea_parse_relative_date_option( $raw_value );
					break;
				default:
					$value = ea_clean( $raw_value );
					break;
			}

			$sanitize_callback = isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
			if ( $sanitize_callback && is_callable( $sanitize_callback ) ) {
				$value = call_user_func( $sanitize_callback, $value );
			}

			/**
			 * Sanitize the value of an option.
			 *
			 * @since 1.1.3
			 */
			$value = apply_filters( 'ever_accounting_admin_settings_sanitize_option', $value, $option, $raw_value );

			/**
			 * Sanitize the value of an option by option name.
			 *
			 * @since 1.1.3
			 */
			$value = apply_filters( "ever_accounting_admin_settings_sanitize_option_$option_name", $value, $option, $raw_value );

			if ( is_null( $value ) ) {
				continue;
			}
			if ( $option_name && $setting_name ) {
				if ( ! isset( $update_options[ $option_name ] ) ) {
					$update_options[ $option_name ] = get_option( $option_name, array() );
				}
				if ( ! is_array( $update_options[ $option_name ] ) ) {
					$update_options[ $option_name ] = array();
				}
				$update_options[ $option_name ][ $setting_name ] = $value;
			} else {
				$update_options[ $option_name ] = $value;
			}

			$autoload_options[ $option_name ] = isset( $option['autoload'] ) ? (bool) $option['autoload'] : true;
		}

		// Save all options in our array.
		foreach ( $update_options as $name => $value ) {
			update_option( $name, $value, $autoload_options[ $name ] ? 'yes' : 'no' );
		}

		return true;
	}

	/**
	 * Helper function to get the formatted description and tip HTML for a
	 * given form field. Plugins can call this when implementing their own custom
	 * settings types.
	 *
	 * @param array $value The form field value array.
	 *
	 * @since  1.0.0
	 * @return array The description and tip as a 2 element array.
	 */
	public static function get_field_description( $value ) {
		$description  = '';
		$tooltip_html = '';

		if ( true === $value['desc_tip'] ) {
			$tooltip_html = $value['desc'];
		} elseif ( ! empty( $value['desc_tip'] ) ) {
			$description  = $value['desc'];
			$tooltip_html = $value['desc_tip'];
		} elseif ( ! empty( $value['desc'] ) ) {
			$description = $value['desc'];
		}

		if ( $description && in_array( $value['type'], array( 'textarea', 'radio' ), true ) ) {
			$description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
		} elseif ( $description && in_array( $value['type'], array( 'checkbox' ), true ) ) {
			$description = wp_kses_post( $description );
		} elseif ( $description ) {
			$description = '<p class="description">' . wp_kses_post( $description ) . '</p>';
		}

		if ( $tooltip_html && in_array( $value['type'], array( 'checkbox' ), true ) ) {
			$tooltip_html = '<p class="description">' . $tooltip_html . '</p>';
		} elseif ( $tooltip_html ) {
			$tooltip_html = ea_tooltip( $tooltip_html );
		}

		return array(
			'description'  => $description,
			'tooltip_html' => $tooltip_html,
		);
	}
}
