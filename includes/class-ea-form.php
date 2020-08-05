<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Form {
	/**
	 * Input Control
	 *
	 * since 1.0.0
	 *
	 * @param $args
	 *
	 * @return string
	 */
	public static function input( $args ) {
		$args = wp_parse_args( $args, array(
			'type'          => 'text',
			'label'         => '',
			'name'          => '',
			'value'         => '',
			'default'       => '',
			'size'          => '',
			'css'           => '',
			'class'         => '',
			'wrapper_class' => '',
			'id'            => '',
			'placeholder'   => '',
			'data'          => array(),
			'desc_tip'      => false,
			'required'      => false,
			'readonly'      => false,
			'disabled'      => false,
		) );
		//general

		$name        = esc_attr( ! empty( $args['name'] ) ? $args['name'] : '' );
		$id          = esc_attr( ! empty( $args['id'] ) ? $args['id'] : $name );
		$value       = empty( $args['value'] ) ? $args['default'] : $args['value'];
		$label       = empty( $args['label'] ) ? false : strip_tags( $args['label'] );
		$type        = ! empty( $args['type'] ) ? $args['type'] : 'text';
		$size        = ! empty( $args['size'] ) ? $args['size'] : 'regular';
		$placeholder = ! empty( $args['placeholder'] ) ? strip_tags( $args['placeholder'] ) : strip_tags( $args['label'] );

		$input_classes = esc_attr( $args['class'] );
		$wrapper_class = esc_attr( $args['wrapper_class'] );
		$wrapper_class .= ( true == $args['required'] ) ? ' required ' : '';

		$desc = empty( $args['desc'] ) ? false : wp_kses_post( $args['desc'] );

		$data                = (array) $args['data'];
		$data['placeholder'] = $placeholder;
		$data['required']    = ( true == $args['required'] ) ? ' required ' : '';
		$data['readonly']    = ( true == $args['readonly'] ) ? ' readonly ' : '';
		$data['disabled']    = ( true == $args['disabled'] ) ? ' disabled ' : '';
		$attributes          = eaccounting_implode_html_attributes( $data );

		$help_tip = ! empty( $field['desc'] ) && false !== $field['desc_tip'] ? eaccounting_help_tip( $field['desc'] ) : '';


		$html = sprintf( '<div class="ea-form-field %s_field %s">', $id, $wrapper_class );
		$html .= ! empty( $label ) ? sprintf( '<label for="%1$s" class="ea-label">%2$s</label>', $id, $label ) : '';
		$html .= $help_tip;
		$html .= sprintf( '<input type="%1$s" class="ea-input-control %2$s-text %7$s" id="%3$s" name="%4$s" value="%5$s" %6$s autocomplete="off"/>', $type, $size, $id, $name, $value, $attributes, $input_classes );
		$html .= ! empty( $field['desc'] ) && false == $field['desc_tip'] ? $desc : '';
		$html .= '</div><!--.ea-form-field-->';

		return $html;
	}

	/**
	 * Sanitize button
	 *
	 * since 1.0.0
	 *
	 * @param $button
	 *
	 * @return string
	 */
	public static function sanitize_button( $button ) {
		return wp_kses( $button, array(
			'a'      => array( 'class' => true, 'href' => true ),
			'button' => array( 'class' => true ),
			'i'      => array( 'class' => true )
		) );
	}
}
