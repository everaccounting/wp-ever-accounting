<?php
/**
 * Handle customers export.
 *
 * @since   1.0.2
 *
 * @package EAccounting\Export
 */

namespace EAccounting\Export;

use EAccounting\Abstracts\CSV_Exporter;

defined( 'ABSPATH' ) || exit();


/**
 * Class Customers
 *
 * @since   1.0.2
 *
 * @package EAccounting\Export
 */
class Customers extends CSV_Exporter {

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
	public function get_columns() {
		return eaccounting_get_io_headers( 'customer' );
	}

	/**
	 * Get export data.
	 *
	 * @return array
	 * @since 1.0.
	 */
	public function get_rows() {
		$args = array(
			'per_page' => $this->limit,
			'page'     => $this->page,
			'orderby'  => 'id',
			'order'    => 'ASC',
			'type'     => 'customer',
			'return'   => 'objects',
			'number'   => - 1,
		);

		$args = apply_filters( 'eaccounting_customer_export_query_args', $args );

		$items = eaccounting_get_customers( $args );

		$rows = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}


	/**
	 * Take a customer and generate row data from it for export.
	 *
	 * @param \EAccounting\Models\Customer $item Customer object.
	 *
	 * @return array
	 */
	protected function generate_row_data( $item ) {
		$props = [];
		foreach ( $this->get_columns() as $column => $label ) {
			switch ( $column ) {
				case 'name':
					$value = $item->get_name();
					break;
				case 'company':
					$value = $item->get_company();
					break;
				case 'email':
					$value = $item->get_email();
					break;
				case 'phone':
					$value = $item->get_phone();
					break;
				case 'birth_date':
					$value = $item->get_birth_date();
					break;
				case 'street':
					$value = $item->get_street();
					break;
				case 'city':
					$value = $item->get_city();
					break;
				case 'state':
					$value = $item->get_street();
					break;
				case 'postcode':
					$value = $item->get_postcode();
					break;
				case 'country':
					$value = $item->get_country_nicename();
					break;
				case 'website':
					$value = $item->get_website();
					break;
				case 'vat_number':
					$value = $item->get_vat_number();
					break;
				case 'currency_code':
					$value = $item->get_currency_code();
					break;
				default:
					$value = apply_filters( 'eaccounting_customer_csv_row_item', '', $column, $item, $this );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
