<?php
/**
 * EverAccounting Form Fields Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @package EverAccounting\Admin\Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit();

/**
 * Return the html selected attribute if stringfied $value is found in array of stringified $options
 * or if stringified $value is the same as scalar stringified $options.
 *
 * @param string|int $value Value to find within options.
 * @param string|int|array $options Options to go through when looking for value.
 *
 * @return string
 * @since 1.0.2
 */
function eaccounting_selected( $value, $options ) {
	if ( is_array( $options ) ) {
		$options = array_map( 'strval', $options );

		return selected( in_array( (string) $value, $options, true ), true, false );
	}

	return selected( $value, $options, false );
}


/**
 * Display help tip.
 *
 * @param string $tip Help tip text.
 * @param bool $allow_html Allow sanitized HTML if true or escape.
 *
 * @return string
 * @since  1.0.2
 *
 */
function eaccounting_help_tip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = wc_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="eaccounting-help-tip" data-tip="' . $tip . '"></span>';
}

/**
 * Output a text input box.
 *
 * @param array $field
 *
 * @since 1.0.2
 */
function eaccounting_text_input( $field = array() ) {
	$field = (array) wp_parse_args(
			$field, array(
					'class'         => 'short',
					'style'         => '',
					'wrapper_class' => '',
					'default'       => '',
					'value'         => '',
					'name'          => '',
					'placeholder'   => '',
					'type'          => 'text',
					'data_type'     => '',
					'tooltip'       => false,
					'attributes'    => array(),
			)
	);

	$field['id']    = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value'] = empty( $field['value'] ) ? $field['default'] : $field['value'];
	$data_type      = empty( $field['data_type'] ) ? '' : $field['data_type'];

	switch ( $data_type ) {
		case 'price':
			$field['class'] .= ' eaccounting_input_price';
			break;
		case 'decimal':
			$field['class'] .= ' eaccounting_input_decimal';
			break;
		case 'url':
			$field['class'] .= ' eaccounting_input_url';
			$field['value'] = esc_url( $field['value'] );
			break;
		default:
			break;
	}

	// Custom attribute handling
	$attributes = eaccounting_implode_html_attributes( $field['attributes'] );

	echo sprintf( '<div class="ea-form-field %s_field %s"><label for="%s">%s</label>',
			esc_attr( $field['id'] ),
			esc_attr( $field['wrapper_class'] ),
			esc_attr( $field['id'] ),
			wp_kses_post( $field['label'] )
	);

	if ( ! empty( $field['description'] ) && false !== $field['tooltip'] ) {
		echo eaccounting_help_tip( $field['description'] );
	}

	echo sprintf( '<input type="%s" class="%s" style="%s" name="%s" id="%s" value="%s" placeholder="%s" %s/>',
			esc_attr( $field['type'] ),
			esc_attr( $field['class'] ),
			esc_attr( $field['style'] ),
			esc_attr( $field['name'] ),
			esc_attr( $field['id'] ),
			esc_attr( $field['value'] ),
			esc_attr( $field['placeholder'] ),
			$attributes
	);

	if ( ! empty( $field['description'] ) && false === $field['tooltip'] ) {
		echo sprintf( '<span class="description">%s</span>', wp_kses_post( $field['description'] ) );
	}

	echo '</div>';
}


/**
 * Output a hidden input box.
 *
 * @param array $field
 */
function eaccounting_hidden_input( $field ) {
	$field['value'] = isset( $field['value'] ) ? $field['value'] : '';
	$field['class'] = isset( $field['class'] ) ? $field['class'] : '';

	echo '<input type="hidden" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" /> ';
}

/**
 * Output a textarea input box.
 *
 * @param array $field
 */
function eaccounting_textarea_input( $field ) {
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : '';
	$field['tooltip']       = isset( $field['tooltip'] ) ? $field['tooltip'] : false;
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['rows']          = isset( $field['rows'] ) ? $field['rows'] : 2;
	$field['cols']          = isset( $field['cols'] ) ? $field['cols'] : 20;

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	if ( ! empty( $field['description'] ) && false !== $field['tooltip'] ) {
		echo wc_help_tip( $field['description'] );
	}

	echo '<textarea class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '"  name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" rows="' . esc_attr( $field['rows'] ) . '" cols="' . esc_attr( $field['cols'] ) . '" ' . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $field['value'] ) . '</textarea> ';

	if ( ! empty( $field['description'] ) && false === $field['tooltip'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	echo '</p>';
}

/**
 * Output a checkbox input box.
 *
 * @param array $field
 */
function eaccounting_wp_checkbox( $field ) {

	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'checkbox';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : '';
	$field['cbvalue']       = isset( $field['cbvalue'] ) ? $field['cbvalue'] : 'yes';
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['tooltip']       = isset( $field['tooltip'] ) ? $field['tooltip'] : false;

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	if ( ! empty( $field['description'] ) && false !== $field['tooltip'] ) {
		echo wc_help_tip( $field['description'] );
	}

	echo '<input type="checkbox" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['cbvalue'] ) . '" ' . checked( $field['value'], $field['cbvalue'], false ) . '  ' . implode( ' ', $custom_attributes ) . '/> ';

	if ( ! empty( $field['description'] ) && false === $field['tooltip'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	echo '</p>';
}

/**
 * Output a select input box.
 *
 * @param array $field Data about the field to render.
 */
function eaccounting_wp_select( $field ) {

	$field = wp_parse_args(
			$field, array(
					'class'             => 'select short',
					'style'             => '',
					'wrapper_class'     => '',
					'value'             => '',
					'name'              => $field['id'],
					'tooltip'           => false,
					'custom_attributes' => array(),
			)
	);

	$wrapper_attributes = array(
			'class' => $field['wrapper_class'] . " form-field {$field['id']}_field",
	);

	$label_attributes = array(
			'for' => $field['id'],
	);

	$field_attributes          = (array) $field['custom_attributes'];
	$field_attributes['style'] = $field['style'];
	$field_attributes['id']    = $field['id'];
	$field_attributes['name']  = $field['name'];
	$field_attributes['class'] = $field['class'];

	$tooltip     = ! empty( $field['description'] ) && false !== $field['tooltip'] ? $field['description'] : '';
	$description = ! empty( $field['description'] ) && false === $field['tooltip'] ? $field['description'] : '';
	?>
	<p <?php echo wc_implode_html_attributes( $wrapper_attributes ); // WPCS: XSS ok. ?>>
		<label <?php echo wc_implode_html_attributes( $label_attributes ); // WPCS: XSS ok. ?>><?php echo wp_kses_post( $field['label'] ); ?></label>
		<?php if ( $tooltip ) : ?>
			<?php echo wc_help_tip( $tooltip ); // WPCS: XSS ok. ?>
		<?php endif; ?>
		<select <?php echo wc_implode_html_attributes( $field_attributes ); // WPCS: XSS ok. ?>>
			<?php
			foreach ( $field['options'] as $key => $value ) {
				echo '<option value="' . esc_attr( $key ) . '"' . wc_selected( $key, $field['value'] ) . '>' . esc_html( $value ) . '</option>';
			}
			?>
		</select>
		<?php if ( $description ) : ?>
			<span class="description"><?php echo wp_kses_post( $description ); ?></span>
		<?php endif; ?>
	</p>
	<?php
}

/**
 * Output a radio input box.
 *
 * @param array $field
 */
function eaccounting_wp_radio( $field ) {
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : '';
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['tooltip']       = isset( $field['tooltip'] ) ? $field['tooltip'] : false;

	echo '<fieldset class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><legend>' . wp_kses_post( $field['label'] ) . '</legend>';

	if ( ! empty( $field['description'] ) && false !== $field['tooltip'] ) {
		echo wc_help_tip( $field['description'] );
	}

	echo '<ul class="wc-radios">';

	foreach ( $field['options'] as $key => $value ) {

		echo '<li><label><input
				name="' . esc_attr( $field['name'] ) . '"
				value="' . esc_attr( $key ) . '"
				type="radio"
				class="' . esc_attr( $field['class'] ) . '"
				style="' . esc_attr( $field['style'] ) . '"
				' . checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '
				/> ' . esc_html( $value ) . '</label>
		</li>';
	}
	echo '</ul>';

	if ( ! empty( $field['description'] ) && false === $field['tooltip'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	echo '</fieldset>';
}

