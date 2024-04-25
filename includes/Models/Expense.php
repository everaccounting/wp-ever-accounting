<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Expense model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 */
class Expense extends Transaction {
	/**
	 * Object type in singular form.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'expense';
}
