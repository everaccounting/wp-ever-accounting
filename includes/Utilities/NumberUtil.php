<?php

namespace EverAccounting\Utilities;

defined('ABSPATH') || exit;

/**
 * Class NumberUtil
 *
 * @since 1.0.0
 * @package EverAccounting\Utilities
 */
class NumberUtil {

	/**
	 * Convert a number to K.
	 *
	 * @param float $number Number to convert.
	 *
	 * @return string
	 */
	public static function number_to_k($number) {
		if ($number > 999) {
			return round($number / 1000, 2) . 'K';
		}

		return $number;
	}
}
