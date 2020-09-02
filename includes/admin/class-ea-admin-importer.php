<?php

namespace EverAccounting\Admin;


use EverAccounting\Ajax;

class Importer {

	/**
	 * Importer constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_eaccounting_do_ajax_import', array( __CLASS__, 'do_ajax_import' ) );
	}

	public static function do_ajax_import() {
		if ( ! isset( $_REQUEST['type'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Import type must be present.', 'wp-ever-accounting' )
			) );
		}

		$type            = sanitize_key( $_REQUEST['type'] );
		$file            = ! empty( $_REQUEST['file'] ) ? eaccounting_clean( wp_unslash( $_REQUEST['file'] ) ) : '';
		$delimiter       = ! empty( $_REQUEST['delimiter'] ) ? eaccounting_clean( wp_unslash( $_REQUEST['delimiter'] ) ) : ',';
		$position        = isset( $_REQUEST['position'] ) ? absint( $_REQUEST['position'] ) : 0;
		$mapping         = isset( $_REQUEST['mapping'] ) ? (array) eaccounting_clean( wp_unslash( $_REQUEST['mapping'] ) ) : array();
		$update_existing = isset( $_REQUEST['update_existing'] ) ? (bool) $_REQUEST['update_existing'] : false;

		//verify nonce
		Ajax::verify_nonce( "{$type}_importer_nonce" );

		if ( empty( $type ) || false === $batch = eaccounting()->utils->batch->get( $type ) ) {
			wp_send_json_error( array(
				'message' => sprintf( __( '%s is an invalid import type.', 'wp-ever-accounting' ), esc_html( $type ) )
			) );
		}

		$class      = isset( $batch['class'] ) ? $batch['class'] : '';
		$class_file = isset( $batch['file'] ) ? $batch['file'] : '';

		if ( empty( $class_file ) ) {
			wp_send_json_error( array(
				'message' => sprintf( __( 'An invalid file path is registered for the %1$s handler.', 'wp-ever-accounting' ), "<code>{$type}</code>" )
			) );
		}

		require_once $class_file;

		if ( empty( $class ) || ! class_exists( $class ) ) {
			wp_send_json_error( array(
				'error' => sprintf( __( '%1$s is an invalid importer handler for the %2$s . Please try again.', 'wp-ever-accounting' ),
					"<code>{$class}</code>",
					"<code>{$type}</code>"
				)
			) );
		}

		if ( empty( $file ) && empty( $_FILES['upload'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing import file. Please provide an import file.', 'wp-ever-accounting' ), 'request' => $_REQUEST ) );
		}

		if ( ! empty( $_FILES['upload'] ) ) {
			$accepted_mime_types = array(
				'text/csv',
				'text/comma-separated-values',
				'text/plain',
				'text/anytext',
				'text/*',
				'text/plain',
				'text/anytext',
				'text/*',
				'application/csv',
				'application/excel',
				'application/vnd.ms-excel',
				'application/vnd.msexcel',
			);

			if ( empty( $_FILES['upload']['type'] ) || ! in_array( strtolower( $_FILES['upload']['type'] ), $accepted_mime_types ) ) {
				wp_send_json_error( array( 'message' => __( 'The file you uploaded does not appear to be a CSV file.', 'wp-ever-accounting' ), 'request' => $_REQUEST ) );
			}

			if ( ! file_exists( $_FILES['upload']['tmp_name'] ) ) {
				wp_send_json_error( array( 'message' => __( 'Something went wrong during the upload process, please try again.', 'wp-ever-accounting' ), 'error' => $_FILES ) );
			}

			// Let WordPress import the file. We will remove it after import is complete
			$import_file = wp_handle_upload( $_FILES['upload'], array( 'test_form' => false ) );
			if ( ! empty( $import_file['error'] ) ) {
				wp_send_json_error( array( 'message' => __( 'Something went wrong during the upload process, please try again.', 'wp-ever-accounting' ), 'error' => $import_file ) );
			}

			$file = $import_file;
		}

		if ( empty( $file ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing import file. Please provide an import file.', 'wp-ever-accounting' ), 'request' => $_REQUEST ) );
		}

		$importer = new $class( $file['file'] );
		if ( ! $importer->can_import() ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to import data', 'wp-ever-accounting' ) ) );
		}


//		$params = array(
//			'delimiter'       => ! empty( $_POST['delimiter'] ) ? eaccounting_clean( wp_unslash( $_POST['delimiter'] ) ) : ',',
//			'start_pos'       => isset( $_POST['position'] ) ? absint( $_POST['position'] ) : 0,
//			'mapping'         => isset( $_POST['mapping'] ) ? (array) eaccounting_clean( wp_unslash( $_POST['mapping'] ) ) : array(),
//			'update_existing' => isset( $_POST['update_existing'] ) ? (bool) $_POST['update_existing'] : false,
//			'lines'           => apply_filters( 'eaccounting_import_batch_size', 30 ),
//			'parse'           => true,
//		);
//
//		/**
//		 * @var $importer \EverAccounting\Abstracts\CSV_Batch_Importer
//		 */
//		$importer = new $class( $file, $params );
//
//		if ( ! $importer->can_import() ) {
//			wp_send_json_error( array(
//				'message' => __( 'You do not have enough privileges to import this.', 'wp-ever-accounting' )
//			) );
//		}
//
//		// Log failures.
//		if ( 0 !== $params['start_pos'] ) {
//			$error_log = array_filter( (array) get_user_option( "{$type}_import_error_log" ) );
//		} else {
//			$error_log = array();
//		}
//
//		$results          = $importer->import();
//		$percent_complete = $importer->get_percent_complete();
//		$error_log        = array_merge( $error_log, $results['failed'], $results['skipped'] );
//
//		update_user_option( get_current_user_id(), "{$type}_import_error_log", $error_log );
//
//
//		if ( 100 === $percent_complete ) {
//			wp_send_json_success(
//				array(
//					'position'   => 'done',
//					'percentage' => 100,
//					'imported'   => count( $results['imported'] ),
//					'failed'     => count( $results['failed'] ),
//					'updated'    => count( $results['updated'] ),
//					'skipped'    => count( $results['skipped'] ),
//				)
//			);
//		} else {
//			wp_send_json_success(
//				array(
//					'position'   => $importer->get_file_position(),
//					'percentage' => $percent_complete,
//					'imported'   => count( $results['imported'] ),
//					'failed'     => count( $results['failed'] ),
//					'updated'    => count( $results['updated'] ),
//					'skipped'    => count( $results['skipped'] ),
//				)
//			);
//		}


		exit();
	}

	/**
	 * Get customer csv fields.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public static function get_customer_csv_fields() {
		$fields = array(
			'Name'          => 'name',
			'Email'         => 'email',
			'Phone'         => 'phone',
			'Fax'           => 'fax',
			'Birth Date'    => 'birth_date',
			'Address'       => 'address',
			'Country'       => 'country',
			'Website'       => 'website',
			'Tax Number'    => 'tax_number',
			'Currency Code' => 'currency_code',
			'Note'          => 'note',
		);

		return apply_filters( 'eaccounting_customer_csv_fields', $fields );
	}
}

return new Importer();
