<?php
/**
 * Handle vendors export.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */

namespace EverAccounting\Export;

use EverAccounting\Abstracts\CSV_Exporter;

defined( 'ABSPATH' ) || exit();

/**
 * Class Vendors
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */
class Vendors extends CSV_Exporter {
	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'vendors';

	/**
	 * Return an array of columns to export.
	 *
	 * @return array
	 * @since  1.0.2
	 */
	public function get_columns() {
		return eaccounting_get_io_headers( 'vendor' );
	}

	/**
	 * Get export data.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_rows() {
		$args  = array(
			'per_page' => $this->limit,
			'page'     => $this->page,
			'orderby'  => 'id',
			'order'    => 'ASC',
			'type'     => 'vendor',
			'return'   => 'objects',
			'number'   => 0,
		);
		$args  = apply_filters( 'eaccounting_vendor_export_query_args', $args );
		$items = eaccounting_get_contacts( $args );
		$rows  = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}


	/**
	 * Take a vendor and generate row data from it for export.
	 *
	 * @param \EverAccounting\Contact $item Contact
	 *
	 * @return array
	 */
	protected function generate_row_data( $item ) {
		$props = [];
		foreach ( $this->get_columns() as $column => $label ) {
			$value = null;
			switch ( $column ) {
				case 'name':
					$value = $item->name;
					break;
				case 'company':
					$value = $item->company;
					break;
				case 'email':
					$value = $item->email;
					break;
				case 'phone':
					$value = $item->phone;
					break;
				case 'birth_date':
					$value = $item->birth_date;
					break;
				case 'street':
					$value = $item->street;
					break;
				case 'city':
					$value = $item->city;
					break;
				case 'state':
					$value = $item->street;
					break;
				case 'postcode':
					$value = $item->postcode;
					break;
				case 'country':
					$value = $item->get_country_nicename();
					break;
				case 'website':
					$value = $item->website;
					break;
				case 'vat_number':
					$value = $item->vat_number;
					break;
				case 'currency_code':
					$value = $item->currency_code;
					break;
				default:
					$value = apply_filters( 'eaccounting_vendor_csv_row_item', '', $column, $item, $this );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
