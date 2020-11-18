<?php
/**
 * Handle the revenue object.
 *
 * @package     EverAccounting\Models
 * @class       Revenue
 * @version     1.0.2
 */
namespace EverAccounting\Models;

use EverAccounting\Abstracts\TransactionModel;
use EverAccounting\Repositories\Revenues;

defined( 'ABSPATH' ) || exit;

/**
 * Class Revenue
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Revenue extends TransactionModel {

	/**
	 * Payment constructor.
	 */
	public function __construct( $data = 0 ) {
		$this->repository = Revenues::instance();
		parent::__construct( $data );

		if ( $this->get_id() > 0 && ! $this->get_object_read() ) {
			$revenue = Revenues::instance()->get( $this->get_id() );
			if ( $revenue && 'income' === $revenue->get_type() ) {
				$this->set_props( $revenue->get_data() );
				$this->set_object_read( $revenue->exists() );
			} else {
				$this->set_id( 0 );
			}
		}
	}

}
