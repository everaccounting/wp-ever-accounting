<?php
/**
 * EverAccounting Form Functions
 *
 * General form functions available on both the front-end and admin.
 *
 * @since   1.0.2
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit();

/**
 * Return the html selected attribute if stringfied $value is found in array of stringified $options
 * or if stringified $value is the same as scalar stringified $options.
 *
 * @since 1.0.2
 *
 * @param string|int|array $options Options to go through when looking for value.
 *
 * @param string|int       $value   Value to find within options.
 *
 * @return string
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
 * @since  1.0.2
 *
 * @param bool   $allow_html Allow sanitized HTML if true or escape.
 *
 * @param string $tip        Help tip text.
 *
 * @return string
 */
function eaccounting_help_tip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = eaccounting_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="ea-help-tip" data-tip="' . $tip . '"></span>';
}

/**
 * Output a hidden input box.
 *
 * @since 1.0.2
 *
 * @param array $field
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
 * @since 1.0.2
 *
 * @param array $field
 */
function eaccounting_text_input( $field = array() ) {
	$field = (array) wp_parse_args(
		$field,
		array(
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
	$field['wrapper_class']   .= ( true == $field['required'] ) ? ' required ' : '';
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
			$field['value']  = esc_url( $field['value'] );
			break;
		default:
			break;
	}

	// Custom attribute handling
	$attributes = eaccounting_implode_html_attributes( $field['attr'] );
	$tooltip    = ! empty( $field['tooltip'] ) ? eaccounting_help_tip( $field['tooltip'] ) : '';
	$desc       = ! empty( $field['desc'] ) ? sprintf( '<span class="desc">%s</span>', wp_kses_post( $field['desc'] ) ) : '';

	if ( ! empty( $field['label'] ) ) {
		echo sprintf(
			'<div class="ea-form-field %s_field %s"><label class="ea-label" for="%s">%s</label>%s',
			esc_attr( $field['id'] ),
			esc_attr( $field['wrapper_class'] ),
			esc_attr( $field['id'] ),
			wp_kses_post( $field['label'] ),
			$tooltip
		);
	}

	if ( $field['data_type'] == 'color' ) {
		echo sprintf( '<span class="colorpickpreview" style="background: %s">&nbsp;</span>', $field['value'] );
	}

	echo sprintf(
		'<input type="%s" class="ea-input-control %s" style="%s" name="%s" id="%s" value="%s" placeholder="%s" %s/>',
		esc_attr( $field['type'] ),
		esc_attr( $field['class'] ),
		esc_attr( $field['style'] ),
		esc_attr( $field['name'] ),
		esc_attr( $field['id'] ),
		esc_attr( $field['value'] ),
		esc_attr( $field['placeholder'] ),
		$attributes
	);
	if ( $field['data_type'] == 'color' ) {
		echo sprintf( '<div id="colorPickerDiv_%s" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>', $field['id'] );
	}
	if ( ! empty( $field['label'] ) ) {
		echo $desc;

		echo '</div>';
	}
}

/**
 * Output a text input box.
 *
 * @since 1.0.2
 *
 * @param array $field
 */
function eaccounting_textarea( $field ) {
	$field                     = (array) wp_parse_args(
		$field,
		array(
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
	$field['wrapper_class']   .= ( true == $field['required'] ) ? ' required ' : '';

	// Custom attribute handling
	$attributes = eaccounting_implode_html_attributes( $field['attr'] );
	$tooltip    = ! empty( $field['tooltip'] ) ? eaccounting_help_tip( $field['tooltip'] ) : '';
	$desc       = ! empty( $field['desc'] ) ? sprintf( '<span class="desc">%s</span>', wp_kses_post( $field['desc'] ) ) : '';

	echo sprintf(
		'<div class="ea-form-field %s_field %s"><label class="ea-label" for="%s">%s</label>%s',
		esc_attr( $field['id'] ),
		esc_attr( $field['wrapper_class'] ),
		esc_attr( $field['id'] ),
		wp_kses_post( $field['label'] ),
		$tooltip
	);

	echo sprintf(
		'<textarea class="ea-input-control %s" style="%s" name="%s" id="%s" placeholder="%s" %s>%s</textarea>',
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
 * @since 1.0.2
 *
 * @param array $field
 */
function eaccounting_wp_radio( $field ) {
	$field = (array) wp_parse_args(
		$field,
		array(
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

	echo sprintf(
		'<div class="ea-form-field %s_field %s"><label class="ea-label" for="%s">%s</label>%s',
		esc_attr( $field['id'] ),
		esc_attr( $field['wrapper_class'] ),
		esc_attr( $field['id'] ),
		wp_kses_post( $field['label'] ),
		$tooltip
	);

	echo '<ul class="ea-radios">';

	foreach ( $field['options'] as $key => $value ) {
		echo sprintf(
			'<li><label><input type="radio" name="%s" value="%s" class="%s" style="%s" %s %s/>%s</label></li>',
			esc_attr( $field['name'] ),
			esc_attr( $key ),
			esc_attr( $field['class'] ),
			esc_attr( $field['style'] ),
			esc_html( $value ),
			$attributes,
			checked( esc_attr( $field['value'] ), esc_attr( $key ), false )
		);
	}

	echo '</ul>';

	echo $desc;

	echo '</fieldset>';
}


/**
 * Output a checkbox input box.
 *
 * @since 1.0.2
 *
 * @param array $field
 */
function eaccounting_wp_checkbox( $field ) {
	$field = (array) wp_parse_args(
		$field,
		array(
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

	echo sprintf(
		'<div class="ea-form-field %s_field %s"><label class="ea-label" for="%s">%s</label>%s',
		esc_attr( $field['id'] ),
		esc_attr( $field['wrapper_class'] ),
		esc_attr( $field['id'] ),
		wp_kses_post( $field['label'] ),
		$tooltip
	);

	echo sprintf(
		'<input type="checkbox" class="%s" style="%s" name="%s" id="%s" value="%s" %s %s/>',
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
 * @since 1.0.2
 *
 * @param array $field Data about the field to render.
 */
function eaccounting_select( $field ) {
	$field = (array) wp_parse_args(
		$field,
		array(
			'label'         => '',
			'class'         => '',
			'style'         => '',
			'wrapper_class' => '',
			'default'       => '',
			'value'         => '',
			'name'          => '',
			'placeholder'   => '',
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

	$field['id']                  = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value']               = empty( $field['value'] ) ? $field['default'] : $field['value'];
	$field['wrapper_class']      .= ( true == $field['required'] ) ? ' required ' : '';
	$field['attr']['required']    = ( true == $field['required'] ) ? ' required ' : '';
	$field['attr']['readonly']    = ( true == $field['readonly'] ) ? ' readonly ' : '';
	$field['attr']['disabled']    = ( true == $field['disabled'] ) ? ' disabled ' : '';
	$field['attr']['multiple']    = ( true == $field['multiple'] ) ? ' multiple ' : '';
	$field['attr']['placeholder'] = $field['placeholder'];

	// Custom attribute handling
	$attributes = eaccounting_implode_html_attributes( $field['attr'] );
	$tooltip    = ! empty( $field['tooltip'] ) ? eaccounting_help_tip( $field['tooltip'] ) : '';
	$desc       = ! empty( $field['desc'] ) ? sprintf( '<span class="desc">%s</span>', wp_kses_post( $field['desc'] ) ) : '';
	if ( ! empty( $field['label'] ) ) {
		echo sprintf(
			'<div class="ea-form-field %s_field %s"><label class="ea-label" for="%s">%s</label>%s',
			esc_attr( $field['id'] ),
			esc_attr( $field['wrapper_class'] ),
			esc_attr( $field['id'] ),
			wp_kses_post( $field['label'] ),
			$tooltip
		);
	}

	echo sprintf(
		'<select class="ea-input-control select %s" style="%s" name="%s" id="%s" %s>',
		esc_attr( $field['class'] ),
		esc_attr( $field['style'] ),
		esc_attr( $field['name'] ),
		esc_attr( $field['id'] ),
		$attributes
	);
	foreach ( $field['options'] as $key => $value ) {
		echo sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), eaccounting_selected( esc_attr( $key ), esc_attr( $field['value'] ) ), esc_html( $value ) );
	}
	echo '</select>';

	if ( ! empty( $field['label'] ) ) {
		echo $desc;

		echo '</div>';
	}
}

/**
 * File input field.
 *
 * @since 1.0.2
 *
 * @param $field
 */
function eaccounting_file_input( $field ) {
	$field = (array) wp_parse_args(
		$field,
		array(
			'label'         => '',
			'class'         => 'short',
			'style'         => '',
			'wrapper_class' => '',
			'value'         => '',
			'name'          => '',
			'tooltip'       => '',
			'desc'          => '',
			'default'       => '',
			'types'         => array( 'jpg', 'jpeg', 'png' ),
			'limit'         => '2024',
			'required'      => false,
			'disabled'      => false,
			'readonly'      => false,
			'attr'          => array(),
		)
	);

	$field['id']                 = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value']              = ! isset( $field['value'] ) ? $field['default'] : $field['value'];
	$field['attr']['required']   = ( true == $field['required'] ) ? ' required ' : '';
	$field['attr']['readonly']   = ( true == $field['readonly'] ) ? ' readonly ' : '';
	$field['attr']['disabled']   = ( true == $field['disabled'] ) ? ' disabled ' : '';
	$field['attr']['data-nonce'] = wp_create_nonce( 'eaccounting_file_upload' );
	$field['attr']['data-limit'] = $field['limit'];
	$field['wrapper_class']     .= ( true == $field['required'] ) ? ' required ' : '';
	if ( ! empty( $field['types'] ) ) {
		$field['attr']['data-types'] = implode( '|', $field['types'] );
		$field['attr']['accept']     = implode( ',', $field['types'] );
	}

	// Custom attribute handling
	$attributes      = eaccounting_implode_html_attributes( $field['attr'] );
	$tooltip         = ! empty( $field['tooltip'] ) ? eaccounting_help_tip( $field['tooltip'] ) : '';
	$desc            = ! empty( $field['desc'] ) ? sprintf( '<span class="desc">%s</span>', wp_kses_post( $field['desc'] ) ) : '';
	$field['style'] .= ! empty( $field['value'] ) ? 'display:none;' : '';

	if ( ! empty( $field['label'] ) ) {
		echo sprintf(
			'<div class="ea-form-field ea-file-field %s_field %s"><label class="ea-label" for="%s">%s</label>%s',
			esc_attr( $field['id'] ),
			esc_attr( $field['wrapper_class'] ),
			esc_attr( $field['id'] ),
			wp_kses_post( $field['label'] ),
			$tooltip
		);
	}

	$link = empty( $field['value'] ) ? '' : $field['value'];
	$name = empty( $field['value'] ) ? '' : basename( $field['value'] );
	?>
	<div class="ea-file" style="<?php echo empty( $field['value'] ) ? 'display:none' : ''; ?>">
		<a href="<?php echo esc_url( $link ); ?>" target="_blank" class="ea-file-link"><?php echo sanitize_file_name( $name ); ?></a>
		<a href="#" class="ea-file-delete"><span class="dashicons dashicons-no-alt"></span></a>
	</div>
	<?php

	echo sprintf(
		'<input type="hidden" name="%s" class="ea-file-input" id="%s" value="%s"/>',
		esc_attr( $field['name'] ),
		esc_attr( $field['id'] ),
		esc_attr( $field['value'] )
	);
	echo sprintf(
		'<input type="file" class="ea-file-upload %s" style="%s" %s/>',
		esc_attr( $field['class'] ),
		esc_attr( $field['style'] ),
		$attributes
	);
	if ( ! empty( $field['label'] ) ) {
		echo $desc;

		echo '</div>';
	}

}

/**
 * Output a toggle field
 *
 * @since 1.0.2
 *
 * @param array $field Data about the field to render.
 */

function eaccounting_toggle( $field ) {
	$field = (array) wp_parse_args(
		$field,
		array(
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
		echo sprintf(
			'<div class="ea-form-field %s_field %s"><label class="ea-label" for="%s">%s</label>%s',
			esc_attr( $field['id'] ),
			esc_attr( $field['wrapper_class'] ),
			esc_attr( $field['id'] ),
			wp_kses_post( $field['label'] ),
			$tooltip
		);
	}

	echo sprintf(
		'<label class="ea-toggle"><input type="checkbox" name="%s" id="%s" class="%s"  style="%s" value="%s" %s %s><span data-label-off="%s" data-label-on="%s" class="ea-toggle-slider"></span></label>',
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
 * Select field wrapper for ajax select 2 and new item creatable.
 *
 * @since 1.0.2
 *
 * @param array $field field properties.
 */
function eaccounting_select2( $field ) {
	$field = (array) wp_parse_args(
		$field,
		array(
			'class'     => '',
			'ajax'      => false,
			'type'      => '',
			'creatable' => false,
			'template'  => '',
			'attr'      => array(),
		)
	);

	if ( $field['ajax'] && empty( $field['type'] ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Ajax type defined without type property', 'wp-ever-accounting' ), '1.0.2' );
		$field['ajax'] = false;
	}

	if ( $field['creatable'] && empty( $field['template'] ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Creatable defined without template property', 'wp-ever-accounting' ), '1.0.2' );
		$field['creatable'] = false;
	}

	$field['class'] .= ' ea-select2 ';
	if ( $field['ajax'] ) {
		$field['attr'] = array_merge(
			$field['attr'],
			array(
				'data-ajax'  => true,
				'data-type'  => $field['type'],
				'data-nonce' => wp_create_nonce( 'ea-dropdown-search' ),
			)
		);

		unset( $field['ajax'] );
		unset( $field['type'] );
	}

	if ( $field['creatable'] ) {
		$field['attr'] = array_merge(
			$field['attr'],
			array(
				'data-creatable' => true,
				'data-template'  => $field['template'],
				'data-text'      => __( 'Add New', 'wp-ever-accounting' ),
			)
		);

		unset( $field['creatable'] );
		unset( $field['template'] );
	}

	if ( ! empty( $field['placeholder'] ) ) {
		$field['options'] = array( '' => esc_html( $field['placeholder'] ) ) + $field['options'];
	}
	eaccounting_select( $field );
}

/***
 * Dropdown field for selecting contacts.
 *
 * @since 1.0.2
 *
 * @param array $field
 */
function eaccounting_contact_dropdown( $field ) {
	$type       = ! empty( $field['type'] ) && array_key_exists( $field['type'], eaccounting_get_contact_types() ) ? eaccounting_clean( $field['type'] ) : false;
	$value      = ! empty( $field['value'] ) ? eaccounting_clean( $field['value'] ) : '';
	$query_args = array( 'return' => 'raw' );
	if ( ! empty( $value ) ) {
		$query_args['include'] = $value;
	}

	$function = 'customer' === $type ? 'eaccounting_get_customers' : 'eaccounting_get_vendors';

	$contacts = call_user_func_array( $function, array( $query_args ) );

	$field = wp_parse_args(
		array(
			'value'    => $value ? absint( $value ) : '',
			'options'  => wp_list_pluck( $contacts, 'name', 'id' ),
			'type'     => $type,
			'ajax'     => true,
			'template' => 'add-' . $type,
		),
		$field
	);
	eaccounting_select2( apply_filters( 'eaccounting_contact_dropdown', $field ) );
}

/***
 * Dropdown field for selecting account.
 *
 * @since 1.0.2
 *
 * @param array $field
 */
function eaccounting_account_dropdown( $field ) {
	$default_id = '';
	$account_id = ! empty( $field['value'] ) ? eaccounting_clean( $field['value'] ) : 0;
	$accounts   = array();
	$include    = array_filter( array_unique( array( $default_id, $account_id ) ) );
	if ( ! empty( $include ) ) {
		$accounts = eaccounting_get_accounts(
			array(
				'return'  => 'raw',
				'include' => $include,
			)
		);
	}
	if ( ! isset( $field['default'] ) ) {
		$default_id = (int) eaccounting()->settings->get( 'default_account' );
	}

	$field = wp_parse_args(
		$field,
		array(
			'type'        => 'account',
			'ajax'        => true,
			'default'     => $default_id,
			'template'    => 'add-account',
			'options'     => wp_list_pluck( $accounts, 'name', 'id' ),
			'placeholder' => __( 'Select Account', 'wp-ever-accounting' ),
		)
	);
	eaccounting_select2( apply_filters( 'eaccounting_account_dropdown', $field ) );
}

/***
 * Dropdown field for selecting category.
 *
 * @since 1.0.2
 *
 * @param array $field
 */
function eaccounting_category_dropdown( $field ) {
	$field      = wp_parse_args(
		$field,
		array(
			'value' => '',
			'type'  => '',
		)
	);
	$type       = ! empty( $field['type'] ) && array_key_exists( $field['type'], eaccounting_get_category_types() ) ? eaccounting_clean( $field['type'] ) : false;
	$value      = ! empty( $field['value'] ) ? eaccounting_clean( $field['value'] ) : false;
	$query_args = array(
		array( 'fields' => array( 'id', 'name' ) ),
		'return' => 'raw',
	);
	if ( $type ) {
		$query_args['type'] = $type;
	}
	if ( ! empty( $value ) ) {
		$query_args['include'] = $value;
	}
	$categories = eaccounting_get_categories( $query_args );

	$field = wp_parse_args(
		array(
			'value'       => $value ? absint( $value ) : '',
			'options'     => wp_list_pluck( $categories, 'name', 'id' ),
			'type'        => $type . '_category',
			'ajax'        => true,
			'placeholder' => __( 'Select Category', 'wp-ever-accounting' ),
			'template'    => 'add-category',
			'data'        => array(
				'data-category_type' => $type,
			),
		),
		$field
	);

	eaccounting_select2( apply_filters( 'eaccounting_category_dropdown', $field ) );
}

/**
 * Dropdown field for selecting payment method.
 *
 * @since 1.0.2
 *
 * @param array $field
 *
 * @return void
 */
function eaccounting_payment_method_dropdown( $field ) {
	$default = '';
	if ( ! isset( $field['default'] ) ) {
		$default = eaccounting()->settings->get( 'default_payment_method' );
	}

	$field = wp_parse_args(
		array(
			'placeholder' => __( 'Enter payment method', 'wp-ever-accounting' ),
			'default'     => $default,
			'options'     => eaccounting_get_payment_methods(),
		),
		$field
	);

	eaccounting_select2( apply_filters( 'eaccounting_payment_method_dropdown', $field ) );
}

/**
 * Dropdown field for selecting currency.
 *
 * @since 1.0.2
 *
 * @param array $field
 *
 * @return void
 */
function eaccounting_currency_dropdown( $field ) {
	$default_code  = (string) eaccounting()->settings->get( 'default_currency' );
	$currency_code = ! empty( $field['value'] ) ? eaccounting_clean( $field['value'] ) : 0;
	$options       = array();
	$wherein       = array_filter( array_unique( array( $default_code, $currency_code ) ) );
	if ( ! empty( $wherein ) ) {
		$options = eaccounting_get_currencies(
			array(
				'return' => 'raw',
				'search' => implode(
					'',
					$wherein
				),
			)
		);
	}
	$field = wp_parse_args(
		$field,
		array(
			'default'     => $default_code,
			'options'     => wp_list_pluck( $options, 'name', 'code' ),
			'placeholder' => __( 'Select Currency', 'wp-ever-accounting' ),
			'ajax'        => true,
			'type'        => 'currency',
			'template'    => 'add-currency',
		)
	);
	eaccounting_select2( apply_filters( 'eaccounting_currency_dropdown', $field ) );
}

/**
 * Dropdown field for selecting country.
 *
 * @since 1.0.2
 *
 * @param array $field
 *
 * @return void
 */
function eaccounting_country_dropdown( $field ) {
	$default = eaccounting()->settings->get( 'company_country' );
	$field   = wp_parse_args(
		$field,
		array(
			'default'     => $default,
			'options'     => eaccounting_get_countries(),
			'placeholder' => __( 'Select Country', 'wp-ever-accounting' ),
		)
	);

	eaccounting_select2( apply_filters( 'eaccounting_country_dropdown', $field ) );
}

/**
 * echo date range field.
 *
 * @since 1.0.2
 *
 * @param $field
 */
function eaccounting_input_date_range( $field ) {
	$field       = (array) wp_parse_args(
		$field,
		array(
			'start_date'  => '',
			'end_date'    => '',
			'name'        => '',
			'placeholder' => '',
		)
	);
	$field['id'] = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$value       = __( 'Date range', 'wp-ever-accounting' );
	$html        = '<div class="ea-date-range-picker">';
	if ( ! empty( $field['start_date'] ) && ! empty( $field['end_date'] ) ) {
		$value = sprintf( '%s >> %s', eaccounting_format_datetime( $field['start_date'], 'd M y' ), eaccounting_format_datetime( $field['start_date'], 'd M y' ) );
	}

	$html .= sprintf( '<span>%s</span>', eaccounting_clean( $value ) );
	$html .= sprintf( '<input type="hidden" name="start_date" value="%s">', eaccounting_clean( $field['start_date'] ) );
	$html .= sprintf( '<input type="hidden" name="end_date" value="%s">', eaccounting_clean( $field['end_date'] ) );
	$html .= '</div>';
	echo $html;
}


