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
use EverAccounting\Core\Repositories;
use EverAccounting\Repositories\Incomes;

defined( 'ABSPATH' ) || exit;

/**
 * Class Payment
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Income extends TransactionModel {
	/**
	 * Type of the contact.
	 */
	const TRANS_TYPE = 'income';

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = self::TRANS_TYPE;

	/**
	 * Get the expense if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.0.2
	 *
	 * @param int|object|Expense $data object to read.
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} elseif ( is_array( $data ) ) {
			$this->set_props( $data );
		} else {
			$this->set_object_read( false );
		}

		//Load repository
		$this->repository = Repositories::load( 'transaction-income' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}

		// If not vendor then reset to default
		if ( self::TRANS_TYPE !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}
}
