<?php
/**
 * CSV Importer Interface.
 *
 * @package     EverAccounting
 * @subpackage  Interfaces
 * @since       1.0.2
 */

namespace EverAccounting\Interfaces;


// Exit if accessed directly
defined( 'ABSPATH' ) || exit();

/**
 * Promise for structuring CSV importers.
 *
 * @since       1.0.2
 *
 * @see         \AffWP\Utils\Importer\Base
 */
interface CSV_Importer extends Importer {

    /**
     * Maps CSV columns to their corresponding import fields.
     *
     * @access      public
     *
     * @param array $import_fields Import fields to map.
     *
     * @since       1.0.2
     *
     */
    public function map_fields( $import_fields = array() );

    /**
     * Retrieves the CSV columns.
     *
     * @access      public
     * @return array The columns in the CSV.
     * @since       1.0.2
     *
     */
    public function get_columns();

    /**
     * Maps a single CSV row to the data passed in via init().
     *
     * @access      public
     *
     * @param array $csv_row CSV row data.
     *
     * @return array CSV row data mapped to form-defined arguments.
     * @since       1.0.2
     *
     */
    public function map_row( $csv_row );

    /**
     * Retrieves the first row of the CSV.
     *
     * This is used for showing an example of what the import will look like.
     *
     * @access      public
     * @return array The first row after the header of the CSV.
     * @since       1.0.2
     *
     */
    public function get_first_row();
}
