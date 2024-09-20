<?php

namespace EverAccounting\Admin\Settings;

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
	 * @since 1.1.6
	 * @var array
	 */
	protected static $tabs = null;

	/**
	 * Settings messages.
	 *
	 * @since 1.1.6
	 * @var array
	 */
	protected static $notices = array();

	/**
	 * Add a message.
	 *
	 * @param string $text Message.
	 */
	public static function add_message( $text ) {
		self::$notices[] = array(
			'message' => $text,
			'type'    => 'success',
		);
	}

	/**
	 * Add an error.
	 *
	 * @param string $text Message.
	 */
	public static function add_error( $text ) {
		self::$notices[] = array(
			'message' => $text,
			'type'    => 'error',
		);
	}

	/**
	 * Get settings pages.
	 *
	 * @return array
	 * @since 1.1.6
	 */
	public static function get_tabs() {
		if ( is_null( self::$tabs ) ) {
			$tabs   = array();
			$tabs[] = new General();
			$tabs[] = new Taxes();
			$tabs[] = new Sales();
			$tabs[] = new Purchases();
			$tabs[] = new Currencies();

			self::$tabs = apply_filters( 'eac_get_settings_tabs', $tabs );
		}

		return self::$tabs;
	}

	/**
	 * Save settings.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function save() {
		global $current_tab;
		check_admin_referer( 'ever-accounting-settings' );

		/**
		 * Fires before saving settings.
		 *
		 * @since 1.1.6
		 */
		do_action( 'eac_settings_save_' . $current_tab );

		// Save settings.
		do_action( 'ever_accounting_update_options_' . $current_tab );
		do_action( 'ever_accounting_update_options' );

		self::add_message( __( 'Your settings have been saved.', 'wp-ever-accounting' ) );

		/**
		 * Fires after saving settings.
		 *
		 * @since 1.1.6
		 */
		do_action( 'eac_settings_saved' );
	}

	/**
	 * Output settings.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function output() {
		global $current_section, $current_tab;

		/**
		 * Fires before outputting settings.
		 *
		 * @since 1.1.6
		 */
		do_action( 'eac_settings_start' );

		// Get tabs for the settings page.
		$tabs       = apply_filters( 'eac_settings_tabs_array', array() );
		$page       = isset( $_GET['page'] ) ? sanitize_title( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$tab_exists = isset( $tabs[ $current_tab ] ) || has_action( 'ever_accounting_sections_' . $current_tab ) || has_action( 'eac_settings_' . $current_tab ) || has_action( 'eac_settings_tabs_' . $current_tab );
		$notices    = self::$notices;
		include __DIR__ . '/views/settings.php';
	}

	/**
	 * Output admin fields.
	 *
	 * Loops though the ever_accounting options array and outputs each field.
	 *
	 * @param array $options Options array.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function output_fields( $options ) {
		foreach ( $options as $value ) {
			$defaults = array(
				'type'         => 'text',
				'title'        => '',
				'id'           => '',
				'class'        => '',
				'desc'         => '',
				'default'      => '',
				'desc_tip'     => false,
				'css'          => '',
				'placeholder'  => '',
				'maxlength'    => false,
				'required'     => false,
				'autocomplete' => false,
				'options'      => array(),
				'attrs'        => array(),
				'autoload'     => false,
				'suffix'       => false,
			);

			$value = wp_parse_args( $value, $defaults );

			// Bail out if the field type is not set.
			if ( empty( $value['type'] ) ) {
				continue;
			}

			// If field name is not set, use the field id.
			if ( empty( $value['name'] ) ) {
				$value['name'] = $value['id'];
			}

			// If value is not set, use the default value.
			if ( ! isset( $value['value'] ) ) {
				$value['value'] = self::get_option( $value['name'], $value['default'] );
			}

			/**
			 * Filter the arguments of a form field before it is rendered.
			 *
			 * @param array $args Arguments used to render the form field.
			 *
			 * @since 1.1.6
			 */
			$value = apply_filters( 'ever_accounting_setting_field', $value );

			/**
			 * Filter the arguments of a specific form field before it is rendered.
			 *
			 * The dynamic portion of the hook name, `$value['type']`, refers to the form field type.
			 *
			 * @param array $args Arguments used to render the form field.
			 *
			 * @since 1.1.6
			 */
			$value = apply_filters( "ever_accounting_setting_field_{$value['type']}", $value );

			// Custom attribute handling.
			$attrs = array();

			foreach ( $value as $attr_key => $attr_value ) {
				if ( empty( $attr_key ) || empty( $attr_value ) ) {
					continue;
				}
				if ( str_starts_with( $attr_key, 'attr-' ) ) {
					$attrs[] = esc_attr( substr( $attr_key, 5 ) ) . '="' . esc_attr( $attr_value ) . '"';
				} elseif ( str_starts_with( $attr_key, 'data-' ) ) {
					$attrs[] = esc_attr( $attr_key ) . '="' . esc_attr( $attr_value ) . '"';
				} elseif ( in_array( $attr_key, array( 'readonly', 'disabled', 'required', 'autofocus' ), true ) ) {
					$attrs[] = esc_attr( $attr_key ) . '="' . esc_attr( $attr_key ) . '"';
				} elseif ( in_array( $attr_key, array( 'maxlength', 'placeholder', 'autocomplete', 'css' ), true ) ) {
					$attrs[] = esc_attr( $attr_key ) . '="' . esc_attr( $attr_value ) . '"';
				}
			}
			// Handle conditional logic.
			if ( ! empty( $value['conditional'] ) ) {
				$conditional = wp_parse_args(
					$value['conditional'],
					array(
						'field'   => '',
						'compare' => '==',
						'value'   => '',
					)
				);

				$value['attrs']['data-conditional'] = wp_json_encode( $conditional );
			}

			foreach ( $value['attrs'] as $attr => $attr_value ) {
				$attrs[] = esc_attr( $attr ) . '="' . esc_attr( $attr_value ) . '"';
			}

			// Description handling.
			$field_description = self::get_field_description( $value );
			$description       = $field_description['description'];
			$tooltip           = $field_description['tooltip'];

			// Suffix handling.
			$suffix = is_callable( $value['suffix'] ) ? call_user_func( $value['suffix'], $value ) : $value['suffix'];

			// Switch based on type.
			switch ( $value['type'] ) {
				// Section Titles.
				case 'title':
					if ( ! empty( $value['title'] ) ) {
						echo '<h2>' . esc_html( $value['title'] ) . '</h2>';
					}
					if ( ! empty( $value['desc'] ) ) {
						echo wp_kses_post( wpautop( wptexturize( $value['desc'] ) ) );
					}
					echo '<table class="form-table eac-settings-table">';
					if ( ! empty( $value['id'] ) ) {
						do_action( 'eac_settings_' . sanitize_title( $value['id'] ) );
					}
					break;

				// Section Ends.
				case 'sectionend':
					if ( ! empty( $value['id'] ) ) {
						do_action( 'eac_settings_' . sanitize_title( $value['id'] ) . '_end' );
					}
					echo '</table>';
					if ( ! empty( $value['id'] ) ) {
						do_action( 'eac_settings_' . sanitize_title( $value['id'] ) . '_after' );
					}

					break;

				// Standard text inputs and subtypes like 'number'.
				case 'text':
				case 'password':
				case 'datetime':
				case 'date':
				case 'month':
				case 'time':
				case 'week':
				case 'number':
				case 'email':
				case 'url':
				case 'tel':
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $value['name'] ); ?>"><?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
							<input
								name="<?php echo esc_attr( $value['name'] ); ?>"
								id="<?php echo esc_attr( $value['name'] ); ?>"
								type="<?php echo esc_attr( $value['type'] ); ?>"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								value="<?php echo esc_attr( $value['value'] ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>"
								placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
								<?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>
							/>
							<?php echo wp_kses_post( $suffix ); ?>
							<?php echo wp_kses_post( $description ); ?>
						</td>
					</tr>
					<?php
					break;

				// Textarea.
				case 'textarea':
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $value['name'] ); ?>"><?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
						<textarea
							name="<?php echo esc_attr( $value['name'] ); ?>"
							id="<?php echo esc_attr( $value['name'] ); ?>"
							style="<?php echo esc_attr( $value['css'] ); ?>"
							class="<?php echo esc_attr( $value['class'] ); ?>"
							placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
							<?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>
						><?php echo esc_textarea( $value['value'] ); ?></textarea>
							<?php echo wp_kses_post( $suffix ); ?>
							<?php echo wp_kses_post( $description ); ?>
						</td>
					</tr>
					<?php
					break;

				case 'select':
					$value['value']       = wp_parse_list( $value['value'] );
					$value['value']       = array_map( 'strval', $value['value'] );
					$value['placeholder'] = ! empty( $value['placeholder'] ) ? $value['placeholder'] : __( 'Select an option&hellip;', 'wp-ever-accounting' );
					if ( ! empty( $value['multiple'] ) ) {
						$value['name'] .= '[]';
						$attrs[]        = 'multiple="multiple"';
					}
					if ( ! empty( $value['option_key'] ) && ! empty( $value['option_value'] ) ) {
						// verify options is an array otherwise we will make it an array.
						if ( ! is_array( $value['options'] ) ) {
							$value['options'] = array();
						}
						$value['options'] = array_filter( $value['options'] );
						$value['options'] = wp_list_pluck( $value['options'], $value['option_value'], $value['option_key'] );
					}
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $value['name'] ); ?>"><?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
							<select
								name="<?php echo esc_attr( $value['name'] ); ?>"
								id="<?php echo esc_attr( $value['name'] ); ?>"
								type="<?php echo esc_attr( $value['type'] ); ?>"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>"
								<?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>
							>
								<option value=""><?php echo esc_html( $value['placeholder'] ); ?></option>
								<?php foreach ( $value['options'] as $key => $val ) : ?>
									<option
										value="<?php echo esc_attr( $key ); ?>" <?php selected( in_array( (string) $key, $value['value'], true ), true ); ?>><?php echo esc_html( $val ); ?></option>
								<?php endforeach; ?>
							</select>
							<?php echo wp_kses_post( $suffix ); ?>
							<?php echo wp_kses_post( $description ); ?>
						</td>

					</tr>
					<?php

					break;
				case 'radio':
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $value['name'] ); ?>"><?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php echo esc_html( $value['title'] ); ?></span></legend>
								<?php foreach ( $value['options'] as $key => $val ) : ?>
									<label
										for="<?php echo esc_attr( $value['name'] ); ?>_<?php echo esc_attr( $key ); ?>">
										<input
											name="<?php echo esc_attr( $value['name'] ); ?>"
											id="<?php echo esc_attr( $value['name'] ); ?>_<?php echo esc_attr( $key ); ?>"
											type="radio"
											value="<?php echo esc_attr( $key ); ?>"
											style="<?php echo esc_attr( $value['css'] ); ?>"
											class="<?php echo esc_attr( $value['class'] ); ?>"
											<?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>
											<?php checked( $value['value'], $key ); ?>
										/>
										<?php echo esc_html( $val ); ?>
									</label>
									<br/>
								<?php endforeach; ?>
							</fieldset>
							<?php echo wp_kses_post( $description ); ?>
						</td>
					</tr>
					<?php
					break;
				case 'checkbox':
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $value['name'] ); ?>"><?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php echo esc_html( $value['title'] ); ?></span></legend>
								<label for="<?php echo esc_attr( $value['name'] ); ?>">
									<input
										name="<?php echo esc_attr( $value['name'] ); ?>"
										id="<?php echo esc_attr( $value['name'] ); ?>"
										type="checkbox"
										value="yes"
										style="<?php echo esc_attr( $value['css'] ); ?>"
										class="<?php echo esc_attr( $value['class'] ); ?>"
										<?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>
										<?php checked( $value['value'], 'yes' ); ?>
									/>
									<?php echo wp_kses_post( $description ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
					<?php
					break;

				case 'checkboxes':
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $value['name'] ); ?>"><?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php echo esc_html( $value['title'] ); ?></span></legend>
								<?php foreach ( $value['options'] as $key => $val ) : ?>
									<label
										for="<?php echo esc_attr( $value['name'] ); ?>_<?php echo esc_attr( $key ); ?>">
										<input
											name="<?php echo esc_attr( $value['name'] ); ?>[]"
											id="<?php echo esc_attr( $value['name'] ); ?>_<?php echo esc_attr( $key ); ?>"
											type="checkbox"
											value="<?php echo esc_attr( $key ); ?>"
											style="<?php echo esc_attr( $value['css'] ); ?>"
											class="<?php echo esc_attr( $value['class'] ); ?>"
											<?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>
											<?php checked( in_array( (string) $key, $value['value'], true ), true ); ?>
										/>
										<?php echo esc_html( $val ); ?>
									</label>
									<br/>
								<?php endforeach; ?>
								<?php echo wp_kses_post( $description ); ?>
							</fieldset>
						</td>
					</tr>
					<?php
					break;

				case 'color':
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $value['name'] ); ?>"><?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php echo esc_html( $value['title'] ); ?></span></legend>
								<input
									name="<?php echo esc_attr( $value['name'] ); ?>"
									id="<?php echo esc_attr( $value['name'] ); ?>"
									type="text"
									value="<?php echo esc_attr( $value['value'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									class="colorpick <?php echo esc_attr( $value['class'] ); ?>"
									<?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>
								/>
								<?php echo wp_kses_post( $suffix ); ?>
								<?php echo wp_kses_post( $description ); ?>
							</fieldset>
						</td>
					</tr>
					<?php
					break;
				// Days/months/years selector.
				case 'relative_date_selector':
					$periods         = array(
						'days'   => __( 'Day(s)', 'wp-ever-accounting' ),
						'weeks'  => __( 'Week(s)', 'wp-ever-accounting' ),
						'months' => __( 'Month(s)', 'wp-ever-accounting' ),
						'years'  => __( 'Year(s)', 'wp-ever-accounting' ),
					);
					$value['number'] = ! empty( $value['number'] ) ? absint( $value['number'] ) : '';
					$value['period'] = ! empty( $value['period'] ) ? $value['period'] : 'days';

					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $value['name'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							<?php echo wp_kses_post( $tooltip ); ?>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php echo esc_html( $value['title'] ); ?></span></legend>
								<input
									name="<?php echo esc_attr( $value['name'] ); ?>"
									id="<?php echo esc_attr( $value['name'] ); ?>"
									type="number"
									value="<?php echo esc_attr( $value['number'] ); ?>"
									style="width: 80px;<?php echo esc_attr( $value['css'] ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									step="1"
									min="1"
									<?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>
								/>&nbsp;
								<select name="<?php echo esc_attr( $value['name'] ); ?>[period]" style="width: auto;">
									<?php foreach ( $periods as $period => $label ) : ?>
										<option
											value="<?php echo esc_attr( $period ); ?>" <?php selected( $period, $value['period'] ); ?>><?php echo esc_html( $label ); ?></option>
									<?php endforeach; ?>
								</select>
								<?php echo wp_kses_post( $description ); ?>
							</fieldset>
						</td>
					</tr>
					<?php
					break;

				case 'wp_editor':
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $value['name'] ); ?>"><?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
							<?php wp_editor( $value['value'], $value['name'], array( 'textarea_name' => $value['name'] ) ); ?>
							<?php echo wp_kses_post( $description ); ?>
						</td>
					</tr>
					<?php
					break;

				case 'callback':
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $value['name'] ); ?>"><?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
							<?php call_user_func( $value['callback'], $value ); ?>
						</td>
					</tr>
					<?php
					break;

				case 'custom':
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $value['name'] ); ?>"><?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip ); ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
							<?php echo wp_kses_post( $value['html'] ); ?>
						</td>
					</tr>
					<?php
					break;

				default:
					/**
					 * Custom form field type.
					 *
					 * @since 1.0.0
					 */
					do_action( 'ever_accounting_admin_field_' . $value['type'], $value );
					break;
			}
		}
	}

	/**
	 * Get a setting from the settings API.
	 *
	 * @param string $option_name Option name.
	 * @param mixed  $fallback Default value.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public static function get_option( $option_name, $fallback = '' ) {
		// Array value.
		if ( strstr( $option_name, '[' ) ) {
			parse_str( $option_name, $option_array );

			// Option name is first key.
			$option_name = current( array_keys( $option_array ) );

			// Get value.
			$option_values = get_option( $option_name, '' );
			$key           = key( $option_array[ $option_name ] );

			$option_value = isset( $option_values[ $key ] ) ? $option_values[ $key ] : $fallback;
		} else {
			// Single value.
			$option_value = get_option( $option_name, $fallback );
		}

		if ( is_array( $option_value ) ) {
			$option_value = wp_unslash( $option_value );
		} elseif ( ! is_null( $option_value ) ) {
			$option_value = stripslashes( $option_value );
		}

		return ( null === $option_value ) ? $default : $option_value;
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
			$data = array_map( 'wp_unslash', $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
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
			$option_name = isset( $option['name'] ) ? $option['name'] : $option['id'];

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
				case 'select':
					$value = eac_clean( $raw_value );
					break;
				default:
					$value = eac_clean( $raw_value );
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
	 * @return array The description and tip as a 2 element array.
	 * @since  1.0.0
	 */
	public static function get_field_description( $value ) {
		$description = '';
		$tooltip     = '';

		if ( true === $value['desc_tip'] ) {
			$tooltip = $value['desc'];
		} elseif ( ! empty( $value['desc_tip'] ) ) {
			$description = $value['desc'];
			$tooltip     = $value['desc_tip'];
		} elseif ( ! empty( $value['desc'] ) ) {
			$description = $value['desc'];
		}

		if ( $description && in_array( $value['type'], array( 'radio' ), true ) ) {
			$description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
		} elseif ( $description && in_array( $value['type'], array( 'checkbox' ), true ) ) {
			$description = wp_kses_post( $description );
		} elseif ( $description ) {
			$description = '<p class="description">' . wp_kses_post( $description ) . '</p>';
		}

		if ( $tooltip && in_array( $value['type'], array( 'checkbox' ), true ) ) {
			$tooltip = '<p class="description">' . $tooltip . '</p>';
		} elseif ( $tooltip ) {
			$tooltip = eac_tooltip( $tooltip );
		}

		return array(
			'description' => $description,
			'tooltip'     => $tooltip,
		);
	}
}
