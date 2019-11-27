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
