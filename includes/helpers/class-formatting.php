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
}
