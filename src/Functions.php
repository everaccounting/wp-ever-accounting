<?php

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/Functions/Accounts.php';
require_once __DIR__ . '/Functions/Contacts.php';
require_once __DIR__ . '/Functions/Categories.php';
require_once __DIR__ . '/Functions/Currencies.php';
require_once __DIR__ . '/Functions/Deprecated.php';
require_once __DIR__ . '/Functions/Documents.php';
require_once __DIR__ . '/Functions/Formatters.php';
require_once __DIR__ . '/Functions/Items.php';
require_once __DIR__ . '/Functions/Media.php';
require_once __DIR__ . '/Functions/Misc.php';
require_once __DIR__ . '/Functions/Notes.php';
require_once __DIR__ . '/Functions/Reports.php';
require_once __DIR__ . '/Functions/Taxes.php';
require_once __DIR__ . '/Functions/Templates.php';
require_once __DIR__ . '/Functions/Transactions.php';

/**
 * Get base currency code
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_get_base_currency() {
	$currency = get_option( 'eac_base_currency', 'USD' );

	return apply_filters( 'ever_accounting_base_currency', strtoupper( $currency ) );
}

/**
 * Get base country.
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_get_base_country() {
	$country = get_option( 'eac_company_country', 'US' );

	return apply_filters( 'ever_accounting_base_country', $country );
}

/**
 * Get only numbers from the string.
 *
 * @param string   $number Number to get only numbers from.
 *
 * @param bool|int $decimals Allow decimal. If true, then allow decimal. If false, then only allow integers. If a number, then allow that many decimal places.
 *
 * @since 1.0.2
 *
 * @return int|float|null
 */
function eac_sanitize_number( $number, $decimals = 2 ) {
	// Convert multiple dots to just one.
	$number = preg_replace( '/\.(?![^.]+$)|[^0-9.-]/', '', eac_clean( $number ) );

	if ( $decimals ) {
		$number = (float) preg_replace( '/[^0-9.-]/', '', $number );
		// if allow decimal is a number, then use that as the number of decimals.
		if ( is_numeric( $decimals ) ) {
			$number = number_format( floatval( $number ), $decimals, '.', '' );
		}

		return $number;
	}

	return (int) preg_replace( '/[^0-9]/', '', $number );
}

/**
 * Round a number using the built-in `round` function, but unless the value to round is numeric
 * (a number or a string that can be parsed as a number), apply 'floatval' first to it
 * (so it will convert it to 0 in most cases).
 *
 * @param mixed $val The value to round.
 * @param int   $precision The optional number of decimal digits to round to.
 * @param int   $mode A constant to specify the mode in which rounding occurs.
 *
 * @return float The value rounded to the given precision as a float, or the supplied default value.
 */
function eac_round_number( $val, $precision = 6, $mode = PHP_ROUND_HALF_UP ) {
	$val = eac_sanitize_number( $val, $precision );

	return round( $val, $precision, $mode );
}

/**
 * Get only numbers from the string.
 *
 * @param string $number Number to get only numbers from.
 *
 * @param int    $decimals Number of decimals.
 * @param bool   $trim_zeros Trim zeros.
 *
 * @since 1.0.2
 *
 * @return int|float|null
 */
function eac_format_decimal( $number, $decimals = 4, $trim_zeros = true ) {

	// Convert multiple dots to just one.
	$number = preg_replace( '/\.(?![^.]+$)|[^0-9.-]/', '', eac_clean( $number ) );

	if ( is_numeric( $decimals ) ) {
		$number = number_format( floatval( $number ), $decimals, '.', '' );
	}

	if ( $trim_zeros && strstr( $number, '.' ) ) {
		$number = rtrim( rtrim( $number, '0' ), '.' );
	}

	return $number;
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
 *
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
		$thousand_separator = $currency ? $currency->get_thousand_separator() : ',';
		$decimal_separator  = $currency ? $currency->get_decimal_separator() : '.';
		$symbol             = $currency ? $currency->get_symbol() : '$';
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
 * Format price with currency code & number format
 *
 * @param string $amount Amount.
 *
 * @param string $code If not passed will be used default currency.
 *
 * @since 1.0.2
 *
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
	$precision    = $currency ? $currency->get_precision() : 2;
	$thousand_sep = $currency ? $currency->get_thousand_separator() : ',';
	$decimal_sep  = $currency ? $currency->get_decimal_separator() : '.';
	$position     = $currency ? $currency->get_position() : 'before';
	$symbol       = $currency ? $currency->get_symbol() : '$';
	$prefix       = 'before' === $position ? $symbol : '';
	$suffix       = 'after' === $position ? $symbol : '';
	$is_negative  = $amount < 0;
	$amount       = $is_negative ? - $amount : $amount;

	$value = number_format( $amount, $precision, $decimal_sep, $thousand_sep );

	return ( $is_negative ? '-' : '' ) . $prefix . $value . $suffix;
}

/**
 * Convert price from default to any other currency.
 *
 * @param string $amount Amount.
 * @param string $to Convert to currency code.
 * @param string $to_rate Convert to currency rate.
 *
 * @since 1.0.2
 *
 * @return float|int|string
 */
function eac_convert_money_from_base( $amount, $to, $to_rate ) {
	$default = eac_get_base_currency();
	$amount  = eac_sanitize_money( $amount, $to );
	// No need to convert same currency.
	if ( $default === $to ) {
		return $amount;
	}
	$currency  = eac_get_currency( $to );
	$precision = $currency ? $currency->get_precision() : 2;

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
 *
 * @return float|int|string
 */
function eac_convert_money_to_base( $amount, $from, $from_rate, $formatted = false ) {
	$default = eac_get_base_currency();
	$amount  = eac_sanitize_money( $amount, $from );
	// No need to convert same currency.
	if ( $default === $from ) {
		return $amount;
	}

	// First check if mathematically possible.
	if ( 0 === $from_rate ) {
		$amount = 0;
	} else {
		// Divide by rate.
		$currency  = eac_get_currency( $from );
		$precision = $currency ? $currency->get_precision() : 2;
		$amount    = round( $amount / $from_rate, $precision, PHP_ROUND_HALF_UP );
	}

	if ( $formatted ) {
		$amount = eac_format_money( $amount, $default );
	}

	return $amount;
}

/**
 * Convert price from one currency to another.
 *
 * @param string      $amount Amount.
 * @param string      $from Convert from currency code.
 * @param string|null $to Convert to currency code.
 * @param string|null $to_rate Convert to currency rate.
 * @param string|null $from_rate Convert from currency rate.
 * @param bool        $formatted Whether to format the price or not.
 *
 * @since 1.0.2
 *
 * @return float|int|string
 */
function eac_convert_money( $amount, $from, $to, $to_rate, $from_rate, $formatted = false ) {

	if ( ! is_numeric( $amount ) ) {
		$amount = eac_sanitize_money( $amount, $from );
	}

	// No need to convert same currency.
	if ( $from !== $to && $amount > 0 && $from_rate > 0 ) {
		$currency  = eac_get_currency( $to );
		$precision = $currency ? $currency->get_precision() : 2;
		$amount    = round( $amount / $from_rate, $precision, PHP_ROUND_HALF_UP );
	}

	if ( $amount > 0 && $to_rate > 0 ) {
		$currency  = eac_get_currency( $to );
		$precision = $currency ? $currency->get_precision() : 2;
		$amount    = round( $amount * $to_rate, $precision, PHP_ROUND_HALF_UP );
	}

	if ( $formatted ) {
		$amount = eac_format_money( $amount, $to );
	}

	return $amount;
}

/**
 * Add notice.
 *
 * @param string $message Message.
 * @param string $type Type.
 * @param array  $args Args.
 *
 * @since 1.1.6
 *
 * @return void
 */
function eac_add_notice( $message, $type = 'success', $args = array() ) {
	\EverAccounting\Notices::add_notice( $message, $type, $args );
}

/**
 * Add message.
 *
 * @param string $message Message.
 * @param string $type Type.
 * @param array  $args Args.
 *
 * @since 1.1.6
 *
 * @return void
 */
function eac_add_message( $message, $type = 'success', $args = array() ) {
	\EverAccounting\Notices::add_message( $message, $type, $args );
}

/**
 * Get payment methods.
 *
 * @since 1.0.2
 * @return array
 */
function eac_get_payment_methods() {
	return apply_filters(
		'ever_accounting_payment_methods',
		array(
			'cash'          => esc_html__( 'Cash', 'wp-ever-accounting' ),
			'check'         => esc_html__( 'Cheque', 'wp-ever-accounting' ),
			'credit_card'   => esc_html__( 'Credit Card', 'wp-ever-accounting' ),
			'debit_card'    => esc_html__( 'Debit Card', 'wp-ever-accounting' ),
			'bank_transfer' => esc_html__( 'Bank Transfer', 'wp-ever-accounting' ),
			'paypal'        => esc_html__( 'PayPal', 'wp-ever-accounting' ),
			'other'         => esc_html__( 'Other', 'wp-ever-accounting' ),
		)
	);
}

/**
 * Get formatted company address.
 *
 * @since 1.1.6
 * @return string
 */
function eac_get_formatted_company_address() {
	return eac_get_formatted_address(
		array(
			'name'      => get_option( 'eac_business_name', get_bloginfo( 'name' ) ),
			'address_1' => get_option( 'eac_business_address_1' ),
			'address_2' => get_option( 'eac_business_address_2' ),
			'city'      => get_option( 'eac_business_city' ),
			'state'     => get_option( 'eac_business_state' ),
			'postcode'  => get_option( 'eac_business_postcode' ),
			'country'   => get_option( 'eac_business_country' ),
			'phone'     => get_option( 'eac_business_phone' ),
			'email'     => get_option( 'eac_business_email' ),
		)
	);
}

/**
 * Form field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.1.6
 * @return void|string String when 'return' argument is passed as true.
 */
function eac_form_field( $field ) {
	$default_args = array(
		'type'        => 'text',
		'name'        => '',
		'id'          => '',
		'label'       => '',
		'desc'        => '',
		'tooltip'     => '',
		'placeholder' => '',
		'required'    => false,
		'readonly'    => false,
		'disabled'    => false,
		'autofocus'   => false,
		'class'       => '',
		'style'       => '',
		'input_class' => '',
		'input_style' => '',
		'options'     => [],
		'attrs'       => [],
		'default'     => '',
		'suffix'      => '',
		'prefix'      => '',
	);

	$field = wp_parse_args( $field, $default_args );

	/**
	 * Filter the arguments of a form field before it is rendered.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.1.6
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
	foreach ( [ 'readonly', 'disabled', 'required', 'autofocus' ] as $attr_key ) {
		if ( isset( $field[ $attr_key ] ) && ! empty( $field[ $attr_key ] ) ) {
			$field['attrs'][ $attr_key ] = $attr_key;
		}
	}
	if ( ! empty( $field['input_style'] ) ) {
		$field['attrs']['style'] = $field['input_style'];
	}
	if ( ! empty( $field['placeholder'] ) ) {
		$field['attrs']['placeholder'] = $field['placeholder'];
	}
	foreach ( $field['attrs'] as $attr_key => $attr_value ) {
		if ( empty( $attr_key ) || empty( $attr_value ) ) {
			continue;
		}
		$attrs[] = esc_attr( $attr_key ) . '="' . esc_attr( $attr_value ) . '"';
	}

	switch ( $field['type'] ) {
		case 'text':
		case 'email':
		case 'number':
		case 'password':
		case 'hidden':
		case 'url':
			$input = sprintf(
				'<input type="%1$s" name="%2$s" id="%3$s" class="%4$s" value="%5$s" %6$s>',
				esc_attr( $field['type'] ),
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['input_class'] ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;

		case 'textarea':
			$rows  = ! empty( $field['rows'] ) ? absint( $field['rows'] ) : 4;
			$cols  = ! empty( $field['cols'] ) ? absint( $field['cols'] ) : 50;
			$input = sprintf(
				'<textarea name="%s" id="%s" class="%s" placeholder="%s" rows="%s" cols="%s" %s>%s</textarea>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['input_class'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $rows ),
				esc_attr( $cols ),
				implode( ' ', $attrs ),
				esc_textarea( $field['value'] )
			);
			break;

		case 'currency':
		case 'country':
		case 'select':
			$field['value']       = wp_parse_list( $field['value'] );
			$field['value']       = array_map( 'strval', $field['value'] );
			$field['placeholder'] = ! empty( $field['placeholder'] ) ? $field['placeholder'] : __( 'Select an option&hellip;', 'wp-ever-accounting' );
			if ( ! empty( $field['multiple'] ) ) {
				$field['name'] .= '[]';
				$attrs[]       = 'multiple="multiple"';
			}

			if ( 'currency' === $field['type'] ) {
				foreach ( eac_get_currencies() as $code => $currency ) {
					$field['options'][ $code ] = sprintf( '%s (%s)', $currency['name'], $currency['symbol'] );
				}
			} elseif ( 'country' === $field['type'] ) {
				$field['options'] = eac_get_countries();
			}

			$options = sprintf( '<option value="">%s</option>', esc_html( $field['placeholder'] ) );
			foreach ( $field['options'] as $value => $option_label ) {
				$options .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $value ), selected( in_array( (string) $value, $field['value'], true ), true, false ), esc_html( $option_label ) );
			}

			$input = sprintf(
				'<select name="%s" id="%s" class="%s" %s>%s</select>',
				$field['name'],
				$field['id'],
				esc_attr( $field['input_class'] ),
				implode( ' ', $attrs ),
				$options
			);
			break;

		case 'wp_editor':
			$settings = isset( $field['settings'] ) ? $field['settings'] : array();
			ob_start();
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
			$input = ob_get_clean();
			break;

		case 'radio':
			$input = '';
			if ( ! empty( $field['options'] ) ) {
				$input = '<fieldset class="eac-form-field__radios">';
				foreach ( $field['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = checked( $option_key, $field['value'], false );
					$input      .= sprintf(
						'<label><input type="radio" name="%1$s" id="%2$s" class="%3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $field['name'] ),
						esc_attr( $field['id'] . '-' . $option_key ),
						esc_attr( $field['input_class'] ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
				$input .= '</fieldset>';
			}
			break;

		case 'checkbox':
			$input = sprintf(
				'<label><input type="checkbox" name="%1$s" id="%2$s" class="%3$s" value="1" %4$s %5$s>%6$s</label>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['input_class'] ),
				checked( $field['value'], 'yes', false ),
				wp_kses_post( implode( ' ', $attrs ) ),
				wp_kses_post( $field['desc'] )
			);

			break;

		case 'date':
		case 'time':
			$field['input_class'] .= ' eac-datepicker';
			$input                = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="%3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['input_class'] ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;

		case 'decimal':
		case 'money':
			$field['input_class'] .= ' eac_input_decimal';
			$input                = sprintf(
				'<input type="text" inputmode="decimal" name="%1$s" id="%2$s" class="%3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['input_class'] ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;

		case 'file':
			$field['input_class'] .= ' eac_input_file';
			$allowed_types        = ! empty( $field['allowed_types'] ) ? $field['allowed_types'] : 'image';
			$input                = sprintf(
				'<input type="file" name="%1$s" id="%2$s" class="%3$s" value="%4$s" %5$s accept="%6$s">',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['input_class'] ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) ),
				esc_attr( $allowed_types )
			);
			break;
		default:
			$input = '';
			break;
	}

	if ( ! empty( $field['prefix'] ) || ! empty( $field['suffix'] ) && ! empty( $input ) ) {
		if ( ! empty( $field['prefix'] ) && ! preg_match( '/<[^>]+>/', $field['prefix'] ) ) {
			$field['prefix'] = '<span class="eac-form-field__addon">' . $field['prefix'] . '</span>';
		}

		if ( ! empty( $field['suffix'] ) && ! preg_match( '/<[^>]+>/', $field['suffix'] ) ) {
			$field['suffix'] = '<span class="eac-form-field__addon">' . $field['suffix'] . '</span>';
		}
		$input = sprintf(
			'<div class="eac-form-field__group">%s%s%s</div>',
			$field['prefix'],
			$input,
			$field['suffix']
		);
	}

	if ( ! empty( $input ) ) {
		if ( ! empty( $field['label'] ) ) {
			$label = '<label for="' . esc_attr( $field['id'] ) . '" class="eac-form-field__label">' . esc_html( $field['label'] );
			if ( true === $field['required'] ) {
				$label .= '&nbsp;<abbr class="eac-form-field__required" title="' . esc_attr__( 'required', 'wp-ever-accounting' ) . '">*</abbr>';
			}
			if ( ! empty( $field['tooltip'] ) ) {
				$label .= eac_tooltip( $field['tooltip'] );
			}
			$label .= '</label>';
			$input = $label . $input;
		}

		if ( ! empty( $field['desc'] ) && ! in_array( $field['type'], array( 'checkbox', 'switch' ), true ) ) {
			$input .= '<p class="eac-form-field__desc">' . esc_html( $field['desc'] ) . '</p>';
		}

		$input = sprintf(
			'<div class="eac-form-field field-%1$s %2$s" id="field-%3$s" style="%4$s">%5$s</div>',
			esc_attr( $field['type'] ),
			esc_attr( $field['class'] ),
			esc_attr( $field['id'] ),
			esc_attr( $field['style'] ),
			$input
		);
	}

	/**
	 * Filter the output of the field.
	 *
	 * @param string $output The field HTML.
	 * @param array  $field The field arguments.
	 */
	echo apply_filters( 'ever_accounting_form_field_html', $input, $field ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
