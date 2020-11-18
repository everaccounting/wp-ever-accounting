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
	 *
	 * @param int $data
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data, Vendors::instance() );

		// If not vendor then reset to default
		if ( 'vendor' !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

}
