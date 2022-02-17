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
			return array_map( 'eaccounting_clean', $var );
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
}
