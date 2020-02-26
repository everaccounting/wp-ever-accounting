<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_CSV_Writer {
	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @var string
	 */
	protected $deliminator;

	/**
	 * Loads data and optionally a deliminator. Data is assumed to be an array
	 * of associative arrays.
	 *
	 * @param array $data
	 * @param string $deliminator
	 */
	function __construct( $data, $deliminator = "," ) {

		$this->data        = $data;
		$this->deliminator = $deliminator;
	}

	/**
	 * @since 1.0.1
	 * @param $data
	 *
	 * @return string
	 */
	private static function wrap_with_quotes( $data ) {
		$data = preg_replace( '/"(.+)"/', '""$1""', $data );

		return sprintf( '"%s"', $data );
	}

	/**
	 * Echos the escaped CSV file with chosen delimeter
	 *
	 * @return void
	 */
	public function output() {
		foreach ( $this->data as $row ) {
			$quoted_data = array_map( array( __CLASS__, 'wrap_with_quotes' ), $row );
			echo sprintf( "%s\n", implode( $this->deliminator, $quoted_data ) );
		}
	}

	/**
	 * Sets proper Content-Type header and attachment for the CSV outpu
	 *
	 * @param string $name
	 *
	 * @return void
	 */
	public function headers( $name ) {
		header( 'Content-Type: application/csv' );
		header( "Content-disposition: attachment; filename={$name}.csv" );
	}
}
