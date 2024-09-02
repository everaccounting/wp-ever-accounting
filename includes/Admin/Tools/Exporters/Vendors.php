<?php
/**
 * Handle vendors export.
 *
 * @since 1.0.2
 *
 * @package EverAccounting\Admin\Exporters
 */

namespace EverAccounting\Admin\Tools\Exporters;

use EverAccounting\Models\Vendor;
use function EverAccounting\Admin\Exporters\eac_get_input_var;

defined( 'ABSPATH' ) || exit;

/**
 * Class Vendors.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Admin\Exporters
 */
class Vendors extends CSVExporter {
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
	 * @since  1.0.2
	 * @return array
	 */
	public function get_columns() {
		return array(
			'id',
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
			'status',
		);
	}

	/**
	 * Get rows.
	 *
	 * @since 1.0.2
	 */
	public function get_rows() {
		$args  = array(
			'per_page' => $this->limit,
			'page'     => $this->page,
			'status'   => eac_get_input_var( 'status', '', 'POST' ),
			'orderby'  => 'id',
			'order'    => 'ASC',
			'limit'    => - 1,
		);

		$args  = apply_filters( 'ever_accounting_vendors_export_query_args', $args );

		$items = Vendor::query( $args );

		$rows  = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}

	/**
	 * Take an item and generate row data from it for export.
	 *
	 * @param \EverAccounting\Models\Vendor $item Vendor object.
	 *
	 * @return array
	 */
	protected function generate_row_data( $item ) {
		$props = [];
		foreach ( $this->get_columns() as $column ) {
			$value = null;
			switch ( $column ) {
				default:
					$value  = '';
					if ( $item->$column ) {
						$value = $item->$column;
					}
					$value = apply_filters( 'ever_accounting_vendors_export_column_' . $column, $value, $item );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
