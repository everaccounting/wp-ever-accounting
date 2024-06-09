<?php

defined( 'ABSPATH' ) || exit();

/**
 * Form input field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.2.0
 * @return void
 */
function eac_form_field_v2( $field ) {
	$defaults = array(
		'type'          => 'text',
		'name'          => '',
		'id'            => '',
		'class'         => '',
		'style'         => '',
		'default'       => '',
		'label'         => '',
		'suffix'        => '',
		'prefix'        => '',
		'wrapper_class' => '',
		'wrapper_style' => '',
	);

	$field = wp_parse_args( $field, $defaults );

	/**
	 * Filter the arguments of a form group before it is rendered.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.2.0
	 */
	$field = apply_filters( 'ever_accounting_form_field_args', $field );

	$field['name']          = empty( $field['name'] ) ? $field['id'] : $field['name'];
	$field['id']            = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value']         = empty( $field['value'] ) ? $field['default'] : $field['value'];
	$field['class']         = array_filter( array_unique( wp_parse_list( $field['class'] ) ) );
	$field['class']         = array_map( 'sanitize_html_class', $field['class'] );
	$field['class']         = implode( ' ', $field['class'] );
	$field['wrapper_class'] = array_filter( array_unique( wp_parse_list( $field['wrapper_class'] ) ) );
	$field['wrapper_class'] = array_map( 'sanitize_html_class', $field['wrapper_class'] );
	$field['wrapper_class'] = implode( ' ', $field['wrapper_class'] );

	$attrs = array();
	foreach ( $field as $k => $v ) {
		if ( empty( $k ) || empty( $v ) ) {
			continue;
		}

		if ( is_array( $v ) || is_object( $v ) ) {
			$v = wp_json_encode( $v, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP );
		}

		if ( strpos( $k, 'attr-' ) === 0 ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( str_replace( 'attr-', '', $k ) ), esc_attr( $v ) );
		} elseif ( strpos( $k, 'data-' ) === 0 ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( $k ), esc_attr( $v ) );
		} elseif ( in_array( $k, array( 'maxlength', 'pattern', 'readonly', 'disabled', 'required', 'autofocus' ), true ) ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( $k ), esc_attr( $k ) );
		}
	}

	// wrapper.
	printf(
		'<div class="eac-form-field eac-form-field-%1$s %2$s" id="eac-form-field-%3$s" style="%4$s">',
		esc_attr( $field['type'] ),
		esc_attr( $field['wrapper_class'] ),
		esc_attr( $field['id'] ),
		esc_attr( $field['wrapper_style'] )
	);

	if ( ! empty( $field['label'] ) ) {
		$required = true === $field['required'] ? '&nbsp;<abbr title="' . esc_attr__( 'required', 'wp-ever-accounting' ) . '"></abbr>' : '';
		$tooltip  = ! empty( $field['tooltip'] ) ? '&nbsp;' . eac_tooltip( $field['tooltip'] ) : '';
		printf(
			'<label class="eac-form-field__label" for="%1$s">%2$s%3$s%4$s</label>',
			esc_attr( $field['id'] ),
			esc_html( $field['label'] ),
			wp_kses_post( $tooltip ),
			wp_kses_post( $required ),
		);
	}

	switch ( $field['type'] ) {
		case 'text':
		case 'email':
		case 'number':
		case 'password':
		case 'hidden':
		case 'url':
			?>
			<div class="eac-form-field__group">
				<?php if ( ! empty( $field['prefix'] ) ) : ?>
					<span class="eac-form-field__addon"><?php echo wp_kses_post( $field['prefix'] ); ?></span>
				<?php endif; ?>
				<input type="<?php echo esc_attr( $field['type'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" class="eac-form-field__input <?php echo esc_attr( $field['class'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" style="<?php echo esc_attr( $field['style'] ); ?>" <?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>>
				<?php if ( ! empty( $field['suffix'] ) ) : ?>
					<span class="eac-form-field__addon"><?php echo wp_kses_post( $field['suffix'] ); ?></span>
				<?php endif; ?>
			</div>
			<?php

			break;

	}

	// end wrapper.
	echo '</div><!-- .eac-form-field -->';
}

/**
 * Form input field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.2.0
 * @return void
 */
function eac_form_field( $field ) {
	$defaults = array(
		'type'          => 'text',
		'name'          => '',
		'id'            => '',
		'placeholder'   => '',
		'required'      => false,
		'readonly'      => false,
		'disabled'      => false,
		'autofocus'     => false,
		'class'         => '',
		'style'         => '',
		'options'       => array(),
		'default'       => '',
		'label'         => '',
		'suffix'        => '',
		'prefix'        => '',
		'wrapper_class' => '',
		'wrapper_style' => '',
	);

	$field = wp_parse_args( $field, $defaults );

	/**
	 * Filter the arguments of a form group before it is rendered.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.2.0
	 */
	$field = apply_filters( 'ever_accounting_form_field_args', $field );

	// Set default name and ID attributes if not provided.
	$field['name']          = empty( $field['name'] ) ? $field['id'] : $field['name'];
	$field['id']            = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value']         = empty( $field['value'] ) ? $field['default'] : $field['value'];
	$field['class']         = array_filter( array_unique( wp_parse_list( $field['class'] ) ) );
	$field['class']         = array_map( 'sanitize_html_class', $field['class'] );
	$field['class']         = implode( ' ', $field['class'] );
	$field['wrapper_class'] = array_filter( array_unique( wp_parse_list( $field['wrapper_class'] ) ) );
	$field['wrapper_class'] = array_map( 'sanitize_html_class', $field['wrapper_class'] );
	$field['wrapper_class'] = implode( ' ', $field['wrapper_class'] );

	// Prepare attributes.
	// Anything that starts with "attr-" will be added to the attributes.
	$attrs = array();
	foreach ( $field as $k => $v ) {
		if ( empty( $k ) || empty( $v ) ) {
			continue;
		}

		if ( is_array( $v ) || is_object( $v ) ) {
			$v = wp_json_encode( $v, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP );
		}

		if ( strpos( $k, 'attr-' ) === 0 ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( str_replace( 'attr-', '', $k ) ), esc_attr( $v ) );
		} elseif ( strpos( $k, 'data-' ) === 0 ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( $k ), esc_attr( $v ) );
		} elseif ( in_array( $k, array( 'maxlength', 'pattern', 'readonly', 'disabled', 'required', 'autofocus' ), true ) ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( $k ), esc_attr( $k ) );
		}
	}

	// Prefix.
	if ( ! empty( $field['prefix'] ) && ! preg_match( '/<[^>]+>/', $field['prefix'] ) ) {
		$prefix          = is_callable( $field['prefix'] ) ? call_user_func( $field['suffix'], $field ) : $field['prefix'];
		$field['prefix'] = '<span class="eac-form-field__addon">' . $prefix . '</span>';
	}

	// Suffix.
	if ( ! empty( $field['suffix'] ) && ! preg_match( '/<[^>]+>/', $field['suffix'] ) ) {
		$suffix          = is_callable( $field['suffix'] ) ? call_user_func( $field['suffix'], $field ) : $field['suffix'];
		$field['suffix'] = '<span class="eac-form-field__addon">' . $suffix . '</span>';
	}

	$input = '';
	switch ( $field['type'] ) {
		case 'text':
		case 'email':
		case 'number':
		case 'password':
		case 'url':
			$input = sprintf(
				'<input type="%1$s" name="%2$s" id="%3$s" class="eac-form-field__input %4$s" value="%5$s" placeholder="%6$s" style="%7$s" %8$s>',
				esc_attr( $field['type'] ),
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['value'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			if ( ! empty( $field['prefix'] ) || ! empty( $field['suffix'] ) ) {
				$input = sprintf(
					'<div class="eac-form-field__group">%1$s%2$s%3$s</div>',
					wp_kses_post( $field['prefix'] ),
					$input,
					wp_kses_post( $field['suffix'] )
				);
			}

			break;

		case 'select':
			$field['value']       = wp_parse_list( $field['value'] );
			$field['value']       = array_map( 'strval', $field['value'] );
			$field['placeholder'] = ! empty( $field['placeholder'] ) ? $field['placeholder'] : '';
			if ( ! empty( $field['multiple'] ) ) {
				$field['name'] .= '[]';
				$attrs[]        = 'multiple="multiple"';
			}

			// It may send an option_key and option_value to use in the options.
			// if its an object, it will use the object properties.
			if ( ! empty( $field['option_value'] ) && ! empty( $field['option_label'] ) ) {
				// verify options is an array otherwise we will make it an array.
				if ( ! is_array( $field['options'] ) ) {
					$field['options'] = array();
				}
				$field['options'] = array_filter( $field['options'] );
				$field['options'] = wp_list_pluck( $field['options'], $field['option_label'], $field['option_value'] );
			}

			$input = sprintf(
				'<select name="%1$s" id="%2$s" class="eac-form-field__select %3$s" placeholder="%4$s" style="%5$s" %6$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);

			if ( ! empty( $field['placeholder'] ) ) {
				$input .= sprintf(
					'<option value="">%s</option>',
					esc_html( $field['placeholder'] )
				);
			}

			foreach ( $field['options'] as $key => $value ) {
				$input .= sprintf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $key ),
					selected( in_array( (string) $key, $field['value'], true ), true, false ),
					esc_html( $value )
				);
			}

			$input .= '</select>';

			if ( ! empty( $field['prefix'] ) || ! empty( $field['suffix'] ) ) {
				$input = sprintf(
					'<div class="eac-form-field__group">%1$s%2$s%3$s</div>',
					wp_kses_post( $field['prefix'] ),
					$input,
					wp_kses_post( $field['suffix'] )
				);
			}

			break;

		case 'date':
			$input = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-form-field__input %3$s" value="%4$s" placeholder="%5$s" style="%6$s" %7$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['value'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);

			if ( ! empty( $field['prefix'] ) || ! empty( $field['suffix'] ) ) {
				$input = sprintf(
					'<div class="eac-form-field__group">%1$s%2$s%3$s</div>',
					wp_kses_post( $field['prefix'] ),
					$input,
					wp_kses_post( $field['suffix'] )
				);
			}

			break;

		case 'hidden':
			$input = sprintf(
				'<input type="hidden" name="%1$s" id="%2$s" class="eac-form-field__input %3$s" value="%4$s" placeholder="%5$s" style="%6$s" %7$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['value'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);

			break;

		case 'textarea':
			$rows  = ! empty( $field['rows'] ) ? absint( $field['rows'] ) : 4;
			$cols  = ! empty( $field['cols'] ) ? absint( $field['cols'] ) : 50;
			$input = sprintf(
				'<textarea name="%1$s" id="%2$s" class="eac-form-field__textarea %3$s" placeholder="%4$s" rows="%5$s" cols="%6$s" style="%7$s" %8$s>%9$s</textarea>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $rows ),
				esc_attr( $cols ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $attrs ) ),
				esc_textarea( $field['value'] )
			);

			break;

		case 'wp_editor':
			ob_start();
			wp_editor(
				$field['value'],
				$field['id'],
				array(
					'textarea_name' => $field['name'],
					'textarea_rows' => 10,
				)
			);
			$input = ob_get_clean();
			break;
	}

	// label.
	if ( ! empty( $input ) && ! empty( $field['label'] ) && 'hidden' !== $field['type'] ) {
		$required = true === $field['required'] ? '&nbsp;<abbr title="' . esc_attr__( 'required', 'wp-ever-accounting' ) . '"></abbr>' : '';
		$tooltip  = ! empty( $field['tooltip'] ) ? '&nbsp;' . eac_tooltip( $field['tooltip'] ) : '';
		$label    = sprintf(
			'<label class="eac-form-field__label" for="%1$s">%2$s%3$s%4$s</label>',
			esc_attr( $field['id'] ),
			esc_html( $field['label'] ),
			wp_kses_post( $tooltip ),
			wp_kses_post( $required ),
		);

		$input = $label . PHP_EOL . $input;
	}

	if ( ! empty( $input ) && ! empty( $field['desc'] ) ) {
		$input .= sprintf( '<span class="eac-form-field__help">%s</span>', wp_kses_post( $field['desc'] ) );
	}

	if ( ! empty( $input ) ) {
		printf(
			'<div class="eac-form-field eac-form-field-%1$s %2$s" id="eac-form-field-%3$s" style="%4$s">%5$s</div>',
			esc_attr( $field['type'] ),
			esc_attr( $field['wrapper_class'] ),
			esc_attr( $field['id'] ),
			esc_attr( $field['wrapper_style'] ),
			$input // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped.
		);
	}
}

/**
 * Form input field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.2.0
 * @return void
 */
function eac_input_field( $field ) {
	$defaults = array(
		'type'    => 'text',
		'name'    => '',
		'id'      => '',
		'class'   => '',
		'style'   => '',
		'default' => '',
	);

	$field = wp_parse_args( $field, $defaults );

	$field['name']  = empty( $field['name'] ) ? $field['id'] : $field['name'];
	$field['id']    = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value'] = empty( $field['value'] ) ? $field['default'] : $field['value'];
	$field['class'] = array_filter( array_unique( wp_parse_list( $field['class'] ) ) );
	$field['class'] = array_map( 'sanitize_html_class', $field['class'] );
	$field['class'] = implode( ' ', $field['class'] );

	$attrs = array();
	foreach ( $field as $k => $v ) {
		if ( empty( $k ) || empty( $v ) ) {
			continue;
		}

		if ( is_array( $v ) || is_object( $v ) ) {
			$v = wp_json_encode( $v, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP );
		}

		if ( strpos( $k, 'attr-' ) === 0 ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( str_replace( 'attr-', '', $k ) ), esc_attr( $v ) );
		} elseif ( strpos( $k, 'data-' ) === 0 ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( $k ), esc_attr( $v ) );
		} elseif ( in_array( $k, array( 'maxlength', 'pattern', 'readonly', 'disabled', 'required', 'autofocus' ), true ) ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( $k ), esc_attr( $k ) );
		}
	}

	switch ( $field['type'] ) {
		case 'text':
		case 'email':
		case 'number':
		case 'password':
		case 'hidden':
		case 'url':
			$field['class'] .= ' eac-input-field eac-input-field--' . $field['type'];
			?>
			<input
				type="<?php echo esc_attr( $field['type'] ); ?>"
				name="<?php echo esc_attr( $field['name'] ); ?>"
				id="<?php echo esc_attr( $field['id'] ); ?>"
				class="<?php echo esc_attr( $field['class'] ); ?>"
				value="<?php echo esc_attr( $field['value'] ); ?>"
				placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
				style="<?php echo esc_attr( $field['style'] ); ?>"
				<?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>
			>
			<?php

			break;
		case 'select':
			$field['value']       = wp_parse_list( $field['value'] );
			$field['value']       = array_map( 'strval', $field['value'] );
			$field['placeholder'] = ! empty( $field['placeholder'] ) ? $field['placeholder'] : '';
			if ( ! empty( $field['multiple'] ) ) {
				$field['name'] .= '[]';
				$attrs[]        = 'multiple="multiple"';
			}
			// It may send an option_key and option_value to use in the options.
			// if its an object, it will use the object properties.
			if ( ! empty( $field['option_value'] ) && ! empty( $field['option_label'] ) ) {
				// verify options is an array otherwise we will make it an array.
				if ( ! is_array( $field['options'] ) ) {
					$field['options'] = array();
				}
				$field['options'] = array_filter( $field['options'] );
				$field['options'] = wp_list_pluck( $field['options'], $field['option_label'], $field['option_value'] );
			}
			?>
			<select
				name="<?php echo esc_attr( $field['name'] ); ?>"
				id="<?php echo esc_attr( $field['id'] ); ?>"
				class="eac-input-field eac-input-field--select <?php echo esc_attr( $field['class'] ); ?>"
				placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
				style="<?php echo esc_attr( $field['style'] ); ?>"
				<?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>
			>
				<?php if ( ! empty( $field['placeholder'] ) ) : ?>
					<option value=""><?php echo esc_html( $field['placeholder'] ); ?></option>
				<?php endif; ?>
				<?php foreach ( $field['options'] as $key => $value ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>"<?php selected( in_array( (string) $key, $field['value'], true ), true ); ?>><?php echo esc_html( $value ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php

			break;

		case 'date':
			?>
			<input
				type="text"
				name="<?php echo esc_attr( $field['name'] ); ?>"
				id="<?php echo esc_attr( $field['id'] ); ?>"
				class="eac-input-field eac-input-field--date <?php echo esc_attr( $field['class'] ); ?>"
				value="<?php echo esc_attr( $field['value'] ); ?>"
				placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
				style="<?php echo esc_attr( $field['style'] ); ?>"
				<?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>
			>
			<?php

			break;

		case 'textarea':
			$rows = ! empty( $field['rows'] ) ? absint( $field['rows'] ) : 4;
			$cols = ! empty( $field['cols'] ) ? absint( $field['cols'] ) : 50;
			?>
			<textarea
				name="<?php echo esc_attr( $field['name'] ); ?>"
				id="<?php echo esc_attr( $field['id'] ); ?>"
				class="eac-input-field eac-input-field--textarea <?php echo esc_attr( $field['class'] ); ?>"
				placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
				rows="<?php echo esc_attr( $rows ); ?>"
				cols="<?php echo esc_attr( $cols ); ?>"
				style="<?php echo esc_attr( $field['style'] ); ?>"
				<?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>
			><?php echo esc_textarea( $field['value'] ); ?></textarea>
			<?php

			break;

		case 'switch':
			?>
			<input
				type="checkbox"
				name="<?php echo esc_attr( $field['name'] ); ?>"
				id="<?php echo esc_attr( $field['id'] ); ?>"
				class="eac-input-field eac-input-field--switch <?php echo esc_attr( $field['class'] ); ?>"
				value="1"
				<?php checked( $field['value'], 1 ); ?>
				<?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>
			>
			<?php

			break;

		case 'wp_editor':
			wp_editor(
				$field['value'],
				$field['id'],
				array(
					'textarea_name' => $field['name'],
					'textarea_rows' => 10,
				)
			);
			break;
	}
}

/**
 * Form upload field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.2.0
 * @return void
 */
function eac_upload_field( $field ) {
	$defaults = array(
		'name'       => '',
		'id'         => '',
		'value'      => '',
		'mime_types' => '',
		'class'      => '',
	);

	$field = wp_parse_args( $field, $defaults );

	$file = array(
		'icon'     => '',
		'title'    => '',
		'url'      => '',
		'filename' => '',
		'filesize' => '',
	);

	if ( $field['value'] ) {
		$post = get_post( $field['value'] );
		if ( $post && 'attachment' === $post->post_type ) {

			// has value.
			$field['class'] .= ' has-value';

			// update.
			$file['icon']     = $post['icon'];
			$file['title']    = $post['title'];
			$file['url']      = $post['url'];
			$file['filename'] = $post['filename'];
			if ( $post['filesize'] ) {
				$file['filesize'] = size_format( $post['filesize'] );
			}
		}
	}

	// wrapper.
	printf(
		'<div class="eac-upload-field %1$s" id="eac-upload-field-%2$s" data-mime-types="%3$s">',
		esc_attr( $field['class'] ),
		esc_attr( $field['id'] ),
		esc_attr( $field['mime_types'] )
	);
	printf(
		'<input type="hidden" name="%1$s" id="%2$s" value="%3$s">',
		esc_attr( $field['name'] ),
		esc_attr( $field['id'] ),
		esc_attr( $field['value'] )
	);
}
