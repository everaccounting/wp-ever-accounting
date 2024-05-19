<?php

namespace EverAccounting\Models;

/**
 * Abstract class Model.
 *
 * @since 1.2.0
 * @package EverAccounting
 * @subpackage Models
 */
abstract class Model extends \ByteKit\Models\Model {
	/**
	 * Get hook prefix. Default is the object type.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_hook_prefix() {
		return 'ever_accounting_' . $this->get_object_type();
	}
}
