<?php

namespace EverAccounting\Export;

defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\CSV_Batch_Exporter;
use EverAccounting\Query_Contact;

class Customer_CSV_Export extends CSV_Batch_Exporter {

	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'customers';


	/**
	 * Return an array of columns to export.
	 *
	 * @return array
	 * @since  1.0.2
	 */
	public function get_csv_columns() {
		return array(
			'name'          => __( 'Name', 'wp-ever-accounting' ),
			'email'         => __( 'Email', 'wp-ever-accounting' ),
			'phone'         => __( 'Phone', 'wp-ever-accounting' ),
			'fax'           => __( 'Fax', 'wp-ever-accounting' ),
			'birth_date'    => __( 'Birth Date', 'wp-ever-accounting' ),
			'address'       => __( 'Address', 'wp-ever-accounting' ),
			'country'       => __( 'Country', 'wp-ever-accounting' ),
			'website'       => __( 'Website', 'wp-ever-accounting' ),
			'tax_number'    => __( 'Tax Number', 'wp-ever-accounting' ),
			'currency_code' => __( 'Currency Code', 'wp-ever-accounting' ),
			'note'          => __( 'Note', 'wp-ever-accounting' ),
		);
	}

	/**
	 *
	 * @since 1.0.2
	 */
	public function set_data() {
		$args              = array(
			'per_page' => $this->get_limit(),
			'page'     => $this->get_page(),
			'orderby'  => 'id',
			'order'    => 'ASC',
			'type'     => 'customer',
		);
		$query             = Query_Contact::init()->where( $args );
		$items             = $query->get( OBJECT, 'eaccounting_get_contact' );
		$this->total_count = $query->count();
		$this->rows        = array();

		foreach ( $items as $item ) {
			$this->rows[] = $this->generate_row_data( $item );
		}
	}


	/**
	 * Take a product and generate row data from it for export.
	 *
	 *
	 * @param \EverAccounting\Contact $item
	 *
	 * @return array
	 */
	protected function generate_row_data( $item ) {
		$props = [];
		foreach ( $this->get_csv_columns() as $column => $label ) {
			$value = null;
			switch ( $column ) {
				case 'name':
					$value = $item->get_name();
					break;
				case 'email':
					$value = $item->get_email();
					break;
				case 'phone':
					$value = $item->get_phone();
					break;
				case 'fax':
					$value = $item->get_fax();
					break;
				case 'birth_date':
					$value = $item->get_birth_date();
					break;
				case 'address':
					$value = $item->get_address();
					break;
				case 'country':
					$value = $item->get_country();
					break;
				case 'website':
					$value = $item->get_website();
					break;
				case 'tax_number':
					$value = $item->get_tax_number();
					break;
				case 'currency_code':
					$value = $item->get_currency_code();
					break;
				case 'note':
					$value = $item->get_note();
					break;
				default:
					$value = apply_filters( 'eaccounting_customer_csv_row_item', '', $column, $item, $this );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
