<?php

namespace EverAccounting\Admin\Exporters;

defined( 'ABSPATH' ) || exit;

/**
 * Handle export.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
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
			'name',
			'balance',
			'type',
			'number',
			'opening_balance',
			'bank_name',
			'bank_phone',
			'bank_address',
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
		$args  = apply_filters( 'ever_accounting_account_export_query_args', $args );
		$items = eac_get_accounts( $args );
		$rows  = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}


	/**
	 * Take a item and generate row data from it for export.
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