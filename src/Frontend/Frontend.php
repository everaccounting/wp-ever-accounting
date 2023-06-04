<?php

namespace EverAccounting\Frontend;

use EverAccounting\Rewrites;
use EverAccounting\Singleton;

defined( 'ABSPATH' ) || exit();


/**
 * Frontend Handlers.
 *
 * @since       1.1.6
 * @package     EverAccounting
 * @class       Frontend
 */
class Frontend extends Singleton {

	/**
	 * Frontend constructor.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	protected function __construct() {
		//add_action( 'ever_accounting_endpoint_head', array( __CLASS__, 'output_head' ) );
	}

	/**
	 * Output head.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function output_head() {
		$title = Rewrites::instantiate()->get_endpoint_title();
		echo sprintf( '<title>%s</title>', esc_html( $title ) );
		// enqueue styles and scripts.
		ever_accounting()->enqueue_style( 'eac-frontend', 'css/frontend.min.css' );
	}
}
