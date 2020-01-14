<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Ajax {

	/**
	 * EAccounting_Ajax constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_eaccounting_get_expense_by_category_chart', array( $this, 'expense_by_category_chart' ) );
		add_action( 'wp_ajax_eaccounting_get_income_by_category_chart', array( $this, 'income_by_category_chart' ) );
		add_action( 'wp_ajax_eaccounting_file_upload', array( $this, 'upload_file' ) );
		add_action( 'wp_ajax_eaccounting_get_invoice_total_item', array( $this, 'get_invoice_total_item' ) );
		add_action( 'wp_ajax_eaccounting_invoice_add_item', array( $this, 'invoice_add_item' ) );
	}

	public function expense_by_category_chart() {
		$this->verify_nonce( 'ea_expense_filter', 'nonce' );
		$this->check_permission();

		$period     = isset( $_REQUEST['period'] ) ? $_REQUEST['period'] : 'custom';
		$date_range = eaccounting_get_dates_from_period( $period );

		$start    = "{$date_range['year']}-{$date_range['m_start']}-{$date_range['day']}";
		$end      = "{$date_range['year_end']}-{$date_range['m_end']}-{$date_range['day_end']}";
		$expenses = eaccounting_get_expense_by_categories( $start, $end );

		$expense_response = [
			'labels'           => '',
			'background_color' => '',
			'data'             => ''
		];
		$expenses_labels  = [];
		$expenses_colors  = [];
		$expenses_data    = [];

		if ( ! empty( $expenses ) ) {
			foreach ( $expenses as $expense ) {
				$expenses_labels[] = sprintf( "%s - %s", html_entity_decode( eaccounting_price( $expense['total'] ) ), $expense['name'] );
				$expenses_colors[] = $expense['color'];
				$expenses_data[]   = $expense['total'];
			}
			$expense_response = [
				'labels'           => $expenses_labels,
				'background_color' => $expenses_colors,
				'data'             => $expenses_data
			];
		}
		$this->send_success( $expense_response );
	}

	public function income_by_category_chart() {
		$this->verify_nonce( 'ea_income_filter', 'nonce' );
		$this->check_permission();

		$period     = isset( $_REQUEST['period'] ) ? $_REQUEST['period'] : 'custom';
		$date_range = eaccounting_get_dates_from_period( $period );

		$start   = "{$date_range['year']}-{$date_range['m_start']}-{$date_range['day']}";
		$end     = "{$date_range['year_end']}-{$date_range['m_end']}-{$date_range['day_end']}";
		$incomes = eaccounting_get_income_by_categories( $start, $end );

		$income_response = [
			'labels'           => '',
			'background_color' => '',
			'data'             => ''
		];
		$incomes_labels  = [];
		$incomes_colors  = [];
		$incomes_data    = [];

		if ( ! empty( $incomes ) ) {
			foreach ( $incomes as $expense ) {
				$incomes_labels[] = sprintf( "%s - %s", html_entity_decode( eaccounting_price( $expense['total'] ) ), $expense['name'] );
				$incomes_colors[] = $expense['color'];
				$incomes_data[]   = $expense['total'];
			}
			$income_response = [
				'labels'           => $incomes_labels,
				'background_color' => $incomes_colors,
				'data'             => $incomes_data
			];
		}
		$this->send_success( $income_response );
	}

	/**
	 * @since 1.0.0
	 */
	public function upload_file() {
		$this->verify_nonce( 'eaccounting_file_upload', 'nonce' );
		$this->check_permission();
		$data = [
			'files' => [],
		];

		if ( ! empty( $_FILES ) ) {
			foreach ( $_FILES as $file_key => $file ) {
				$files_to_upload = eaccounting_prepare_uploaded_files( $file );
				foreach ( $files_to_upload as $file_to_upload ) {
					$uploaded_file = eaccounting_upload_file(
						$file_to_upload,
						[
							'file_key' => $file_key,
						]
					);

					if ( is_wp_error( $uploaded_file ) ) {
						$data['files'][] = [
							'error' => $uploaded_file->get_error_message(),
						];
					} else {
						$data['files'][] = $uploaded_file;
					}
				}
			}
		}

		wp_send_json( $data );

	}

	/**
	 * @since 1.0.0
	 */
	public function get_invoice_total_item() {
		$this->verify_nonce( 'invoice_total_item', 'nonce' );
		$this->check_permission();
		$input_items = isset( $_REQUEST['item'] ) ? $_REQUEST['item'] : [];
		$discount    = isset( $_REQUEST['discount'] ) ? (double) eaccounting_sanitize_price( $_REQUEST['discount'] ) : 00.00;
		$shipping    = isset( $_REQUEST['shipping'] ) ? (double) eaccounting_sanitize_price( $_REQUEST['shipping'] ) : 00.00;
		$result      = [];

		$sub_total      = 0;
		$tax_total      = 0;
		$discount_total = 0;

		if ( $input_items ) {
			foreach ( $input_items as $key => $item ) {

				$price           = (double) eaccounting_sanitize_price( $item['price'] );
				$quantity        = (double) absint( $item['quantity'] );
				$item_tax_total  = 0;
				$item_tax_amount = 0;
				$item_sub_total  = ( $price * $quantity );

				$item_discount_total = $item_sub_total;
				// Apply discount to item
				if ( $discount ) {
					$item_discount_total = $item_sub_total - ( $item_sub_total * ( $discount / 100 ) );
				}

				if ( ! empty( $item['tax_id'] ) ) {
					$inclusives = $compounds = $taxes = [];

					foreach ( $item['tax_id'] as $tax_id ) {
						$tax = eaccounting_get_tax( $tax_id );

						switch ( $tax->type ) {
							case 'inclusive':
								$inclusives[] = $tax;
								break;
							case 'compound':
								$compounds[] = $tax;
								break;
							case 'normal':
							default:
								$taxes[] = $tax;

								$item_tax_amount = ( $item_discount_total / 100 ) * $tax->rate;

								$item_tax_total += $item_tax_amount;
								break;
						}
					}

					if ( $inclusives ) {
						$item_sub_and_tax_total = $item_discount_total + $item_tax_total;

						$item_base_rate = $item_sub_and_tax_total / ( 1 + array_sum( wp_list_pluck( $inclusives, 'rate' ) ) / 100 );
						$item_tax_total = $item_sub_and_tax_total - $item_base_rate;

						$item_sub_total = $item_base_rate + $discount;
					}

					if ( $compounds ) {
						foreach ( $compounds as $compound ) {
							$item_tax_total += ( ( $item_discount_total + $item_tax_total ) / 100 ) * $compound->rate;
						}
					}
				}

				$sub_total     += $item_sub_total;
				$tax_total     += $item_tax_total;
				$items[ $key ] = eaccounting_price( $item_sub_total );
			}
		}

		$result['items']    = $items;
		$result['subtotal'] = eaccounting_price( $sub_total );

		// Apply discount to total
		if ( $discount ) {
			$discount_total = $sub_total * ( $discount / 100 );

			$sub_total = $sub_total - ( $sub_total * ( $discount / 100 ) );
		}
		$grand_total              = $sub_total + $tax_total;
		$result['grand_total']    = eaccounting_price( $grand_total );
		$result['shipping']       = eaccounting_price( $shipping );
		$result['discount_total'] = eaccounting_price( $discount_total );
		$result['tax_total']      = eaccounting_price( $tax_total );

		wp_send_json_success( $result );
	}

	/**
	 * @since 1.0.0
	 */
	public function invoice_add_item() {
		$this->verify_nonce( 'invoice_add_item', 'nonce' );
		$this->check_permission();
		$item_row = isset( $_REQUEST['item_row'] ) ? absint( $_REQUEST['item_row'] ) : 0;
		ob_start();
		eaccounting_get_views( 'invoice/line-item.php', array(
			'item_row' => $item_row,
			'line_id'  => null,
			'item_id'  => null,
			'name'     => '',
			'quantity' => 0,
			'price'    => 0,
		) );
		$html = ob_get_contents();
		ob_get_clean();
		$this->send_success( [
			'html' => $html
		] );

	}

	/**
	 * Check permission
	 *
	 * since 1.0.0
	 */
	public function check_permission() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Error: You are not allowed to do this.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Verify nonce request
	 * since 1.0.0
	 *
	 * @param $action
	 */
	public function verify_nonce( $action, $field = '_wpnonce' ) {
		if ( ! isset( $_REQUEST[ $field ] ) || ! wp_verify_nonce( $_REQUEST[ $field ], $action ) ) {
			$this->send_error( __( 'Error: Nonce verification failed', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Wrapper function for sending success response
	 * since 1.0.0
	 *
	 * @param null $data
	 */
	public function send_success( $data = null ) {
		wp_send_json_success( $data );
	}

	/**
	 * Wrapper function for sending error
	 * since 1.0.0
	 *
	 * @param null $data
	 */
	public function send_error( $data = null ) {
		wp_send_json_error( $data );
	}

}

new EAccounting_Ajax();
