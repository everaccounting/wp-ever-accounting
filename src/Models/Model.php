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
	 * Magic call function.
	 *
	 * @param string $method Method name.
	 * @param array  $args Method arguments.
	 *
	 *
	 * @return mixed
	 */
	public function __call( $method, $args = array() ) {
		if ( str_starts_with( 'get_', $method ) ) {
			$attribute = substr( $method, 4 );
			if ( $this->has_attribute( $attribute ) ) {
				return $this->get_attribute( $attribute );
			}
		}
	}

	/**
	 * Get hook prefix.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_hook_prefix() {
		return 'ever_accounting_' . $this->get_object_type();
	}
}
