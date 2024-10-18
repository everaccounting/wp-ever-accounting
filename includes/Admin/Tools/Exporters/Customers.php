<?php
/**
 * Handle customers export.
 *
 * @since 1.0.2
 *
 * @package EverAccounting\Admin\Exporters
 */

namespace EverAccounting\Admin\Tools\Exporters;

defined( 'ABSPATH' ) || exit();


/**
 * Class Customers.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Admin\Exporters
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
			'id',
			'type',
			'name',
			'company',
			'email',
			'phone',
			'website',
			'address_1',
			'address_2',
			'city',
			'state',
			'postcode',
			'country',
			'vat_number',
			'vat_exempt',
			'currency_code',
			'thumbnail_id',
			'user_id',
			'status',
			'created_via',
			'author_id',
			'uuid',
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

		$args = apply_filters( 'eac_export_customers_args', $args );

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
		$props = array();
		foreach ( $this->get_columns() as $column ) {
			switch ( $column ) {
				default:
					$value = '';
					if ( $item->$column ) {
						$value = $item->$column;
					}
					$value = apply_filters( 'eac_export_customers_column_' . $column, $value, $item );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
