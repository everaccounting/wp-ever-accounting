<?php

defined( 'ABSPATH' ) || exit();
/**
 * Gets and includes template files.
 *
 * @since 1.0.0
 * @param mixed  $template_name
 * @param array  $args (default: array()).
 * @param string $template_path (default: '').
 * @param string $default_path (default: '').
 */
function eaccounting_get_template( $template_name, $args = [], $template_path = 'eaccounting', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}
	include eaccounting_locate_template( $template_name, $template_path, $default_path );
}


/**
 * Locates a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *      yourtheme       /   $template_path  /   $template_name
 *      yourtheme       /   $template_name
 *      $default_path   /   $template_name
 *
 * @since 1.0.0
 * @param string      $template_name
 * @param string      $template_path (default: 'eaccounting').
 * @param string|bool $default_path (default: '') False to not load a default.
 * @return string
 */
function eaccounting_locate_template( $template_name, $template_path = 'eaccounting', $default_path = '' ) {
	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		[
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		]
	);

	// Get default template.
	if ( ! $template && false !== $default_path ) {
		$default_path = $default_path ? $default_path : EACCOUNTING_ABSPATH . '/templates/';
		if ( file_exists( trailingslashit( $default_path ) . $template_name ) ) {
			$template = trailingslashit( $default_path ) . $template_name;
		}
	}

	// Return what we found.
	return apply_filters( 'eaccounting_locate_template', $template, $template_name, $template_path );
}

/**
 * Gets template part (for templates in loops).
 *
 * @since 1.0.0
 * @param string      $slug
 * @param string      $name (default: '').
 * @param string      $template_path (default: 'job_manager').
 * @param string|bool $default_path (default: '') False to not load a default.
 */
function eaccounting_get_template_part( $slug, $name = '', $template_path = 'job_manager', $default_path = '' ) {
	$template = '';

	if ( $name ) {
		$template = eaccounting_locate_template( "{$slug}-{$name}.php", $template_path, $default_path );
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/job_manager/slug.php.
	if ( ! $template ) {
		$template = eaccounting_locate_template( "{$slug}.php", $template_path, $default_path );
	}

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Open wrapper
 * since 1.0.0
 *
 * @param string $class
 */
function eaccounting_page_wrapper_open( $class = ' ' ) {
	$classes = 'ea-main-wrapper ' . sanitize_html_class( $class );
	//echo '<div class="wrap">';
	echo '<div class="wrap ' . $classes . '">';
	//do_action( 'eaccounting_page_top' );
	echo '<div class="ea-page-wrapper">';
	echo '<hr class="wp-header-end">';
}

/**
 * Close wrapper
 *
 * since 1.0.0
 */
function eaccounting_page_wrapper_close() {
	echo '<div><!--.ea-page-wrapper-->';
	//echo '<div><!--.ea-main-wrapper-->';
	echo '<div><!--.wrap-->';
}

function eaccounting_add_page_header() {
	ob_start();
	include eaccounting()->plugin_path() . '/templates/header.php';
	$html = ob_get_contents();
	ob_get_clean();
	echo $html;
}

add_action( 'eaccounting_page_top', 'eaccounting_add_page_header' );

/**
 * Select field
 *
 * @param $args
 *
 * @return string
 * @since 1.0.0
 */
function eaccounting_select_field( $args ) {
	$defaults = array(
		'label'         => '',
		'name'          => null,
		'options'       => array(),
		'selected'      => array(),
		'default'       => null,
		'icon'          => '',
		'class'         => '',
		'wrapper_class' => '',
		'id'            => '',
		'select2'       => false,
		'placeholder'   => __( '-- Please Select --', 'wp-ever-accounting' ),
		'multiple'      => false,
		'data'          => array(),
		'required'      => false,
		'readonly'      => false,
		'disabled'      => false,
	);
	$args     = wp_parse_args( $args, $defaults );
	$name     = sanitize_key( ! empty( $args['name'] ) ? $args['name'] : '' );
	$id       = sanitize_key( ! empty( $args['id'] ) ? $args['id'] : $name );
	if ( $args['multiple'] ) {
		$value = ! empty( $args['selected'] ) ? (array) $args['selected'] : (array) $args['default'];
	} else {
		$value = ! empty( $args['selected'] ) ? $args['selected'] : $args['default'];
	}
	$class                    = isset( $args['class'] ) && ! empty( $args['class'] ) ? sanitize_html_class( $args['class'] ) : '';
	$data_attr                = $args['data'];
	$data_attr['required']    = true == $args['required'] ? 'required' : '';
	$data_attr['readonly']    = true == $args['readonly'] ? 'readonly' : '';
	$data_attr['disabled']    = true == $args['disabled'] ? 'disabled' : '';
	$data_attr['placeholder'] = $args['placeholder'] ? sanitize_text_field( $args['placeholder'] ) : '';
	$class                    .= true == $args['select2'] ? ' ea-select2 ' : '';

	$wrapper_class = ( true == $args['required'] ) ? 'required' : '';
	$wrapper_class .= empty( $args['wrapper_class'] ) ? '' : sanitize_html_class( $args['wrapper_class'] );

	$icon   = empty( $args['icon'] ) ? false : sprintf( '<i class="%s"></i>', $args['icon'] );
	$button = empty( $args['button'] ) ? false : wp_kses( $args['button'], array(
		'button' => array( 'class' => true ),
		'i'      => array( 'class' => true )
	) );


	if ( empty( $name ) ) {
		wp_die( 'field name missing' );
	}

	$attributes = eaccounting_make_field_attributes( $data_attr );

	$html = sprintf( '<div class="ea-form-group %s">', $wrapper_class );
	if ( ! empty( $args['label'] ) ) {
		$html .= sprintf( '<label for="%1$s" class="ea-control-label">%2$s</label>', $id, $args['label'] );
	}

	if ( $button || $icon ) {
		$html .= '<div class="ea-input-group">';
	}

	if ( $icon ) {
		$html .= sprintf( '<div class="ea-input-group-addon">%s</div>', $icon );
	}
	$class           = implode( ' ', array_map( 'trim', explode( ' ', $class ) ) );
	$html            .= sprintf( '<select class=" ea-form-control %1$s" name="%2$s" id="%3$s" %4$s>', $class, $name, $id, $attributes );
	$args['options'] = array_merge( array( '' => $args['placeholder'] ), $args['options'] );
	foreach ( $args['options'] as $key => $label ) {
		$selected = selected( $value, $key, false );
		if ( $args['multiple'] ) {
			$selected = in_array( $key, $value ) ? ' selected="selected" ' : '';
		}
		$html .= sprintf( '<option value="%s"%s>%s</option>', $key, $selected, $label );

	}
	$html .= '</select>';

	if ( $button ) {
		$html .= sprintf( '<div class="ea-input-group-btn">%s</div>', $button );
	}

	if ( $button || $icon ) {
		$html .= '</div><!--.ea-input-group-->';
	}

	$html .= '</div><!--.ea-form-group-->';

	return $html;

}

/**
 * Input field
 *
 * @param $args
 * @param string $type
 *
 * @return string
 * @since 1.0.0
 */
function eaccounting_input_field( $args, $type = 'text' ) {
	$defaults                 = array(
		'label'         => '',
		'name'          => null,
		'value'         => '',
		'default'       => null,
		'size'          => '',
		'icon'          => '',
		'class'         => '',
		'wrapper_class' => '',
		'id'            => '',
		'placeholder'   => '',
		'data'          => array(),
		'required'      => false,
		'readonly'      => false,
		'disabled'      => false,
	);
	$args                     = wp_parse_args( $args, $defaults );
	$name                     = sanitize_key( ! empty( $args['name'] ) ? $args['name'] : '' );
	$id                       = sanitize_key( ! empty( $args['id'] ) ? $args['id'] : $name );
	$value                    = isset( $args['value'] ) ? $args['value'] : $args['default'];
	$class                    = isset( $args['class'] ) && ! empty( $args['class'] ) ? sanitize_html_class( $args['class'] ) : '';
	$size                     = ! empty( $args['size'] ) ? $args['size'] : 'regular';
	$data_attr                = $args['data'];
	$data_attr['required']    = true == $args['required'] ? 'required' : '';
	$data_attr['readonly']    = true == $args['readonly'] ? 'readonly' : '';
	$data_attr['disabled']    = true == $args['disabled'] ? 'disabled' : '';
	$data_attr['placeholder'] = $args['placeholder'] ? sanitize_text_field( $args['placeholder'] ) : $args['label'];

	$wrapper_class = ( true == $args['required'] ) ? ' required ' : '';
	$wrapper_class .= empty( $args['wrapper_class'] ) ? '' : sanitize_html_class( $args['wrapper_class'] );

	$icon   = empty( $args['icon'] ) ? false : sprintf( '<i class="%s"></i>', $args['icon'] );
	$button = empty( $args['button'] ) ? false : wp_kses( $args['button'], array(
		'button' => array( 'class' => true ),
		'i'      => array( 'class' => true )
	) );

	if ( empty( $name ) ) {
		wp_die( 'field name missing' );
	}

	$attributes = eaccounting_make_field_attributes( $data_attr );

	$html = sprintf( '<div class="ea-form-group %s">', $wrapper_class );
	if ( ! empty( $args['label'] ) ) {
		$html .= sprintf( '<label for="%1$s" class="ea-control-label">%2$s</label>', $id, $args['label'] );
	}

	if ( $button || $icon ) {
		$html .= '<div class="ea-input-group">';
	}
	if ( $icon ) {
		$html .= sprintf( '<div class="ea-input-group-addon">%s</div>', $icon );
	}
	$html .= sprintf( '<input type="%1$s" class="ea-form-control %2$s-text %7$s" id="%3$s" name="%4$s" value="%5$s"%6$s/>', $type, $size, $id, $name, $value, $attributes, $class );
	if ( $button ) {
		$html .= sprintf( '<div class="ea-input-group-btn">%s</div>', $button );
	}
	if ( $button || $icon ) {
		$html .= '</div><!--.ea-input-group-->';
	}

	$html .= '</div><!--.ea-form-group-->';

	return $html;
}

function eaccounting_checkboxes_field( $args ) {

}

function eaccounting_radios_fields( $args ) {

}

function eaccounting_textarea_field( $args ) {
	$args = wp_parse_args( $args, array(
		'label'         => '',
		'name'          => null,
		'value'         => '',
		'check'         => 'on',
		'default'       => null,
		'class'         => '',
		'placeholder'   => '',
		'wrapper_class' => '',
		'id'            => '',
		'size'          => '',
		'rows'          => '5',
		'cols'          => '5',
		'data'          => array(),
		'required'      => false,
		'readonly'      => false,
		'disabled'      => false,
	) );

	$name                     = sanitize_key( ! empty( $args['name'] ) ? $args['name'] : '' );
	$id                       = sanitize_key( ! empty( $args['id'] ) ? $args['id'] : $name );
	$value                    = ! empty( $args['value'] ) ? $args['value'] : $args['default'];
	$check                    = ! empty( $args['check'] ) ? $args['check'] : 'on';
	$class                    = isset( $args['class'] ) && ! empty( $args['class'] ) ? sanitize_html_class( $args['class'] ) : '';
	$size                     = ! empty( $args['size'] ) ? $args['size'] : 'regular';
	$rows                     = ! empty( $args['rows'] ) ? $args['rows'] : '5';
	$cols                     = ! empty( $args['cols'] ) ? $args['cols'] : '5';
	$data_attr                = $args['data'];
	$data_attr['required']    = true == $args['required'] ? 'required' : '';
	$data_attr['readonly']    = true == $args['readonly'] ? 'readonly' : '';
	$data_attr['disabled']    = true == $args['disabled'] ? 'disabled' : '';
	$data_attr['placeholder'] = $args['placeholder'] ? sanitize_text_field( $args['placeholder'] ) : $args['label'];

	$wrapper_class = ( true == $args['required'] ) ? ' required ' : '';
	$wrapper_class .= empty( $args['wrapper_class'] ) ? '' : sanitize_html_class( $args['wrapper_class'] );

	if ( empty( $name ) ) {
		wp_die( 'field name missing' );
	}

	$attributes = eaccounting_make_field_attributes( $data_attr );

	$html = sprintf( '<div class="ea-form-group %s">', $wrapper_class );
	if ( ! empty( $args['label'] ) ) {
		$html .= sprintf( '<label for="%1$s" class="ea-control-label">%2$s</label>', $id, $args['label'] );
	}

	$html .= sprintf( '<textarea rows="%7$s" cols="%8$s" class="ea-form-control %1$s-text %6$s" id="%2$s" name="%3$s" %5$s>%4$s</textarea>', $size, $id, $name, $value, $attributes, $class, $rows, $cols );

	$html .= '</div><!--.ea-form-group-->';

	return $html;
}

/**
 * Switch field
 *
 * @param $args
 *
 * @return string
 * @since 1.0.0
 */
function eaccounting_switch_field( $args ) {
	$args = wp_parse_args( $args, array(
		'label'         => '',
		'name'          => null,
		'value'         => '',
		'check'         => 'on',
		'default'       => null,
		'class'         => '',
		'wrapper_class' => '',
		'id'            => '',
		'data'          => array(),
		'required'      => false,
		'readonly'      => false,
		'disabled'      => false,
	) );

	$name                  = sanitize_key( ! empty( $args['name'] ) ? $args['name'] : '' );
	$id                    = sanitize_key( ! empty( $args['id'] ) ? $args['id'] : $name );
	$value                 = ! empty( $args['value'] ) ? $args['value'] : $args['default'];
	$check                 = ! empty( $args['check'] ) ? $args['check'] : 'on';
	$class                 = isset( $args['class'] ) && ! empty( $args['class'] ) ? sanitize_html_class( $args['class'] ) : '';
	$data_attr             = $args['data'];
	$data_attr['required'] = true == $args['required'] ? 'required' : '';
	$data_attr['readonly'] = true == $args['readonly'] ? 'readonly' : '';
	$data_attr['disabled'] = true == $args['disabled'] ? 'disabled' : '';

	$wrapper_class = ( true == $args['required'] ) ? ' required ' : '';
	$wrapper_class .= empty( $args['wrapper_class'] ) ? '' : sanitize_html_class( $args['wrapper_class'] );

	if ( empty( $name ) ) {
		wp_die( 'field name missing' );
	}

	$attributes = eaccounting_make_field_attributes( $data_attr );

	$html = sprintf( '<div class="ea-form-group ea-switch %s">', $wrapper_class );
	if ( ! empty( $args['label'] ) ) {
		$html .= sprintf( '<label for="%1$s" class="ea-control-label">%2$s</label>', $id, $args['label'] );
	}

	$html .= sprintf( '
	<fieldset>
		<label for="%1$s">
		<input type="checkbox" class="%2$s" id="%3$s" name="%4$s" value="%5$s" %6$s%7$s/>
		<span class="ea-switch-view"></span>
		</label>
	</fieldset>', $id, $class, $id, $name, $check, $attributes, checked( $value, $check, false ) );

	$html .= '</div><!--.ea-form-group-->';

	return $html;

}

/**
 * make a string of data attribute from array
 *
 * @param $data
 *
 * @return string
 * @since 1.0.0
 */
function eaccounting_make_field_attributes( $data ) {
	$data_elements = '';
	foreach ( $data as $key => $value ) {
		if ( $value == '' ) {
			continue;
		}
		$data_elements .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
	}

	return $data_elements;
}


function eaccounting_categories_dropdown( $args = array(), $type = 'product' ) {
	$args       = wp_parse_args( $args, array(
		'label'    => __( 'Product Category', 'wp-ever-accounting' ),
		'name'     => 'category',
		'icon'    => 'fa fa-folder-open-o',
		'class'    => '',
		'selected' => '',
		'select2'  => true,
	) );
	$categories = eaccounting_get_categories( array(
		'per_page' => '-1',
		'fields'   => array( 'id', 'name' ),
		'status'   => '1',
		'type'     => $type
	) );

	$categories      = wp_list_pluck( $categories, 'name', 'id' );
	$args['options'] = $categories;
	$args['class']   .= 'ea-category-dropdown';
	$args['data']    = array(
		'type'   => $type,
		'nonce'  => wp_create_nonce( 'eaccounting_search_categories' ),
		'action' => 'eaccounting_search_categories_dropdown',
	);

	return eaccounting_select_field( $args );
}

function eaccounting_taxes_dropdown( $args = array(), $type = 'product' ) {
	$args  = wp_parse_args( $args, array(
		'label'    => __( 'Product Tax', 'wp-ever-accounting' ),
		'name'     => 'tax_id',
		'class'    => '',
		'icon'    => 'fa fa-percent',
		'selected' => '',
		'select2'  => true,
	) );
	$taxes = eaccounting_get_taxes( array(
		'per_page' => '-1',
		'fields'   => array( 'id', 'name' ),
		'status'   => '1',
		'type'     => $type
	) );

	$taxes           = wp_list_pluck( $taxes, 'name', 'id' );
	$args['options'] = $taxes;
	$args['class']   .= 'ea-taxes-dropdown';
	$args['data']    = array(
		'type'   => $type,
		'nonce'  => wp_create_nonce( 'eaccounting_search_taxes' ),
		'action' => 'eaccounting_search_taxes_dropdown',
	);

	return eaccounting_select_field( $args );
}


function eaccounting_accounts_dropdown( $args = array(), $type = 'product' ) {
	$args  = wp_parse_args( $args, array(
		'label'    => __( 'Account', 'wp-ever-accounting' ),
		'name'     => 'account_id',
		'class'    => '',
		'icon'    => 'fa fa-university',
		'selected' => '',
		'select2'  => true,
	) );
	$taxes = eaccounting_get_accounts( array(
		'per_page' => '-1',
		'fields'   => array( 'id', 'name' ),
		'status'   => '1',
		'type'     => $type
	) );

	$taxes           = wp_list_pluck( $taxes, 'name', 'id' );
	$args['options'] = $taxes;
	$args['class']   .= 'ea-accounts-dropdown';
	$args['data']    = array(
		'type'   => $type,
		'nonce'  => wp_create_nonce( 'eaccounting_search_accounts' ),
		'action' => 'eaccounting_search_accounts',
	);

	return eaccounting_select_field( $args );
}

function eaccounting_get_taxes_dropdown( $args = array() ) {
	$args = wp_parse_args( $args, array() );
}
