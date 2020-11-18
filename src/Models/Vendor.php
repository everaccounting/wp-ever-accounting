<?php
/**
 * Handle the vendor object.
 *
 * @package     EverAccounting\Models
 * @class       Customer
 * @version     1.0.2
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ContactModel;
use EverAccounting\Repositories\Vendors;

defined( 'ABSPATH' ) || exit;

/**
 * Class Vendor
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Vendor extends ContactModel {

	/**
	 * Customer constructor.
	 */
	public function __construct( $data = 0 ) {
		$this->repository = Vendors::instance();
		parent::__construct( $data );
	}

}
