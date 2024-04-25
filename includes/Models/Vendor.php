<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Vendor.
 *
 * @since   1.1.0
 * @package EAccounting\Models
 */
class Vendor extends Contact {
	/**
	 * Object type in singular form.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'vendor';
}
