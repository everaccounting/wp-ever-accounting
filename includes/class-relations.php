<?php

class Ever_Accounting_Relations {


	public function __construct() {
		add_action( 'init', array( $this, 'register_relations' ) );
	}


	public function register_relations() {
		$relations = array();

	}

	public function register_relation( $relation_name ) {
		global $ever_accounting_relations;
		if ( ! is_array( $ever_accounting_relations ) ) {
			$ever_accounting_relations = array();
		}

		// Sanitize relation name.
		$relation_name = sanitize_key( $relation_name );

		/**
		 * Fires after a relation is registered.
		 *
		 * @param string $relation_name Relation name.
		 *
		 * @since 1.1.4
		 */
		do_action( 'ever_accounting_register_relation', $relation_name );

		return $relation_name;
	}

}
