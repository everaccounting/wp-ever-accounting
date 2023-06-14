<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Rewrites.
 *
 * @since 1.1.6
 * @package EverAccounting
 * @class Rewrites
 */
class Rewrites extends Singleton {

	/**
	 * Endpoints constructor.
	 *
	 * @since 1.1.6
	 */
	protected function __construct() {
		add_action( 'init', array( $this, 'register_endpoints' ), 0 );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 99 );
		add_filter( 'template_include', array( $this, 'handle_request' ), 99 );
	}

	/**
	 * Register endpoints.
	 *
	 * @since 1.1.6
	 */
	public function register_endpoints() {
		$base = $this->get_endpoint_base();
		// Invoice endpoints.
		add_rewrite_rule( "^{$base}/invoice/([^/]+)/?$", 'index.php?uuid=$matches[1]&eac_page=invoice', 'top' );
		// Payment endpoints.
		add_rewrite_rule( "^{$base}/payment/([^/]+)/?$", 'index.php?uuid=$matches[1]&eac_page=payment', 'top' );
	}

	/**
	 * Add query vars.
	 *
	 * @param array $vars Query vars.
	 *
	 * @since 1.1.6
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'uuid';
		$vars[] = 'eac_page';

		return $vars;
	}

	/**
	 * Handle request.
	 *
	 * @param string $template Template.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function handle_request( $template ) {
		$page = get_query_var( 'eac_page' );
		$uuid = get_query_var( 'uuid' );

		// If the page and uuid are not set, bail.
		if ( empty( $page ) || empty( $uuid ) ) {
			return $template;
		}

		switch ( $page ) {
			case 'invoice':
				$document = eac_get_invoice( [ 'uuid' => $uuid ] );
				$template = ever_accounting()->get_template_path() . 'invoice.php';
				break;
			case 'payment':
				$template = ever_accounting()->get_template_path() . 'payment.php';
				break;
		}

		return $template;
	}

	/**
	 * Get endpoint base.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_endpoint_base() {
		return apply_filters( 'ever_accounting_endpoints_base', 'eac' );
	}
}
