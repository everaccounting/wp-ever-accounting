<?php

namespace Ever_Accounting\Admin;

class Settings_API {
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
	 * Get settings page ID.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get settings page label.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Get all sections for this page.
	 *
	 * @return array
	 */
	public function get_sections() {
		return array();
	}

	/**
	 * Get settings array for the default section.
	 * @return array Settings array, each item being an associative array representing a setting.
	 */
	public function get_settings( $section = '' ) {
		return array();
	}

	/**
	 * Output sections.
	 */
	public function output_sections( $current_section = '' ) {
		$sections = apply_filters( 'ever_accounting_' . $this->id . '_sections', $this->get_sections() );

		if ( empty( $sections ) || 1 === count( $sections ) ) {
			return;
		}

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $sections );

		foreach ( $sections as $id => $label ) {
			$url       = admin_url( 'admin.php?page=eaccounting-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) );
			$class     = ( $current_section === $id ? 'current' : '' );
			$separator = ( end( $array_keys ) === $id ? '' : '|' );
			$text      = esc_html( $label );
			echo "<li><a href='$url' class='$class'>$text</a> $separator </li>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</ul><br class="clear" />';
	}

	/**
	 * Output the HTML for the settings.
	 */
	public function output( $section_id ) {
		if ( ! empty( $section_id ) && method_exists( $this, "get_{$section_id}_section_settings" ) ) {
			$settings = $this->{"get_{$section_id}_section_settings"}();
		} else {
			$settings = apply_filters( 'ever_accounting_' . $this->id . '_settings', $this->get_settings( $section_id ), $section_id );
		}

		$this->output_fields( $settings );
	}

	/**
	 * Output admin fields.
	 *
	 * Loops through the ever_accounting options array and outputs each field.
	 *
	 * @param array[] $fields Opens array to output.
	 */
	public function output_fields( $fields ) {
		$defaults = array(
				'id'          => '',
				'title'       => '',
				'class'       => '',
				'css'         => '',
				'default'     => '',
				'desc'        => '',
				'placeholder' => '',
				'suffix'      => '',
				'tooltip'     => false,
				'attributes'  => array(),
				'type'        => 'text',
				'size'        => 'regular',
				'options'     => array(),
				'multiple'    => null,
				'required'    => '',
				'disabled'    => '',
				'callback'    => '',
				'attrs'       => array(),
		);


		foreach ( $fields as $field ) {
			if ( ! isset( $field['type'] ) ) {
				continue;
			}

			$field = wp_parse_args( $field, $defaults );
			if ( ! empty( $field['value'] ) && is_callable( $field['value'] ) ) {
				$field['value'] = call_user_func( $field['value'] );
			} else {
				$field['value'] = $this->get_option( $field['id'], $field['default'] );
			}

			// Custom attribute handling.
			$attributes = array();
			if ( ! empty( $field['attrs'] ) && is_array( $field['attrs'] ) ) {
				foreach ( $field['attrs'] as $attribute => $attribute_value ) {
					$attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
				}
			}

			// Description handling.
			$description              = '';
			$tooltip_html             = '';
			if ( true === $field['tooltip'] ) {
				$tooltip_html = $field['desc'];
			} elseif ( ! empty( $field['tooltip'] ) ) {
				$description  = $field['desc'];
				$tooltip_html = $field['tooltip'];
			} elseif ( ! empty( $field['desc'] ) ) {
				$description = $field['desc'];
			}
			if ( $description && in_array( $field['type'], array( 'textarea', 'radio' ), true ) ) {
				$description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
			} elseif ( $description && $field['type'] === 'checkbox' ) {
				$description = wp_kses_post( $description );
			} elseif ( $description ) {
				$description = '<p class="description">' . wp_kses_post( $description ) . '</p>';
			}

			if ( $tooltip_html && $field['type'] === 'checkbox' ) {
				$tooltip_html = '<p class="description">' . $tooltip_html . '</p>';
			} elseif ( $tooltip_html ) {
				$tooltip_html = '<span class="help-tip" data-tip="' . esc_attr( $tooltip_html ) . '"></span>';
			}

			// Switch based on type.
			switch ( $field['type'] ) {
				case 'title':
				case 'section_start':
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
						do_action( 'ever_accounting_settings_' . sanitize_title( $field['id'] ) );
					}
					break;
				// Section Ends.
				case 'section_end':
					if ( ! empty( $field['id'] ) ) {
						do_action( 'ever_accounting_settings_' . sanitize_title( $field['id'] ) . '_end' );
					}
					echo '</table>';
					if ( ! empty( $field['id'] ) ) {
						do_action( 'ever_accounting_settings_' . sanitize_title( $field['id'] ) . '_after' );
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
					$option_value = $field['value'];
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $field['id'] ); ?>">
								<?php echo esc_html( $field['title'] ); ?>
								<?php echo $tooltip_html; ?>
							</label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
							<input
									name="<?php echo esc_attr( $field['id'] ); ?>"
									id="<?php echo esc_attr( $field['id'] ); ?>"
									type="<?php echo esc_attr( $field['type'] ); ?>"
									style="<?php echo esc_attr( $field['css'] ); ?>"
									value="<?php echo esc_attr( $option_value ); ?>"
									class="<?php echo esc_attr( $field['class'] ); ?>"
									placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
									<?php echo implode( ' ', $attributes ); ?>
							/>
							<?php echo esc_html( $field['suffix'] ); ?> <?php echo $description; ?>
						</td>
					</tr>
					<?php
					break;

				// Color picker.
				case 'color':
					$option_value = $field['value'];

					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $field['id'] ); ?>">
								<?php echo esc_html( $field['title'] ); ?>
								<?php echo $tooltip_html; ?>
							</label>
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
									<?php echo implode( ' ', $attributes ); ?>
							/>&lrm;
							<?php echo $description; ?>
							<div id="colorPickerDiv_<?php echo esc_attr( $field['id'] ); ?>"
								 class="colorpickdiv"
								 style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
						</td>
					</tr>
					<?php
					break;

				// Textarea.
				case 'textarea':
					$option_value = $field['value'];

					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $field['id'] ); ?>">
								<?php echo esc_html( $field['title'] ); ?>
								<?php echo $tooltip_html; ?>
							</label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
							<?php echo $description; ?>

							<textarea
									name="<?php echo esc_attr( $field['id'] ); ?>"
									id="<?php echo esc_attr( $field['id'] ); ?>"
									style="<?php echo esc_attr( $field['css'] ); ?>"
									class="<?php echo esc_attr( $field['class'] ); ?>"
									placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
								<?php echo implode( ' ', $attributes ); ?>><?php echo esc_textarea( $option_value ); ?></textarea>
						</td>
					</tr>
					<?php
					break;

				// Select boxes.
				case 'select':
				case 'multiselect':
					$option_value = $field['value'];

					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $field['id'] ); ?>">
								<?php echo esc_html( $field['title'] ); ?>
								<?php echo $tooltip_html; ?>
							</label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
							<select
									name="<?php echo esc_attr( $field['id'] ); ?><?php echo ( 'multiselect' === $field['type'] ) ? '[]' : ''; ?>"
									id="<?php echo esc_attr( $field['id'] ); ?>"
									style="<?php echo esc_attr( $field['css'] ); ?>"
									class="<?php echo esc_attr( $field['class'] ); ?>"
									<?php echo implode( ' ', $attributes ); ?>
									<?php echo 'multiselect' === $field['type'] ? 'multiple="multiple"' : ''; ?>
							>
								<?php
								foreach ( $field['options'] as $key => $val ) {
									?>
									<option value="<?php echo esc_attr( $key ); ?>"
											<?php

											if ( is_array( $option_value ) ) {
												selected( in_array( (string) $key, $option_value, true ), true );
											} else {
												selected( $option_value, (string) $key );
											}

											?>
									><?php echo esc_html( $val ); ?></option>
									<?php
								}
								?>
							</select>
							<?php echo $description; ?>
						</td>
					</tr>
					<?php
					break;

				// Radio inputs.
				case 'radio':
					$option_value = $field['value'];

					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $field['id'] ); ?>">
								<?php echo esc_html( $field['title'] ); ?>
								<?php echo $tooltip_html; ?>
							</label>
						</th>
						<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
							<fieldset>
								<?php echo $description;
								?>
								<ul>
									<?php
									foreach ( $field['options'] as $key => $val ) {
										?>
										<li>
											<label>
												<input
														name="<?php echo esc_attr( $field['id'] ); ?>"
														value="<?php echo esc_attr( $key ); ?>"
														type="radio"
														style="<?php echo esc_attr( $field['css'] ); ?>"
														class="<?php echo esc_attr( $field['class'] ); ?>"
														<?php echo implode( ' ', $attributes ); ?>
														<?php checked( $key, $option_value ); ?>
												/> <?php echo esc_html( $val ); ?></label>
										</li>
										<?php
									}
									?>
								</ul>
							</fieldset>
						</td>
					</tr>
					<?php
					break;

				// Checkbox input.
				case 'checkbox':
					$option_value = $field['value'];
					$visibility_class = array();

					if ( ! isset( $field['hide_if_checked'] ) ) {
						$field['hide_if_checked'] = false;
					}
					if ( ! isset( $field['show_if_checked'] ) ) {
						$field['show_if_checked'] = false;
					}
					if ( 'yes' === $field['hide_if_checked'] || 'yes' === $field['show_if_checked'] ) {
						$visibility_class[] = 'hidden_option';
					}
					if ( 'option' === $field['hide_if_checked'] ) {
						$visibility_class[] = 'hide_options_if_checked';
					}
					if ( 'option' === $field['show_if_checked'] ) {
						$visibility_class[] = 'show_options_if_checked';
					}

					if ( ! isset( $field['checkboxgroup'] ) || 'start' === $field['checkboxgroup'] ) {
						?>
						<tr valign="top" class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
						<th scope="row" class="titledesc"><?php echo esc_html( $field['title'] ); ?></th>
						<td class="forminp forminp-checkbox">
						<fieldset>
						<?php
					} else {
						?>
						<fieldset class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
						<?php
					}

					if ( ! empty( $field['title'] ) ) { ?>
						<legend class="screen-reader-text"><span><?php echo esc_html( $field['title'] ); ?></span></legend>
					<?php } ?>
					<label for="<?php echo esc_attr( $field['id'] ); ?>">
						<input
								name="<?php echo esc_attr( $field['id'] ); ?>"
								id="<?php echo esc_attr( $field['id'] ); ?>"
								type="checkbox"
								class="<?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>"
								value="1"
								<?php checked( $option_value, 'yes' ); ?>
								<?php echo implode( ' ', $attributes ); ?>
						/> <?php echo $description; ?>
					</label> <?php echo $tooltip_html; ?>
					<?php
					if ( ! isset( $field['checkboxgroup'] ) || 'end' === $field['checkboxgroup'] ) {
						echo '</fieldset></td></tr>';
					} else {
						echo '</fieldset>';
					}
					break;

				// Single page selects.
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
							<label><?php echo esc_html( $field['title'] ); ?><?php echo $tooltip_html;
								?></label>
						</th>
						<td class="forminp">
							<?php
							echo str_replace( ' id=', " data-placeholder='" . esc_attr__( 'Select a page&hellip;', 'text_domain' ) . "' style='" . $field['css'] . "' class='" . $field['class'] . "' id=", wp_dropdown_pages( $args ) );
							echo $description;
							?>
						</td>
					</tr>
					<?php
					break;

				// Default: run an action.
				default:
					do_action( 'ever_accounting_admin_field_' . $field['type'], $field );
					break;

			}
		}
	}

	/**
	 * Save settings.
	 */
	public function save( $section ) {
		$settings = $this->get_settings( $section );

		return $this->save_fields( $settings );
	}

	/**
	 * Save admin fields.
	 *
	 * Loops through the ever_accounting options array and outputs each field.
	 *
	 * @param array $options Options array to output.
	 * @param array $data Optional. Data to use for saving. Defaults to $_POST.
	 *
	 * @return bool
	 */
	public function save_fields( $options, $data = null ) {
		if ( is_null( $data ) ) {
			$data = $_POST; // WPCS: input var okay, CSRF ok.
		}
		if ( empty( $data ) ) {
			return false;
		}

		// Options to update will be stored here and saved later.
		$update_options   = array();
		$autoload_options = array();
		// Loop options and get values to save.
		foreach ( $options as $option ) {
			if ( ! isset( $option['id'] ) || ! isset( $option['type'] ) || ( isset( $option['is_option'] ) && false === $option['is_option'] ) ) {
				continue;
			}

			// Get posted value.
			if ( strpos( $option['id'], '[' ) !== false ) {
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
				case 'wysiwyg':
					$value = wp_kses_post( trim( $raw_value ) );
					break;
				case 'multiselect':
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
					$value = $this->clean( $raw_value );
					break;
			}

			/**
			 * Sanitize the value of an option.
			 *
			 * @since 2.4.0
			 */
			$value = apply_filters( 'ever_accounting_admin_settings_sanitize_option', $value, $option, $raw_value );

			/**
			 * Sanitize the value of an option by option name.
			 *
			 * @since 2.4.0
			 */
			$value = apply_filters( "ever_accounting_admin_settings_sanitize_option_$option_name", $value, $option, $raw_value );

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

			$autoload_options[ $option_name ] = isset( $option['autoload'] ) ? (bool) $option['autoload'] : true;
		}

		// Save all options in our array.
		foreach ( $update_options as $name => $value ) {
			update_option( $name, $value, $autoload_options[ $name ] ? 'yes' : 'no' );
		}

		return true;
	}

	/**
	 * Get a setting from the settings API.
	 *
	 * @param string $option_name Option name.
	 * @param mixed $default Default value.
	 *
	 * @return mixed
	 */
	public function get_option( $option_name, $default = '' ) {
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
			$option_value = wp_unslash( $option_value );
		} elseif ( ! is_null( $option_value ) ) {
			$option_value = stripslashes( $option_value );
		}

		return ( null === $option_value ) ? $default : $option_value;
	}

	/**
	 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
	 * Non-scalar values are ignored.
	 *
	 * @param string|array $var Data to sanitize.
	 *
	 * @return string|array
	 */
	public function clean( $var ) {
		if ( is_array( $var ) ) {
			return array_map( array( $this, 'clean' ), $var );
		}

		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}
