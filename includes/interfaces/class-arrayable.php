<?php
/**
 * Interface arrayable loader.
 *
 * @since       1.0.0
 * @subpackage  Interfaces
 * @package     Ever_Accounting\Includes
 */

namespace Ever_Accounting\Interfaces;

defined('ABSPATH') || exit;

interface Arrayable {
	/**
	 * Returns object as string.
	 *
	 * @since 1.0.0
	 */
	public function __toArray();
	/**
	 * Returns object as string.
	 *
	 * @since 1.0.0
	 */
	public function to_array();
}