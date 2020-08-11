<?php
/**
 * Utilities class for EverAccounting.
 *
 * @package EverAccounting
 * @since 1.0.2
 *
 */

namespace EverAccounting;

use EverAccounting\Utilities\Data;

defined( 'ABSPATH' ) || exit();

/**
 * Class Utilities
 * @since 1.0.2
 * @package EverAccounting
 */
class Utilities {
	/**
	 * Temporary data storage class instance variable.
	 *
	 * @since  1.0.2
	 * @var    Data
	 */
	public $data;

	/**
	 * Instantiates the utilities class.
	 *
	 * @access public
	 * @since  1.0.2
	 */
	public function __construct() {
		$this->includes();
		$this->setup_objects();
	}

	/**
	 * Includes necessary utility files.
	 *
	 * @access public
	 * @since  1.0.2
	 */
	public function includes() {
		require_once EACCOUNTING_ABSPATH . '/includes/utilities/class-ea-data.php';
	}

	/**
	 * Sets up utility objects.
	 *
	 * @access public
	 * @since  1.0.2
	 */
	public function setup_objects() {
		$this->data = new Data();
	}

}
