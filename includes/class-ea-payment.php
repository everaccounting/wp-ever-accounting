<?php
/**
 * Handle the payment object.
 *
 * @package     EverAccounting
 * @class       Transaction
 * @version     1.0.2
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit();

/**
 * Class Payment
 *
 * @since   1.0.2
 * @package EverAccounting
 */
class Payment extends Transaction {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $object_type = 'payment';


}
