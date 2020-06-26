<?php
defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Query_Builder
 */
class EAccounting_Transaction_Model extends EAccounting_Query_Builder {
	protected $table = 'ea_transactions';

	/**
	 * @param $name
	 *
	 * @return EAccounting_Query_Builder
	 */
	public static function table( $name ) {
		$self              = ( new self() );
		$self->table       = $self->table_prefix . $name;
		$self->whereValues = [];

		return $self;
	}
}
