<?php
/**
 * Utilities class for EAccounting.
 *
 * @since   1.0.2
 *
 * @package EAccounting
 */

namespace EAccounting;

use EAccounting\Utilities\Data;
use EAccounting\Utilities\Defaults;
use EAccounting\Utilities\Batch;

defined( 'ABSPATH' ) || exit();

/**
 * Class Utilities
 *
 * @since   1.0.2
 * @package EAccounting
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
	 * Storage for holding default company data.
	 *
	 * @since 1.0.2
	 * @var Defaults
	 */
	public $defaults;

	/**
	 * Batch processing class instance variable.
	 *
	 * @since 1.0.2
	 * @var Batch
	 */
	public $batch;

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
	 * @since  1.0.2
	 */
	public function includes() {
		require_once EACCOUNTING_ABSPATH . '/includes/utilities/class-data.php';
		require_once EACCOUNTING_ABSPATH . '/includes/utilities/class-batch.php';
		require_once EACCOUNTING_ABSPATH . '/includes/utilities/class-defaults.php';
	}

	/**
	 * Sets up utility objects.
	 *
	 * @since  1.0.2
	 */
	public function setup_objects() {
		$this->data     = new Data();
		$this->defaults = new Defaults();
		$this->batch    = new Batch();
	}

}
