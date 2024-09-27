<?php
/**
 * Handle accounts export.
 *
 * @since 1.0.2
 *
 * @package EverAccounting\Admin\Exporters
 */

namespace EverAccounting\Admin\Tools\Exporters;

use function EverAccounting\Admin\Exporters\eac_get_input_var;

defined( 'ABSPATH' ) || exit;

/**
 * Class Accounts.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Admin\Exporters
 */
class Accounts extends CSVExporter {
	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'accounts';


	/**
	 * Return an array of columns to export.
	 *
	 * @since  1.0.2
	 * @return array
	 */
	public function get_columns() {
		return array(
			'id',
			'type',
			'name',
			'number',
			'opening_balance',
			'bank_name',
			'bank_phone',
			'bank_address',
			'currency_code',
			'author_id',
			'thumbnail_id',
			'balance',
			'status',
			'uuid',
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

		$args  = apply_filters( 'eac_accounts_export_query_args', $args );

		$items = eac_get_accounts( $args );

		$rows  = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}

	/**
	 * Take an item and generate row data from it for export.
	 *
	 * @param \EverAccounting\Models\Account $item Account object.
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
					$value = apply_filters( 'eac_accounts_export_column_' . $column, $value, $item );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
