<?php
/**
 * Handle customers export.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */

namespace EverAccounting\Admin\Exporters;

defined( 'ABSPATH' ) || exit();


/**
 * Class Customers
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */
class Customers extends CSVExporter {

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
		return array(
			'name',
			'company',
			'email',
			'phone',
			'birth_date',
			'street',
			'city',
			'state',
			'postcode',
			'country',
			'website',
			'vat_number',
			'currency_code',
			'type',
			'status',
		);
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

		$args = apply_filters( 'ever_accounting_export_customers_args', $args );

		$items = eac_get_customers( $args );

		$rows = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}


	/**
	 * Take a customer and generate row data from it for export.
	 *
	 * @param \EverAccounting\Models\Customer $item Customer object.
	 *
	 * @return array
	 */
	protected function generate_row_data( $item ) {
		$props = [];
		foreach ( $this->get_columns() as $column ) {
			switch ( $column ) {
				default:
					$getter = 'get_' . $column;
					$value  = '';
					if ( method_exists( $item, $getter ) ) {
						$value = $item->$getter( 'edit' );
					}
					$value = apply_filters( 'ever_accounting_account_export_column_' . $column, $value, $item );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}