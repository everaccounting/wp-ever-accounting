<?php
/**
 * EverAccounting Exception Class.
 *
 * Extends Exception to provide additional data.
 *
 * @package EverAccounting
 * @since   1.0.2
 */

namespace EverAccounting;

use Exception as Ex;

defined( 'ABSPATH' ) || exit;

/**
 * Data exception class.
 */
class Exception extends Ex {

	/**
	 * Sanitized error code.
	 *
	 * @var string
	 */
	protected $error_code;

	/**
	 * Error extra data.
	 *
	 * @var array
	 */
	protected $error_data;

	/**
	 * Setup exception.
	 *
	 * @param string $code Machine-readable error code, e.g `eaccounting_invalid_transaction_id`.
	 * @param string $message User-friendly translated error message, e.g. 'Transaction ID is invalid'.
	 * @param int    $http_status_code Proper HTTP status code to respond with, e.g. 400.
	 * @param array  $data Extra error data.
	 * @since 1.0.2
	 */
	public function __construct( $code, $message, $http_status_code = 400, $data = array() ) {
		$this->error_code = $code;
		$this->error_data = array_merge( array( 'status' => $http_status_code ), $data );

		parent::__construct( $message, $http_status_code );
	}

	/**
	 * Returns the error code.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function getErrorCode() {
		return $this->error_code;
	}

	/**
	 * Returns error data.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function getErrorData() {
		return $this->error_data;
	}
}
