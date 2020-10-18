<?php
/**
 * Handle csv writer
 *
 * @package     EverAccounting
 * @since       1.0.2
 *
 */
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
     * @param array  $data
     * @param string $deliminator
     *
     * @since     1.0.2
     */
    function __construct( $data, $deliminator = "," ) {
        
        $this->data        = $data;
        $this->deliminator = $deliminator;
    }
    
    /**
     * @param $data
     *
     * @return string
     * @since 1.0.2
     */
    private static function wrap_with_quotes( $data ) {
        $data = preg_replace( '/"(.+)"/', '""$1""', $data );
        
        return sprintf( '"%s"', $data );
    }
    
    /**
     * Echos the escaped CSV file with chosen delimeter
     *
     * @return void
     * @since     1.0.2
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
     * @since     1.0.2
     */
    public function headers( $name ) {
        header( 'Content-Type: application/csv' );
        header( "Content-disposition: attachment; filename={$name}.csv" );
    }
}
