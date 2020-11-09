<?php

namespace EverAccounting\Accounts;

class Acc {

	/**
	 * Acc constructor.
	 */
	public function __construct( $data = '' ) {
		if ( $data instanceof Acc ) {
			$this->set_id( absint( $data->get_id() ) );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		} elseif ( is_object( $data ) && ! empty( $data->id ) ) {
			$this->set_id( $data->id );
			$this->set_props( (array) $data );
			$this->set_object_read( true );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = \WC_Data_Store::load( 'admin-note' );
		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}
	}
}
