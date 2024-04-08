<?php
/**
 * Interface arrayable loader.
 *
 * @since       1.0.0
 * @subpackage  Interfaces
 * @package     EverAccounting\Legacy\Includes
 */

namespace EverAccounting\Legacy\Interfaces;

defined( 'ABSPATH' ) || exit;

interface Arrayable {
	/**
	 * Returns object as string.
	 *
	 * @since 1.0.0
	 */
	public function to_array();
}
