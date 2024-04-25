<?php

defined( 'ABSPATH' ) || exit();

/**
 * Form field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.2.0
 * @return void
 */
function eac_form_group( $field ) {
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
	$field = apply_filters( 'ever_accounting_form_group_args', $field );

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
	foreach ( $field as $attr_key => $attr_value ) {
		if ( empty( $attr_key ) || empty( $attr_value ) ) {
			continue;
		}
		if ( strpos( $attr_key, 'attr-' ) === 0 ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( str_replace( 'attr-', '', $attr_key ) ), esc_attr( $attr_value ) );
		} elseif ( strpos( $attr_key, 'data-' ) === 0 ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( $attr_key ), esc_attr( $attr_value ) );
		} elseif ( in_array( $attr_key, array( 'readonly', 'disabled', 'required', 'autofocus' ), true ) ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( $attr_key ), esc_attr( $attr_value ) );
		}
	}

	// Prefix.
	if ( ! empty( $field['prefix'] ) && ! preg_match( '/<[^>]+>/', $field['prefix'] ) ) {
		$prefix          = is_callable( $field['prefix'] ) ? call_user_func( $field['suffix'], $field ) : $field['prefix'];
		$field['prefix'] = '<span class="addon eac-form-field__addon">' . $prefix . '</span>';
	}

	// Suffix.
	if ( ! empty( $field['suffix'] ) && ! preg_match( '/<[^>]+>/', $field['suffix'] ) ) {
		$suffix          = is_callable( $field['suffix'] ) ? call_user_func( $field['suffix'], $field ) : $field['suffix'];
		$field['suffix'] = '<span class="addon eac-form-field__addon">' . $suffix . '</span>';
	}

	switch ( $field['type'] ) {
		case 'text':
		case 'email':
		case 'number':
		case 'password':
		case 'hidden':
		case 'url':
			$input = sprintf(
				'<input type="%1$s" name="%2$s" id="%3$s" class="%4$s" value="%5$s" placeholder="%6$s" style="%7$s" %8$s>',
				esc_attr( $field['type'] ),
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['value'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);

			break;
		case 'select':
			$field['value']       = wp_parse_list( $field['value'] );
			$field['value']       = array_map( 'strval', $field['value'] );
			$field['placeholder'] = ! empty( $field['placeholder'] ) ? $field['placeholder'] : '';
			if ( ! empty( $field['multiple'] ) ) {
				$field['name'] .= '[]';
				$attrs[]        = 'multiple="multiple"';
			}
			$input = sprintf(
				'<select name="%1$s" id="%2$s" class="%3$s" placeholder="%4$s" style="%5$s" %6$s>',
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

			break;

		case 'textarea':
			$rows  = ! empty( $field['rows'] ) ? absint( $field['rows'] ) : 4;
			$cols  = ! empty( $field['cols'] ) ? absint( $field['cols'] ) : 50;
			$input = sprintf(
				'<textarea name="%1$s" id="%2$s" class="%3$s" placeholder="%4$s" rows="%5$s" cols="%6$s" style="%7$s" %8$s>%9$s</textarea>',
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

		case 'checkbox':
			$field['value'] = ! empty( $field['value'] ) ? $field['value'] : '1';
			$input          = sprintf(
				'<label><input type="checkbox" name="%1$s" id="%2$s" class="%3$s" value="%4$s" %5$s %6$s>%7$s</label>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['value'] ),
				checked( $field['value'], '1', false ),
				wp_kses_post( implode( ' ', $attrs ) ),
				wp_kses_post( $field['label'] )
			);

			break;

		case 'radio':
		case 'checkboxes':
			$field['value'] = wp_parse_list( $field['value'] );
			$input          = '';
			if ( ! empty( $field['options'] ) ) {
				$input .= '<fieldset>';
				foreach ( $field['options'] as $key => $value ) {
					$input .= sprintf(
						'<label><input type="%1$s" name="%2$s" id="%3$s_%4$s" class="%5$s" value="%6$s" %7$s %8$s>%9$s</label>',
						esc_attr( $field['type'] ),
						esc_attr( $field['name'] ),
						esc_attr( $field['id'] ),
						esc_attr( $key ),
						esc_attr( $field['class'] ),
						esc_attr( $key ),
						checked( in_array( (string) $key, $field['value'], true ), true, false ),
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $value )
					);
				}

				$input .= '</fieldset>';
			}
			break;

		case 'date':
			$input = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="%3$s" value="%4$s" placeholder="%5$s" style="%6$s" %7$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['value'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);

			break;

		case 'file':
			$accept = ! empty( $field['accept'] ) ? $field['accept'] : 'image/*';
			$input  = sprintf(
				'<input type="file" name="%1$s" id="%2$s" class="%3$s" value="%4$s" placeholder="%5$s" style="%6$s" %7$s accept="%8$s">',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['value'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $attrs ) ),
				esc_attr( $accept )
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
		case 'callback':
		default:
			$input = isset( $field['callback'] ) && is_callable( $field['callback'] ) ? call_user_func( $field['callback'], $field ) : '';
			break;
	}

	if ( ! empty( $field['prefix'] ) || ! empty( $field['suffix'] ) && ! empty( $input ) ) {
		$input = sprintf(
			'<div class="bkit-input-group eac-form-field__group">%s%s%s</div>',
			$field['prefix'],
			$input,
			$field['suffix']
		);
	}

	if ( ! empty( $input ) ) {
		if ( ! empty( $field['label'] ) ) {
			$label = '<label for="' . esc_attr( $field['id'] ) . '">' . esc_html( $field['label'] );
			if ( true === $field['required'] ) {
				$label .= '&nbsp;<abbr title="' . esc_attr__( 'required', 'wp-ever-accounting' ) . '">	</abbr>';
			}
			if ( ! empty( $field['tooltip'] ) ) {
				$label .= eac_tooltip( $field['tooltip'] );
			}
			$label .= '</label>';
			$input  = $label . $input;
		}

		if ( ! empty( $field['desc'] ) && ! in_array( $field['type'], array( 'checkbox', 'switch' ), true ) ) {
			$input .= '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
		}

		$input = sprintf(
			'<div class="bkit-form-group eac-form-group eac-form-group-%1$s %2$s" id="eac-form-group-%3$s" style="%4$s">%5$s</div>',
			esc_attr( $field['type'] ),
			esc_attr( $field['wrapper_class'] ),
			esc_attr( $field['id'] ),
			esc_attr( $field['wrapper_style'] ),
			$input
		);
	}

	echo $input; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in the above code.
}
