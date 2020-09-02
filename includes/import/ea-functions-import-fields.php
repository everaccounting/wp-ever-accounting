<?php
/**
 * Mapping functions
 *
 * @since       1.0.2
 * @subpackage  Abstracts
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

function eaccounting_importer_customer_fields( $fields = array() ) {
	$mappings = array(
		'Name'          => 'name',
		'Email'         => 'email',
		'Phone'         => 'phone',
		'Fax'           => 'fax',
		'Birthday'      => 'birth_date',
		'Address'       => 'address',
		'Country'       => 'country',
		'Website'       => 'website',
		'Tax Number'    => 'tax_number',
		'Currency Code' => 'currency_code',
		'Note'          => 'note',
	);

	return array_merge( $fields, $mappings );
}
