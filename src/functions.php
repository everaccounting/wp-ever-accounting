<?php
/**
 * Core functions
 *
 * @version  1.1.0
 * @category Functions
 * @package  EverAccounting\Functions
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/Functions/accounts.php';
require_once __DIR__ . '/Functions/categories.php';
require_once __DIR__ . '/Functions/currencies.php';
require_once __DIR__ . '/Functions/items.php';
require_once __DIR__ . '/Functions/taxes.php';
require_once __DIR__ . '/Functions/updates.php';

/**
 * Get base currency code
 *
 * @since 1.0.2
 * @return string
 */
function eac_get_base_currency() {
	$settings = get_option( 'eaccounting_settings', array() );
	$currency = get_option( 'eac_base_currency', isset( $settings['default_currency'] ) ? $settings['default_currency'] : 'usd' );

	return apply_filters( 'ever_accounting_base_currency', strtoupper( $currency ) );
}

/**
 * Format price with currency code & number format
 *
 * @param string $amount Amount.
 *
 * @param string $code If not passed will be used default currency.
 *
 * @since 1.0.2
 * @return string
 */
function eac_format_money( $amount, $code = null ) {
	if ( is_null( $code ) ) {
		$code = eac_get_base_currency();
	}
	if ( ! is_numeric( $amount ) ) {
		$amount = eac_sanitize_money( $amount, $code );
	}
	$currency     = eac_get_currency( $code );
	$precision    = $currency ? $currency->precision : 2;
	$thousand_sep = $currency ? $currency->thousand_separator : '';
	$decimal_sep  = $currency ? $currency->decimal_separator : '.';
	$position     = $currency ? $currency->position : 'before';
	$symbol       = $currency ? $currency->symbol : '';
	$prefix       = 'before' === $position ? $symbol : '';
	$suffix       = 'after' === $position ? $symbol : '';
	$is_negative  = $amount < 0;
	$amount       = $is_negative ? - $amount : $amount;

	$value = number_format( $amount, $precision, $decimal_sep, $thousand_sep );

	return ( $is_negative ? '-' : '' ) . $prefix . $value . $suffix;
}

/**
 * Sanitize price for inserting into database.
 *
 * When converting to default currency, the amount will convert to default currency
 * with the exchange rate of the currency at the time of the transaction.
 *
 * @param string $amount Amount.
 * @param string $from_code If not passed will be used default currency.
 *
 * @since 1.0.2
 * @return float|int
 */
function eac_sanitize_money( $amount, $from_code = null ) {
	$base_currency = eac_get_base_currency();
	// Get the base currency if not passed.
	if ( is_null( $from_code ) ) {
		$from_code = $base_currency;
	}

	if ( ! is_numeric( $amount ) ) {
		$currency = eac_get_currency( $from_code );
		// Retrieve the thousand and decimal separator from currency object.
		$thousand_separator = $currency ? $currency->thousand_separator : '';
		$decimal_separator  = $currency ? $currency->decimal_separator : '.';
		$symbol             = $currency ? $currency->symbol : '';
		// Remove currency symbol from amount.
		$amount = str_replace( $symbol, '', $amount );
		// Remove any non-numeric characters except a thousand and decimal separators.
		$amount = preg_replace( '/[^0-9\\' . $thousand_separator . '\\' . $decimal_separator . '\-\+]/', '', $amount );
		// Replace a thousand and decimal separators with empty string and dot respectively.
		$amount = str_replace( array( $thousand_separator, $decimal_separator ), array( '', '.' ), $amount );
		// Convert to int if amount is a whole number, otherwise convert to float.
		if ( preg_match( '/^([\-\+])?\d+$/', $amount ) ) {
			$amount = (int) $amount;
		} elseif ( preg_match( '/^([\-\+])?\d+\.\d+$/', $amount ) ) {
			$amount = (float) $amount;
		} else {
			$amount = 0;
		}
	}

	return $amount;
}

/**
 * Convert price from default to any other currency.
 *
 * @param string $amount Amount.
 * @param string $to Convert to currency code.
 * @param string $to_rate Convert to currency rate.
 *
 * @since 1.0.2
 * @return float|int|string
 */
function eac_convert_money_from_base( $amount, $to, $to_rate = null ) {
	$default = eac_get_base_currency();
	$amount  = eac_sanitize_money( $amount, $to );
	// No need to convert same currency.
	if ( $default === $to ) {
		return $amount;
	}
	$currency  = eac_get_currency( $to );
	$precision = $currency ? $currency->precision : 2;
	if ( is_null( $to_rate ) ) {
		$to_rate = $currency ? $currency->exchange_rate : 1;
	}

	// First check if mathematically possible.
	if ( 0 === $to_rate ) {
		return 0;
	}

	// Multiply by rate.
	return round( $amount * $to_rate, $precision, PHP_ROUND_HALF_UP );
}

/**
 * Convert price from any currency to default.
 *
 * @param string $amount Amount.
 * @param string $from Convert from currency code.
 * @param string $from_rate Convert from currency rate.
 * @param bool   $formatted Whether to format the price or not.
 *
 * @since 1.0.2
 * @return float|int|string
 */
function eac_convert_money_to_base( $amount, $from, $from_rate = null, $formatted = false ) {
	$base          = eac_get_base_currency();
	$from_currency = eac_get_currency( $from );
	$amount        = eac_sanitize_money( $amount, $from );
	// No need to convert same currency.
	if ( $base === $from ) {
		return $amount;
	}

	if ( empty( $from_rate ) ) {
		$from_rate = $from_currency ? $from_currency->exchange_rate : 1;
	}

	// First check if mathematically possible.
	if ( 0 === $from_rate ) {
		$amount = 0;
	} else {
		// Divide by rate.
		$precision = $from_currency ? $from_currency->precision : 2;
		$amount    = round( $amount / $from_rate, $precision, PHP_ROUND_HALF_UP );
	}

	if ( $formatted ) {
		$amount = eac_format_money( $amount, $base );
	}

	return $amount;
}

/**
 * Convert price from one currency to another.
 *
 * @param string      $amount Amount.
 * @param string      $from Convert from currency code.
 * @param string|null $to Convert to currency code.
 * @param string|null $from_rate Convert from currency rate.
 * @param string|null $to_rate Convert to currency rate.
 * @param bool        $formatted Whether to format the price or not.
 *
 * @since 1.0.2
 * @return float|int|string
 */
function eac_convert_money( $amount, $from, $to, $from_rate = null, $to_rate = null, $formatted = false ) {

	if ( ! is_numeric( $amount ) ) {
		$amount = eac_sanitize_money( $amount, $from );
	}

	if ( empty( $from_rate ) ) {
		$from_currency = eac_get_currency( $from );
		$from_rate     = $from_currency ? $from_currency->exchange_rate : 1;
	}

	if ( empty( $to_rate ) ) {
		$to_currency = eac_get_currency( $to );
		$to_rate     = $to_currency ? $to_currency->exchange_rate : 1;
	}

	// No need to convert same currency.
	if ( $from !== $to && $amount > 0 && $from_rate > 0 ) {
		$currency  = eac_get_currency( $to );
		$precision = $currency ? $currency->precision : 2;
		$amount    = round( $amount / $from_rate, $precision, PHP_ROUND_HALF_UP );
	}

	if ( $amount > 0 && $to_rate > 0 ) {
		$currency  = eac_get_currency( $to );
		$precision = $currency ? $currency->precision : 2;
		$amount    = round( $amount * $to_rate, $precision, PHP_ROUND_HALF_UP );
	}

	if ( $formatted ) {
		$amount = eac_format_money( $amount, $to );
	}

	return $amount;
}

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
		'type'    => 'text',
		'name'    => '',
		'id'      => '',
		'label'   => '',
		'desc'    => '',
		'tooltip' => '',
		'default' => '',
		'suffix'  => '',
		'prefix'  => '',
	);

	$field = wp_parse_args( $field, $defaults );

	/**
	 * Filter the arguments of a form field before it is rendered.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.2.0
	 */
	$field = apply_filters( 'ever_accounting_form_field_args', $field );

	// Set default name and ID attributes if not provided.
	$field['name']        = empty( $field['name'] ) ? $field['id'] : $field['name'];
	$field['id']          = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value']       = empty( $field['value'] ) ? $field['default'] : $field['value'];
	$field['attrs']       = array_filter( array_unique( wp_parse_args( $field['attrs'] ) ) );
	$field['class']       = array_filter( array_unique( wp_parse_list( $field['class'] ) ) );
	$field['class']       = implode( ' ', $field['class'] );
	$field['input_class'] = array_filter( array_unique( wp_parse_list( $field['input_class'] ) ) );
	$field['input_class'] = implode( ' ', $field['input_class'] );

	// Custom input attribute handling.
	$attrs = array();
	foreach ( array( 'readonly', 'disabled', 'required', 'autofocus' ) as $attr_key ) {
		if ( isset( $field[ $attr_key ] ) && ! empty( $field[ $attr_key ] ) ) {
			$field['attrs'][ $attr_key ] = $attr_key;
		}
	}

	if ( ! empty( $field['input_style'] ) ) {
		$field['attrs']['style'] = $field['input_style'];
	}
}

/**
 * Input group field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.2.0
 * @return void
 */
function eac_input_group( $field ) {
	$defaults = array(
		'type'        => 'text',
		'name'        => '',
		'id'          => '',
		'placeholder' => '',
		'required'    => false,
		'readonly'    => false,
		'disabled'    => false,
		'autofocus'   => false,
		'class'       => '',
		'style'       => '',
		'options'     => array(),
		'attrs'       => array(),
		'default'     => '',
		'suffix'      => '',
		'prefix'      => '',
	);

	$field = wp_parse_args( $field, $defaults );

	/**
	 * Filter the arguments of an input group before it is rendered.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.2.0
	 */
	$field = apply_filters( 'ever_accounting_input_group_args', $field );

	// Set default name and ID attributes if not provided.
	$field['name']  = empty( $field['name'] ) ? $field['id'] : $field['name'];
	$field['id']    = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value'] = empty( $field['value'] ) ? $field['default'] : $field['value'];
	$field['attrs'] = array_filter( array_unique( wp_parse_args( $field['attrs'] ) ) );
	$field['class'] = array_filter( array_unique( wp_parse_list( $field['class'] ) ) );
	$field['class'] = implode( ' ', $field['class'] );

	foreach ( array( 'readonly', 'disabled', 'required', 'autofocus' ) as $attr_key ) {
		if ( isset( $field[ $attr_key ] ) && ! empty( $field[ $attr_key ] ) ) {
			$field['attrs'][ $attr_key ] = $attr_key;
		}
	}
}

/**
 * Input group field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.2.0
 * @return void
 */
function eac_input_field( $field ) {
	$defaults = array(
		'type'        => 'text',
		'name'        => '',
		'id'          => '',
		'placeholder' => '',
		'required'    => false,
		'readonly'    => false,
		'disabled'    => false,
		'autofocus'   => false,
		'class'       => '',
		'style'       => '',
		'options'     => array(),
		'attrs'       => array(),
		'default'     => '',
		'suffix'      => '',
		'prefix'      => '',
	);

	$field = wp_parse_args( $field, $defaults );

	/**
	 * Filter the arguments of an input group before it is rendered.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.2.0
	 */
	$field = apply_filters( 'ever_accounting_input_group_args', $field );

	// Set default name and ID attributes if not provided.
	$field['name']  = empty( $field['name'] ) ? $field['id'] : $field['name'];
	$field['id']    = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value'] = empty( $field['value'] ) ? $field['default'] : $field['value'];
	$field['attrs'] = array_filter( array_unique( wp_parse_args( $field['attrs'] ) ) );
	$field['class'] = array_filter( array_unique( wp_parse_list( $field['class'] ) ) );
	$field['class'] = implode( ' ', $field['class'] );

	foreach ( array( 'readonly', 'disabled', 'required', 'autofocus' ) as $attr_key ) {
		if ( isset( $field[ $attr_key ] ) && ! empty( $field[ $attr_key ] ) ) {
			$field['attrs'][ $attr_key ] = $attr_key;
		}
	}
	foreach ( array( 'style', 'placeholder' ) as $attr_key ) {
		if ( isset( $field[ $attr_key ] ) && ! empty( $field[ $attr_key ] ) ) {
			$field['attrs'][ $attr_key ] = esc_attr( $field[ $attr_key ] );
		}
	}
	switch ( $field['type'] ) {
		case 'select':
			$field['value']       = wp_parse_list( $field['value'] );
			$field['value']       = array_map( 'strval', $field['value'] );
			$field['placeholder'] = ! empty( $field['placeholder'] ) ? $field['placeholder'] : __( 'Select an option&hellip;', 'wp-ever-accounting' );
			if ( ! empty( $field['multiple'] ) ) {
				$field['name'] .= '[]';
				$field['attrs'] = 'multiple="multiple"';
			}

			printf(
				'<select name="%1$s" id="%2$s" class="%3$s" style="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $field['attrs'] ) )
			);

			if ( ! empty( $field['placeholder'] ) ) {
				printf(
					'<option value="">%s</option>',
					esc_html( $field['placeholder'] )
				);
			}

			foreach ( $field['options'] as $key => $value ) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $key ),
					selected( in_array( (string) $key, $field['value'], true ), true, false ),
					esc_html( $value )
				);
			}
			echo '</select>';
			break;
		case 'radio':
		case 'checkboxes':
			$field['value'] = wp_parse_list( $field['value'] );
			$field['value'] = array_map( 'strval', $field['value'] );
			echo '<div class="fieldset">';
			foreach ( $field['options'] as $key => $value ) {
				printf(
					'<label><input type="%1$s" name="%2$s" id="%3$s" class="%4$s" value="%5$s" %6$s %7$s> %8$s</label>',
					esc_attr( $field['type'] ),
					esc_attr( $field['name'] ),
					esc_attr( $field['id'] . '-' . $key ),
					esc_attr( $field['class'] ),
					esc_attr( $key ),
					checked( in_array( (string) $key, $field['value'], true ), true, false ),
					wp_kses_post( implode( ' ', $field['attrs'] ) ),
					esc_html( $value )
				);
			}
			echo '</div>';
			break;
		case 'checkbox':
			printf(
				'<label><input type="checkbox" name="%1$s" id="%2$s" class="%3$s" value="1" %4$s %5$s> %6$s</label>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				checked( ! empty( $field['value'] ), true, false ),
				wp_kses_post( implode( ' ', $field['attrs'] ) ),
				esc_html( $field['label'] )
			);
			break;

		case 'textarea':
			$rows = ! empty( $field['rows'] ) ? absint( $field['rows'] ) : 4;
			$cols = ! empty( $field['cols'] ) ? absint( $field['cols'] ) : 50;
			printf(
				'<textarea name="%1$s" id="%2$s" class="%3$s" placeholder="%4$s" rows="%5$s" cols="%6$s" style="%7$s" %8$s>%9$s</textarea>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $rows ),
				esc_attr( $cols ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $field['attrs'] ) ),
				esc_textarea( $field['value'] )
			);
			break;
		case 'file':
			$accept = ! empty( $field['accept'] ) ? $field['accept'] : 'image/*';
			printf(
				'<input type="file" name="%1$s" id="%2$s" class="%3$s" accept="%4$s" style="%5$s" %6$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $accept ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $field['attrs'] ) )
			);
			break;
		case 'wp_editor':
			$settings = ! empty( $field['settings'] ) ? $field['settings'] : array();
			wp_editor(
				$field['value'],
				$field['id'],
				wp_parse_args(
					$settings,
					array(
						'textarea_name'    => $field['name'],
						'textarea_rows'    => 5,
						'teeny'            => true,
						'media_buttons'    => false,
						'quicktags'        => false,
						'drag_drop_upload' => false,
						'editor_class'     => 'eac-form-field__control',
					)
				)
			);
			break;

		case 'country':
			$options          = \EverAccounting\Utilities\I18n::get_currencies();
			$field['options'] = $options;
			$field['type']    = 'select';
			eac_input_field( $field );
			break;
		case 'text':
		case 'email':
		case 'url':
		case 'tel':
		case 'number':
		case 'password':
		default:
			printf(
				'<input type="%1$s" name="%2$s" id="%3$s" class="%4$s" value="%5$s" placeholder="%6$s" style="%7$s" %8$s>',
				esc_attr( $field['type'] ),
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['value'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $field['attrs'] ) )
			);
			break;
	}
}
