<?php
/**
 * Handle the payment object.
 *
 * @package     EverAccounting\Models
 * @class       Payment
 * @version     1.0.2
 */
namespace EverAccounting\Models;

use EverAccounting\Abstracts\TransactionModel;
use EverAccounting\Repositories\Payments;

defined( 'ABSPATH' ) || exit;

/**
 * Class Payment
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Payment extends TransactionModel {

	/**
	 * Payment constructor.
	 */
	public function __construct( $data = 0 ) {
		$this->repository = Payments::instance();
		parent::__construct( $data );

		if ( $this->get_id() > 0 && ! $this->get_object_read() ) {
			$payment = Payments::instance()->get( $this->get_id() );
			if ( $payment && 'expense' === $payment->get_type() ) {
				$this->set_props( $payment->get_data() );
				$this->set_object_read( $payment->exists() );
			} else {
				$this->set_id( 0 );
			}
		}
	}

}
