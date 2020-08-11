<?php
/**
 * Importer Batch Precess.
 *
 * @package     EverAccounting
 * @subpackage  Interfaces
 * @since       1.0.2
 */

namespace AffWP\Utils\Batch_Process;
defined( 'ABSPATH' ) || exit();

/**
 * Second-level interface for registering a batch process that leverages
 * pre-fetch and data storage.
 *
 * @since 1.0.2
 *
 * @see   \AffWP\Utils\Data_Storage
 */
interface With_PreFetch extends Base {
    /**
     * Initializes the batch process.
     *
     * This is the point where any relevant data should be initialized for use by the processor methods.
     *
     * @access public
     * @since  1.0.2
     */
    public function init( $data = null );
    
    /**
     * Pre-fetches data to speed up processing.
     *
     * @access public
     * @since  1.0.2
     */
    public function pre_fetch();
    
}
