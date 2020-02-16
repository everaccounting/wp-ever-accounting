<?php
defined( 'ABSPATH' ) || die();

abstract class EAccounting_Settings_Page {
	/**
	 * Setting page id.
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Setting page label.
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'eaccounting_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'eaccounting_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'eaccounting_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'eaccounting_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings page ID.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get settings page label.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Add this page to settings.
	 *
	 * @param array $pages
	 *
	 * @return mixed
	 */
	public function add_settings_page( $pages ) {
		$pages[ $this->id ] = $this->label;

		return $pages;
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings( $section = null ) {
		return apply_filters( 'eaccounting_get_settings_' . $this->id, array(), $section );
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		return apply_filters( 'eaccounting_get_sections_' . $this->id, array() );
	}

	/**
	 * Get option name
	 * @return string
	 */
	public function get_option_name() {
		$option_name = 'eaccounting_settings_' . $this->id;
		if ( $sections = $this->get_sections() ) {

			if ( isset( $_REQUEST['section'] ) && array_key_exists( sanitize_key( $_REQUEST['section'] ), $sections ) ) {
				$current_section = sanitize_text_field( wp_unslash( $_REQUEST['section'] ) );
				$option_name     = 'eaccounting_settings_' . $this->id . '_' . $current_section;
			} else {
				$option_name = 'eaccounting_settings_' . $this->id . '_' . strtolower( reset( $sections ) );
			}
		}

		return $option_name;
	}

	/**
	 * Output fields.
	 *
	 * @param $fields
	 */
	public function output_fields( $fields ) {

		foreach ( $fields as $field ) {

			//make a field with default options.
			$field = wp_parse_args( $field, array(
				'type'         => 'text',
				'id'           => '',
				'title'        => '',
				'default'      => '',
				'class'        => '',
				'css'          => '',
				'desc'         => '',
				'tooltip'      => '',
				'placeholder'  => '',
				'suffix'       => '',
				'description'  => '',
				'custom_attrs' => [],
			) );

			//if value not set.
			if ( ! isset( $field['value'] ) ) {
				$field['value'] = self::get_option( $field['id'], $field['default'] );
			}

			//if type not set then return.
			if ( ! isset( $field['type'] ) ) {
				continue;
			}

			//Custom attribute handling.
			$custom_attributes = array();
			if ( ! empty( $field['custom_attrs'] ) && is_array( $field['custom_attrs'] ) ) {
				foreach ( $field['custom_attrs'] as $attribute => $attribute_value ) {
					$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
				}
			}

			// Description handling.
			$description = '';
			if ( $field['desc'] && in_array( $field['type'], array( 'textarea', 'radio' ), true ) ) {
				$description = '<p style="margin-top:0">' . wp_kses_post( $field['desc'] ) . '</p>';
			} elseif ( $field['desc'] && in_array( $field['type'], array( 'checkbox' ), true ) ) {
				$description = wp_kses_post( $field['desc'] );
			} elseif ( $field['desc'] ) {
				$description = '<p class="description">' . wp_kses_post( $field['desc'] ) . '</p>';
			}

			//tip handling.
			$tooltip = '';
			if ( $field['tooltip'] && in_array( $field['type'], array( 'checkbox' ), true ) ) {
				$tooltip = '<p class="description">' . $field['tooltip'] . '</p>';
			} elseif ( $field['tooltip'] ) {
				$tooltip = sprintf( '<span class="eaccounting-help-tip" data-tip="%s">[?]</span>', wp_kses_post( $field['tooltip'] ) );
			}


			//sanitize type.
			$type                = esc_attr( sanitize_title( $field['type'] ) );

			switch ( $type ) {
				case 'title':
					if ( ! empty( $field['title'] ) ) {
						echo '<h2>' . esc_html( $field['title'] ) . '</h2>';
					}
					if ( ! empty( $field['desc'] ) ) {
						echo '<div id="' . esc_attr( sanitize_title( $field['id'] ) ) . '-description">';
						echo wp_kses_post( wpautop( wptexturize( $field['desc'] ) ) );
						echo '</div>';
					}
					echo '<table class="form-table">' . "\n\n";
					if ( ! empty( $field['id'] ) ) {
						do_action( 'eaccounting_settings_' . sanitize_title( $field['id'] ) );
					}
					break;
				case 'sectionend':
					if ( ! empty( $field['id'] ) ) {
						do_action( 'eaccounting_settings_' . sanitize_title( $field['id'] ) . '_end' );
					}
					echo '</table>';
					if ( ! empty( $field['id'] ) ) {
						do_action( 'eaccounting_settings_' . sanitize_title( $field['id'] ) . '_after' );
					}
					break;

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
					$option_value = $field['value'];
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?><?php echo $tooltip; ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
							<input
								name="<?php echo esc_attr( $field['id'] ); ?>"
								id="<?php echo esc_attr( $field['id'] ); ?>"
								type="<?php echo esc_attr( $field['type'] ); ?>"
								style="<?php echo esc_attr( $field['css'] ); ?>"
								value="<?php echo esc_attr( $option_value ); ?>"
								class="regular-text <?php echo esc_attr( $field['class'] ); ?>"
								placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
								<?php echo implode( ' ', $custom_attributes ); ?>
							/><?php echo esc_html( $field['suffix'] ); ?> <?php echo $description; ?>
						</td>
					</tr>
					<?php
					break;

				case 'color':
					$option_value = $field['value'];
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?><?php echo $tooltip; ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">&lrm;
							<span class="colorpickpreview" style="background: <?php echo esc_attr( $option_value ); ?>">&nbsp;</span>
							<input
								name="<?php echo esc_attr( $field['id'] ); ?>"
								id="<?php echo esc_attr( $field['id'] ); ?>"
								type="text"
								dir="ltr"
								style="<?php echo esc_attr( $field['css'] ); ?>"
								value="<?php echo esc_attr( $option_value ); ?>"
								class="<?php echo esc_attr( $field['class'] ); ?>colorpick"
								placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
								<?php echo implode( ' ', $custom_attributes ); ?>
							/>&lrm; <?php echo $description; ?>
							<div id="colorPickerDiv_<?php echo esc_attr( $field['id'] ); ?>" class="colorpickdiv"
							     style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
						</td>
					</tr>
					<?php
					break;

				case 'textarea':
					$option_value = $field['value'];
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?><?php echo $tooltip; ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
							<?php echo $description; ?>
							<textarea
								name="<?php echo esc_attr( $field['id'] ); ?>"
								id="<?php echo esc_attr( $field['id'] ); ?>"
								style="<?php echo esc_attr( $field['css'] ); ?>"
								class="regular-text <?php echo esc_attr( $field['class'] ); ?>"
								placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
								<?php echo implode( ' ', $custom_attributes ); ?>><?php echo esc_textarea( $option_value ); ?></textarea>
						</td>
					</tr>
					<?php
					break;

				case 'wysiwyg':
					$option_value = $field['value'];
					$editor_args = [
						'editor_class'  => $field['css'],
						'textarea_rows' => 10
					];
					?>
					<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="<?php echo esc_attr( $field['id'] ); ?>">
							<?php echo esc_html( $field['title'] ); ?>
						</label>
						<?php echo wp_kses_post( $tooltip ); ?>
					</th>
					<td class="forminp forminp-<?php echo esc_attr( $field['type'] ) ?>">
						<?php wp_editor( $option_value, $field['id'], $editor_args ); ?>
						<?php echo wp_kses_post( $description ); ?>
					</td>
					</tr><?php
					break;

				case 'select':
				case 'multiselect':
					$option_value = $field['value'];
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?><?php echo $tooltip; ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
							<select
								name="<?php echo esc_attr( $field['id'] ); ?><?php echo ( 'multiselect' === $field['type'] ) ? '[]' : ''; ?>"
								id="<?php echo esc_attr( $field['id'] ); ?>"
								style="<?php echo esc_attr( $field['css'] ); ?>"
								class="regular-text <?php echo esc_attr( $field['class'] ); ?>"
								<?php echo implode( ' ', $custom_attributes ); ?>
								<?php echo 'multiselect' === $field['type'] ? 'multiple="multiple"' : ''; ?>>
								<?php foreach ( $field['options'] as $key => $val ) { ?>
									<option value="<?php echo esc_attr( $key ); ?>"
										<?php if ( is_array( $option_value ) ) {
											selected( in_array( (string) $key, $option_value, true ), true );
										} else {
											selected( $option_value, (string) $key );
										} ?>>
										<?php echo esc_html( $val ); ?>
									</option>
								<?php } ?>
							</select>
							<?php echo $description; ?>
						</td>
					</tr>
					<?php
					break;

				case 'radio':
					$option_value = $field['value'];
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?><?php echo $tooltip; ?></label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
							<fieldset>
								<?php echo $description; ?>
								<ul>
									<?php foreach ( $field['options'] as $key => $val ) { ?>
										<li>
											<label>
												<input
													name="<?php echo esc_attr( $field['id'] ); ?>"
													value="<?php echo esc_attr( $key ); ?>"
													type="radio"
													style="<?php echo esc_attr( $field['css'] ); ?>"
													class="<?php echo esc_attr( $field['class'] ); ?>"
													<?php echo implode( ' ', $custom_attributes ); ?>
													<?php checked( $key, $option_value ); ?>
												/>
												<?php echo esc_html( $val ); ?>
											</label>
										</li>
									<?php } ?>
								</ul>
							</fieldset>
						</td>
					</tr>
					<?php
					break;

				case 'checkbox':
					$option_value     = $field['value'];
					if ( ! isset( $field['checkboxgroup'] ) || 'start' === $field['checkboxgroup'] ) {
						echo sprintf('<tr valign="top"><th scope="row" class="titledesc">%s</th><td class="forminp forminp-checkbox"><fieldset>', esc_html( $field['title'] ));
						} else {
						echo '<fieldset>';
					} ?>
						<label for="<?php echo esc_attr( $field['id'] ); ?>">
								<input
									name="<?php echo esc_attr( $field['id'] ); ?>"
									id="<?php echo esc_attr( $field['id'] ); ?>"
									type="checkbox"
									class="<?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>"
									value="1"
									<?php checked( $option_value, 'yes' ); ?>
									<?php echo implode( ' ', $custom_attributes ); ?>
								/> <?php echo $description; ?>
							</label> <?php echo $tooltip; ?>
					<?php
					if ( ! isset( $field['checkboxgroup'] ) || 'end' === $field['checkboxgroup'] ) {
						echo '</fieldset></td></tr>';
					} else {
						echo '</fieldset>';
					}
					break;

				case 'multicheck' :
					$option_value = (array) $field['value'];
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label
								for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
							<?php echo wp_kses_post( $tooltip ); ?>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( $field['type'] ) ?>">
							<fieldset>
								<ul>
									<?php foreach ( $field['options'] as $key => $val ) { ?>
										<li>
											<label>
												<input
													name="<?php echo esc_attr( $field['id'] ); ?>[<?php echo esc_attr( $key ); ?>]"
													value="<?php echo esc_html( $key ); ?>"
													type="checkbox"
													style="<?php echo esc_attr( $field['css'] ); ?>"
													class="<?php echo esc_attr( $field['class'] ); ?>"
													<?php echo esc_html( implode( ' ', $custom_attributes ) ); ?>
													<?php checked( in_array( $key, $option_value ) ); ?>
												/><?php echo esc_html( $val ) ?>
											</label>
										</li>
									<?php } ?>
								</ul>
							</fieldset>
							<?php echo wp_kses_post( $description ); ?>
						</td>
					</tr>
					<?php
					break;
				case 'single_select_page':
					$args = array(
						'name'             => $field['id'],
						'id'               => $field['id'],
						'sort_column'      => 'menu_order',
						'sort_order'       => 'ASC',
						'show_option_none' => ' ',
						'class'            => $field['class'],
						'echo'             => false,
						'selected'         => absint( $field['value'] ),
						'post_status'      => 'publish,private,draft',
					);
					if ( isset( $field['args'] ) ) {
						$args = wp_parse_args( $field['args'], $args );
					}
					?>
					<tr valign="top" class="single_select_page">
						<th scope="row" class="titledesc">
							<label>
								<?php echo esc_html( $field['title'] ); ?>
								<?php echo $tooltip; ?>
							</label>
						</th>
						<td class="forminp">
							<?php echo str_replace( ' id=', " data-placeholder='" . esc_attr__( 'Select a page&hellip;', 'text-domain' ) . "' style='" . $field['css'] . "' class='" . $field['class'] . "' id=", wp_dropdown_pages( $args ) ); ?>
							<?php echo $description; ?>
						</td>
					</tr>
					<?php
					break;

				default:
					do_action( 'eaccounting_admin_field_' . $field['type'], $field );
					break;
			}


		}


	}


	/**
	 * Get a setting from the settings API.
	 *
	 * @param $option_name
	 * @param string $default
	 *
	 * @return array|mixed|string|void|null
	 */
	public function get_option( $option_name, $default = '' ) {

		$options = get_option( $this->get_option_name(), array() );
		$option_value = isset( $options[$option_name] ) ? $options[$option_name] : $default;

		if ( ! $option_name ) {
			return $default;
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
	 * Loops though the options array and outputs each field.
	 *
	 * @param array $options Options array to output.
	 * @param array $data    Optional. Data to use for saving. Defaults to $_POST.
	 *
	 * @return bool
	 */
	public function save_fields( $options, $data = null ) {
		if ( is_null( $data ) ) {
			$data = $_REQUEST; // WPCS: input var okay, CSRF ok.
		}

		if ( empty( $data ) ) {
			return false;
		}

		// Options to update will be stored here and saved later.
		$update_options   = array();

		// Loop options and get values to save.
		foreach ( $options as $option ) {
			if ( ! isset( $option['id'] ) || ! isset( $option['type'] ) || ( isset( $option['is_option'] ) && false === $option['is_option'] ) ) {
				continue;
			}

			// Get posted value.
			if ( strstr( $option['id'], '[' ) ) {
				parse_str( $option['id'], $option_name_array );
				$option_name  = current( array_keys( $option_name_array ) );
				$setting_name = key( $option_name_array[ $option_name ] );
				$raw_value    = isset( $data[ $option_name ][ $setting_name ] ) ? wp_unslash( $data[ $option_name ][ $setting_name ] ) : null;
			} else {
				$option_name  = $option['id'];
				$setting_name = '';
				$raw_value    = isset( $data[ $option['id'] ] ) ? wp_unslash( $data[ $option['id'] ] ) : null;
			}

			// Format the value based on option type.
			switch ( $option['type'] ) {
				case 'checkbox':
					$value = '1' === $raw_value || 'yes' === $raw_value ? 'yes' : 'no';
					break;
				case 'textarea':
					$value = wp_kses_post( trim( $raw_value ) );
					break;
				case 'multicheck':
				case 'multiselect':
					$value = array_filter( array_map( 'sanitize_text_field', (array) $raw_value ) );
					break;
				case 'select':
					$allowed_values = empty( $option['options'] ) ? array() : array_map( 'strval', array_keys( $option['options'] ) );
					if ( empty( $option['default'] ) && empty( $allowed_values ) ) {
						$value = null;
						break;
					}
					$default = ( empty( $option['default'] ) ? $allowed_values[0] : $option['default'] );
					$value   = in_array( $raw_value, $allowed_values, true ) ? $raw_value : $default;
					break;
				default:
					$value = sanitize_text_field( $raw_value );
					break;
			}

			$value = apply_filters( 'eaccounting_admin_settings_sanitize_option', $value, $option, $raw_value );

			// Sanitize the value of an option by option name.
			$value = apply_filters( "eaccounting_admin_settings_sanitize_option_$option_name", $value, $option, $raw_value );

			if ( is_null( $value ) ) {
				continue;
			}

			// Check if option is an array and handle that differently to single values.
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
			//Fire an action before saved.
			do_action( 'eaccounting_update_option', $option );
		}

		// Save all options in our array.
		update_option( $this->get_option_name(), $update_options );

		return true;
	}

	/**
	 * Output sections.
	 */
	public function output_sections() {
		global $current_section;

		$sections = $this->get_sections();

		if ( empty( $sections ) || 1 === sizeof( $sections ) ) {
			return;
		}

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $sections );

		foreach ( $sections as $id => $label ) {
			echo '<li><a href="' . admin_url( 'admin.php?page=eaccounting-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
		}

		echo '</ul><br class="clear" />';
	}

	/**
	 * Output settings
	 */
	public function output() {
		global $current_section;
		do_action( 'eaccounting_settings_start' );
		$settings = $this->get_settings($current_section);
		self::output_fields( $settings );

	}

	/**
	 * Save Settings
	 */
	public function save() {
		global $current_section;
		$settings = $this->get_settings($current_section);
		self::save_fields($settings);
	}
}
