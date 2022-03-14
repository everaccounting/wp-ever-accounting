<?php
namespace Ever_Accounting\Helpers;

class Formatting {

	/**
	 * Converts a string (e.g. 'yes' or 'no') to a bool.
	 *
	 * @since 1.0.2
	 *
	 * @param string|boolean $string String to convert.
	 *
	 * @return bool
	 */
	public static function string_to_bool( $string ) {
		return is_bool( $string ) ? $string : ( in_array( strtolower( $string ), array( 'yes', 'true', 'active', 'enabled' ), true ) || 1 === $string || '1' === $string );
	}

	/**
	 * Converts a bool to a 'yes' or 'no'.
	 *
	 * @since 1.0.2
	 *
	 * @param bool $bool String to convert.
	 *
	 * @return string
	 */
	public static function bool_to_string( $bool ) {
		if ( ! is_bool( $bool ) ) {
			$bool = self::string_to_bool( $bool );
		}

		return true === $bool ? 'yes' : 'no';
	}

	/**
	 * Converts a bool to a 1 or 0.
	 *
	 * @since 1.1.0
	 *
	 * @param $bool
	 *
	 * @return int
	 */
	public static function bool_to_number( $bool ) {
		if ( ! is_bool( $bool ) ) {
			$bool = self::string_to_bool( $bool );
		}

		return true === $bool ? 1 : 0;
	}

	/**
	 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
	 * Non-scalar values are ignored.
	 *
	 * @since 1.0.2
	 *
	 * @param string|array $var Data to sanitize.
	 *
	 * @return string|array
	 */
	public static function clean( $var ) {
		if ( is_array( $var ) ) {
			return array_map( 'self::clean', $var );
		}

		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}

	/**
	 * Get only numbers from the string.
	 *
	 * @param string $number Number
	 * @param bool $allow_decimal
	 *
	 * @return int|float|null
	 * @since 1.0.2
	 */
	public static function sanitize_number( $number, $allow_decimal = true ) {
		// Convert multiple dots to just one.
		$number = preg_replace( '/\.(?![^.]+$)|[^0-9.-]/', '', eaccounting_clean( $number ) );

		if ( $allow_decimal ) {
			return (float) preg_replace( '/[^0-9.-]/', '', $number );
		}

		return (int) preg_replace( '/[^0-9]/', '', $number );
	}

	/**
	 * Get only numbers from the string.
	 *
	 * @param      $number
	 *
	 * @param int  $decimals
	 * @param bool $trim_zeros
	 *
	 * @return int|float|null
	 * @since 1.0.2
	 */
	public static function format_decimal( $number, $decimals = 4, $trim_zeros = false ) {

		// Convert multiple dots to just one.
		$number = preg_replace( '/\.(?![^.]+$)|[^0-9.-]/', '', self::clean( $number ) );

		if ( is_numeric( $decimals ) ) {
			$number = number_format( floatval( $number ), $decimals, '.', '' );
		}

		if ( $trim_zeros && strstr( $number, '.' ) ) {
			$number = rtrim( rtrim( $number, '0' ), '.' );
		}

		return $number;
	}

	/**
	 * Sanitize a string destined to be a tooltip.
	 *
	 * Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
	 *
	 * @param string $var Data to sanitize.
	 *
	 * @return string
	 * @since  1.0.2
	 */
	public static function sanitize_tooltip( $var ) {
		return htmlspecialchars(
			wp_kses(
				html_entity_decode( $var ),
				array(
					'br'     => array(),
					'em'     => array(),
					'strong' => array(),
					'small'  => array(),
					'span'   => array(),
					'ul'     => array(),
					'li'     => array(),
					'ol'     => array(),
					'p'      => array(),
				)
			)
		);
	}

	/**
	 * Implode and escape HTML attributes for output.
	 *
	 * @param array $raw_attributes Attribute name value pairs.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public static function implode_html_attributes( $raw_attributes ) {
		$attributes     = array();
		$raw_attributes = array_filter( $raw_attributes );
		foreach ( $raw_attributes as $name => $value ) {
			$attributes[] = esc_attr( $name ) . '="' . esc_attr( trim( $value ) ) . '"';
		}

		return implode( ' ', $attributes );
	}

	/**
	 * Display help tip.
	 *
	 * @param bool   $allow_html Allow sanitized HTML if true or escape.
	 *
	 * @param string $tip        Help tip text.
	 *
	 * @return string
	 * @since  1.0.2
	 */
	public static function help_tip( $tip, $allow_html = false ) {
		if ( $allow_html ) {
			$tip = self::sanitize_tooltip( $tip );
		} else {
			$tip = esc_attr( $tip );
		}

		return '<span class="ea-help-tip" title="' . $tip . '"></span>';
	}

	/**
	 * EverAccounting date format - Allows to change date format for everything.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public static function date_format() {
		return apply_filters( 'eaccounting_date_format', ever_accounting_get_option( 'date_format', 'Y-m-d' ) );
	}

	/**
	 * Format a date for output.
	 *
	 * @since 1.1.0
	 *
	 * @param string $format
	 * @param        $date
	 *
	 * @return string
	 */
	public static function date( $date, $format = '' ) {

		if ( empty( $date ) || '0000-00-00 00:00:00' === $date || '0000-00-00' === $date ) {
			return '';
		}

		if ( ! $format ) {
			$format = self::date_format();
		}

		if ( ! is_numeric( $date ) ) {
			$date = strtotime( $date );
		}

		return date_i18n( $format, $date );
	}

	/**
	 * Format address.
	 *
	 * @param array $address Address params.
	 * @param string $break Break.
	 *
	 * @return string
	 * @since 1.1.0
	*/
	public static function format_address( $address, $break = '<br>' ) {
		$address   = wp_parse_args(
			$address,
			array(
				'street'   => '',
				'city'     => '',
				'state'    => '',
				'postcode' => '',
				'country'  => '',
			)
		);
		$countries = Misc::get_countries();
		if ( ! empty( $address['country'] ) && isset( $countries[ $address['country'] ] ) ) {
			$address['country'] = $countries[ $address['country'] ];
		}

		$line_1       = $address['street'];
		$line_2       = implode( ' ', array_filter( array( $address['city'], $address['state'], $address['postcode'] ) ) );
		$line_3       = $address['country'];
		$full_address = array_filter( array( $line_1, $line_2, $line_3 ) );

		return implode( $break, $full_address );
	}
}
