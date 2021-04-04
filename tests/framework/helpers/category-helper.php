<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Category_Helper {

	public static function create_category( $save = true, $props = array() ) {
		$default = array(
			'name'         => 'Income',
			'type'         => 'income',
			'color'        => '#e6e6e6',
			'enabled'      => true,
			'date_created' => date( 'Y-m-d' ),
		);
		$props   = array_merge( $default, $props );
		if ( $save ) {
			return eaccounting_insert_category( $props );
		}

		return $props;
	}

}
