<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Query_Transactions extends EAccounting_Query {
	/**
	 * Static constructor.
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	public static function init( $id = null ) {
		$builder     = new self();
		$builder->id = ! empty( $id ) ? $id : uniqid();
		$builder->from( 'ea_transactions' );

		return $builder;
	}

	/**
	 * @since 1.0.2
	 * @return $this
	 */
	public function isNotTransfer(){
		global $wpdb;
		$this->whereRaw("category_id NOT IN(select id from {$wpdb->prefix}ea_categories where type='other')");
		return $this;
	}
}
