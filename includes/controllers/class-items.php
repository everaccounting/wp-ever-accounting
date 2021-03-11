<?php
namespace EverAccounting\Controllers;

/**
 * Class Items
 * @package EverAccounting\Controllers
 */
class Items{

	/**
	 * Items constructor.
	 */
	public function __construct() {
	}

	/**
	 * @param $item
	 * @param null $by
	 */
	public function get( $item, $by = null){
		if ( empty( $item ) ) {
			return null;
		}

		global $wpdb;
		if( !is_null( $by ) ){
			switch ($by){
				case 'sku':
					$item = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ea_itesm WHERE sku=%s", eaccounting_clean( $item ) ) );
				break;
			}
		}
	}




}
