<?php
/**
 * EverAccounting Form Functions
 *
 * General form functions available on both the front-end and admin.
 *
 * @package EverAccounting\Functions
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
		$tip = eaccounting_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="eaccounting-help-tip" data-tip="' . $tip . '"></span>';
}

/**
 * Output a hidden input box.
 *
 * @param array $field
 *
 * @since 1.0.2
 */
function eaccounting_hidden_input( $field ) {
	$field['value'] = isset( $field['value'] ) ? $field['value'] : '';
	$field['class'] = isset( $field['class'] ) ? $field['class'] : '';
	$field['id']    = empty( $field['id'] ) ? $field['name'] : $field['id'];

	echo '<input type="hidden" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" /> ';
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
			'label'         => '',
			'class'         => 'short',
			'style'         => '',
			'wrapper_class' => '',
			'default'       => '',
			'value'         => '',
			'name'          => '',
			'placeholder'   => '',
			'type'          => 'text',
			'data_type'     => '',
			'after'         => '',
			'tooltip'       => '',
			'desc'          => '',
			'required'      => false,
			'disabled'      => false,
			'readonly'      => false,
			'attr'          => array(),
		)
	);

	$field['id']               = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value']            = ! isset( $field['value'] ) ? $field['default'] : $field['value'];
	$field['attr']['required'] = ( true == $field['required'] ) ? ' required ' : '';
	$field['attr']['readonly'] = ( true == $field['readonly'] ) ? ' readonly ' : '';
	$field['attr']['disabled'] = ( true == $field['disabled'] ) ? ' disabled ' : '';
	$field['wrapper_class']    .= ( true == $field['required'] ) ? ' required ' : '';
	$data_type                 = empty( $field['data_type'] ) ? '' : $field['data_type'];

	switch ( $data_type ) {
		case 'price':
			$field['class'] .= ' ea-input-price';
			break;
		case 'decimal':
			$field['class'] .= ' ea-input-decimal';
			break;
		case 'date':
			$field['class'] .= ' ea-input-date';
			break;
		case 'color':
			$field['class']         .= ' ea-input-color';
			$field['wrapper_class'] .= ' ea-color-field';
			break;
		case 'url':
			$field['class'] .= ' ea-input-url';
			$field['value'] = esc_url( $field['value'] );
			break;
		default:
			break;
	}

	// Custom attribute handling
	$attributes = eaccounting_implode_html_attributes( $field['attr'] );
	$tooltip    = ! empty( $field['tooltip'] ) ? eaccounting_help_tip( $field['tooltip'] ) : '';
	$desc       = ! empty( $field['desc'] ) ? sprintf( '<span class="desc">%s</span>', wp_kses_post( $field['desc'] ) ) : '';

	echo sprintf( '<div class="ea-form-field %s_field %s"><label class="ea-label" for="%s">%s</label>%s',
		esc_attr( $field['id'] ),
		esc_attr( $field['wrapper_class'] ),
		esc_attr( $field['id'] ),
		wp_kses_post( $field['label'] ),
		$tooltip
	);

	if ( $field['data_type'] == 'color' ) {
		echo sprintf( '<span class="colorpickpreview" style="background: %s">&nbsp;</span>', $field['value'] );
	}

	echo sprintf( '<input type="%s" class="ea-input-control %s" style="%s" name="%s" id="%s" value="%s" placeholder="%s" %s/>',
		esc_attr( $field['type'] ),
		esc_attr( $field['class'] ),
		esc_attr( $field['style'] ),
		esc_attr( $field['name'] ),
		esc_attr( $field['id'] ),
		esc_attr( $field['value'] ),
		esc_attr( $field['placeholder'] ),
		$attributes
	);

	echo $desc;

	if ( $field['data_type'] == 'color' ) {
		echo sprintf( '<div id="colorPickerDiv_%s" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>', $field['id'] );
	}

	echo '</div>';
}

/**
 * Output a text input box.
 *
 * @param array $field
 *
 * @since 1.0.2
 */
function eaccounting_textarea( $field ) {
	$field                     = (array) wp_parse_args(
		$field, array(
			'label'         => '',
			'class'         => 'short',
			'style'         => '',
			'wrapper_class' => '',
			'default'       => '',
			'value'         => '',
			'name'          => '',
			'placeholder'   => '',
			'rows'          => 2,
			'cols'          => 20,
			'tooltip'       => '',
			'desc'          => '',
			'required'      => false,
			'disabled'      => false,
			'readonly'      => false,
			'attr'          => array(),
		)
	);
	$field['id']               = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value']            = empty( $field['value'] ) ? $field['default'] : $field['value'];
	$field['attr']['required'] = ( true == $field['required'] ) ? ' required ' : '';
	$field['attr']['readonly'] = ( true == $field['readonly'] ) ? ' readonly ' : '';
	$field['attr']['rows']     = $field['rows'];
	$field['attr']['cols']     = $field['cols'];
	$field['wrapper_class']    .= ( true == $field['required'] ) ? ' required ' : '';

	// Custom attribute handling
	$attributes = eaccounting_implode_html_attributes( $field['attr'] );
	$tooltip    = ! empty( $field['tooltip'] ) ? eaccounting_help_tip( $field['tooltip'] ) : '';
	$desc       = ! empty( $field['desc'] ) ? sprintf( '<span class="desc">%s</span>', wp_kses_post( $field['desc'] ) ) : '';

	echo sprintf( '<div class="ea-form-field %s_field %s"><label class="ea-label" for="%s">%s</label>%s',
		esc_attr( $field['id'] ),
		esc_attr( $field['wrapper_class'] ),
		esc_attr( $field['id'] ),
		wp_kses_post( $field['label'] ),
		$tooltip
	);

	echo sprintf( '<textarea class="ea-input-control %s" style="%s" name="%s" id="%s" placeholder="%s" %s>%s</textarea>',
		esc_attr( $field['class'] ),
		esc_attr( $field['style'] ),
		esc_attr( $field['name'] ),
		esc_attr( $field['id'] ),
		esc_attr( $field['placeholder'] ),
		$attributes,
		esc_attr( $field['value'] )
	);

	echo $desc;

	echo '</div>';

}


/**
 * Output a radio input box.
 *
 * @param array $field
 *
 * @since 1.0.2
 */
function eaccounting_wp_radio( $field ) {
	$field = (array) wp_parse_args(
		$field, array(
			'label'         => '',
			'class'         => '',
			'style'         => '',
			'wrapper_class' => '',
			'default'       => '',
			'value'         => '',
			'name'          => '',
			'tooltip'       => '',
			'desc'          => '',
			'options'       => array(),
			'attr'          => array(),
		)
	);

	$field['id']    = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value'] = empty( $field['value'] ) ? $field['default'] : $field['value'];

	// Custom attribute handling
	$attributes = eaccounting_implode_html_attributes( $field['attr'] );
	$tooltip    = ! empty( $field['tooltip'] ) ? eaccounting_help_tip( $field['tooltip'] ) : '';
	$desc       = ! empty( $field['desc'] ) ? sprintf( '<span class="desc">%s</span>', wp_kses_post( $field['desc'] ) ) : '';

	echo sprintf( '<div class="ea-form-field %s_field %s"><label class="ea-label" for="%s">%s</label>%s',
		esc_attr( $field['id'] ),
		esc_attr( $field['wrapper_class'] ),
		esc_attr( $field['id'] ),
		wp_kses_post( $field['label'] ),
		$tooltip
	);

	echo '<ul class="ea-radios">';

	foreach ( $field['options'] as $key => $value ) {
		echo sprintf( '<li><label><input type="radio" name="%s" value="%s" class="%s" style="%s" %s %s/>%s</label></li>',
			esc_attr( $field['name'] ),
			esc_attr( $key ),
			esc_attr( $field['class'] ),
			esc_attr( $field['style'] ),
			esc_html( $value ),
			$attributes,
			checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) );
	}

	echo '</ul>';

	echo $desc;

	echo '</fieldset>';
}


/**
 * Output a checkbox input box.
 *
 * @param array $field
 *
 * @since 1.0.2
 */
function eaccounting_wp_checkbox( $field ) {
	$field = (array) wp_parse_args(
		$field, array(
			'label'         => '',
			'class'         => '',
			'style'         => '',
			'wrapper_class' => '',
			'default'       => '',
			'cbvalue'       => 'yes',
			'value'         => '',
			'name'          => '',
			'tooltip'       => '',
			'desc'          => '',
			'attr'          => array(),
		)
	);

	$field['id']    = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value'] = empty( $field['value'] ) ? $field['default'] : $field['value'];

	// Custom attribute handling
	$attributes = eaccounting_implode_html_attributes( $field['attr'] );
	$tooltip    = ! empty( $field['tooltip'] ) ? eaccounting_help_tip( $field['tooltip'] ) : '';
	$desc       = ! empty( $field['desc'] ) ? sprintf( '<span class="desc">%s</span>', wp_kses_post( $field['desc'] ) ) : '';

	echo sprintf( '<div class="ea-form-field %s_field %s"><label class="ea-label" for="%s">%s</label>%s',
		esc_attr( $field['id'] ),
		esc_attr( $field['wrapper_class'] ),
		esc_attr( $field['id'] ),
		wp_kses_post( $field['label'] ),
		$tooltip
	);

	echo sprintf( '<input type="checkbox" class="%s" style="%s" name="%s" id="%s" value="%s" %s %s/>',
		esc_attr( $field['class'] ),
		esc_attr( $field['style'] ),
		esc_attr( $field['name'] ),
		esc_attr( $field['id'] ),
		esc_attr( $field['cbvalue'] ),
		$attributes,
		checked( $field['value'], $field['cbvalue'], false )
	);

	echo $desc;

	echo '</div>';
}


/**
 * Output a select input box.
 *
 * @param array $field Data about the field to render.
 *
 * @since 1.0.2
 */
function eaccounting_select( $field ) {
	$field = (array) wp_parse_args(
		$field, array(
			'label'         => '',
			'class'         => '',
			'style'         => '',
			'wrapper_class' => '',
			'default'       => '',
			'value'         => '',
			'name'          => '',
			'options'       => array(),
			'multiple'      => false,
			'tooltip'       => '',
			'desc'          => '',
			'required'      => false,
			'disabled'      => false,
			'readonly'      => false,
			'attr'          => array(),
		)
	);

	$field['id']               = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value']            = empty( $field['value'] ) ? $field['default'] : $field['value'];
	$field['wrapper_class']    .= ( true == $field['required'] ) ? ' required ' : '';
	$field['attr']['required'] = ( true == $field['required'] ) ? ' required ' : '';
	$field['attr']['readonly'] = ( true == $field['readonly'] ) ? ' readonly ' : '';
	$field['attr']['disabled'] = ( true == $field['disabled'] ) ? ' disabled ' : '';
	$field['attr']['multiple'] = ( true == $field['multiple'] ) ? ' multiple ' : '';

	// Custom attribute handling
	$attributes = eaccounting_implode_html_attributes( $field['attr'] );
	$tooltip    = ! empty( $field['tooltip'] ) ? eaccounting_help_tip( $field['tooltip'] ) : '';
	$desc       = ! empty( $field['desc'] ) ? sprintf( '<span class="desc">%s</span>', wp_kses_post( $field['desc'] ) ) : '';

	echo sprintf( '<div class="ea-form-field %s_field %s"><label class="ea-label" for="%s">%s</label>%s',
		esc_attr( $field['id'] ),
		esc_attr( $field['wrapper_class'] ),
		esc_attr( $field['id'] ),
		wp_kses_post( $field['label'] ),
		$tooltip
	);

	echo sprintf( '<select class="ea-input-control select %s" style="%s" name="%s" id="%s" %s>',
		esc_attr( $field['class'] ),
		esc_attr( $field['style'] ),
		esc_attr( $field['name'] ),
		esc_attr( $field['id'] ),
		$attributes );

	foreach ( $field['options'] as $key => $value ) {
		echo sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), eaccounting_selected( esc_attr( $key ), esc_attr( $field['value'] ) ), esc_html( $value ) );
	}

	echo '</select>';

	echo $desc;

	echo '</div>';
}


function eaccounting_toggle( $field ) {
	$field = (array) wp_parse_args(
		$field, array(
			'label'         => '',
			'class'         => '',
			'style'         => '',
			'wrapper_class' => '',
			'default'       => '',
			'value'         => '',
			'name'          => '',
			'cbvalue'       => '1',
			'options'       => array(),
			'multiple'      => false,
			'naked'         => false,
			'tooltip'       => '',
			'desc'          => '',
			'required'      => false,
			'disabled'      => false,
			'readonly'      => false,
			'attr'          => array(),
		)
	);

	$field['id']               = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value']            = empty( $field['value'] ) ? $field['default'] : $field['value'];
	$field['attr']['readonly'] = ( true == $field['readonly'] ) ? ' readonly ' : '';
	$field['attr']['disabled'] = ( true == $field['disabled'] ) ? ' disabled ' : '';

	// Custom attribute handling
	$attributes = eaccounting_implode_html_attributes( $field['attr'] );
	$tooltip    = ! empty( $field['tooltip'] ) ? eaccounting_help_tip( $field['tooltip'] ) : '';
	$desc       = ! empty( $field['desc'] ) ? sprintf( '<span class="desc">%s</span>', wp_kses_post( $field['desc'] ) ) : '';

	if ( ! $field['naked'] ) {
		echo sprintf( '<div class="ea-form-field %s_field %s"><label class="ea-label" for="%s">%s</label>%s',
			esc_attr( $field['id'] ),
			esc_attr( $field['wrapper_class'] ),
			esc_attr( $field['id'] ),
			wp_kses_post( $field['label'] ),
			$tooltip
		);
	}

	echo sprintf( '<label class="ea-toggle"><input type="checkbox" name="%s" id="%s" class="%s"  style="%s" value="%s" %s %s><span data-label-off="%s" data-label-on="%s" class="ea-toggle-slider"></span></label>',
		esc_attr( $field['name'] ),
		esc_attr( $field['id'] ),
		esc_attr( $field['class'] ),
		esc_attr( $field['style'] ),
		esc_attr( $field['cbvalue'] ),
		$attributes,
		checked( $field['value'], $field['cbvalue'], false ),
		__( 'No', 'wp-ever-accounting' ),
		__( 'Yes', 'wp-ever-accounting' )
	);
	if ( ! $field['naked'] ) {
		echo $desc;

		echo '</div>';
	}

}

/**
 * Convert database result to option list.
 *
 * @param array $list database query result.
 * @param string $key Key of the option.
 * @param string $value Value of the option.
 *
 * @return array
 * @since 1.0.2
 *
 */
function eaccounting_result_to_dropdown( $list, $key = 'id', $value = 'name' ) {
	if ( ! is_array( $list ) || empty( $list ) || count( $list ) < 0 ) {
		return array();
	}

	return wp_list_pluck( $list, $value, $key );
}
