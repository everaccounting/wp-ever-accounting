<?php
/**
 * Controller various actions of the plugin.
 *
 * @package     EverAccounting
 * @subpackage  Classes
 * @version     1.1.0
 */

/**
 * Class EverAccounting_Controller
 * @since 1.1.0
 */
class EverAccounting_Controller {

	/**
	 * EverAccounting_Controller constructor.
	 */
	public function __construct() {
		//accounts

		//customers

		//vendors

		//category

		//currency

		//bill
		add_action( 'eaccounting_delete_payment', array( $this, 'update_bill' ), 10, 2 );
		add_action( 'eaccounting_update_payment', array( $this, 'update_bill' ), 10, 2 );
	}

	public static function update_bill( $payment_id, $payment ) {
		error_log(print_r($payment_id, true ));
		if( !empty( $payment->get_document_id() ) && $bill = eaccounting_get_bill($payment->get_document_id())){
			$bill->save();
		}
	}
}

new EverAccounting_Controller();
