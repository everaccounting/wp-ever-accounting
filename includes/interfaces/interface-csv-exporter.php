<?php
/**
 * CSV Exporter Interface.
 *
 * @package     EverAccounting
 * @subpackage  Interfaces
 * @since       1.0.2
 */

namespace EverAccounting\Interfaces;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit();

/**
 * Promise for structuring CSV exporters.
 *
 * @since       1.0.2
 */
interface CSV_Exporter extends Exporter {
    
    /**
     * Sets the CSV columns.
     *
     * @access      public
     * @return array<string,string> CSV columns.
     * @since       1.0.2
     *
     */
    public function csv_cols();
    
    /**
     * Retrieves the CSV columns array.
     *
     * Alias for csv_cols(), usually used to implement a filter on the return.
     *
     * @access      public
     * @return array<string,string> CSV columns.
     * @since       1.0.2
     *
     */
    public function get_csv_cols();
    
    /**
     * Outputs the CSV columns.
     *
     * @access      public
     * @return void
     * @since       1.0.2
     *
     */
    public function csv_cols_out();
    
    /**
     * Outputs the CSV rows.
     *
     * @access      public
     * @return void
     * @since       1.0.2
     *
     */
    public function csv_rows_out();
    
}
